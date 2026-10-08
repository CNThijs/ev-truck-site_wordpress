// Security baseline over HTTP, for any environment: security headers, closed endpoints, exposed files, login errors.
// Usage: node bin/check-security.mjs [baseUrl] [--production]
// --production: the CSP must be enforcing (not report-only) and HSTS is required. Exit code 1 on any failure.
// Server-side checks (salts, roles, permissions, 2FA): bin/check-hardening.php. Docs: docs/security.md.
const args = process.argv.slice(2);
const base = (args.find((a) => /^https?:/.test(a)) ?? 'http://chargenet.ddev.site').replace(
	/\/$/,
	'',
);
const production = args.includes('--production');
const https = base.startsWith('https:');
const local = /\.ddev\.site$|^localhost/.test(new URL(base).hostname);

const results = [];
const check = (ok, name, detail = '', level = 'fail') => results.push({ ok, name, detail, level });
const get = (path, init = {}) => fetch(base + path, { redirect: 'manual', ...init });

// --- Headers -------------------------------------------------------------------------------------------------------
for (const path of ['/en/', '/nl/']) {
	const res = await get(path);
	const h = (name) => res.headers.get(name) ?? '';
	const csp = h('content-security-policy');
	const cspReport = h('content-security-policy-report-only');
	check(
		h('x-content-type-options').toLowerCase() === 'nosniff',
		`${path} X-Content-Type-Options: nosniff`,
		h('x-content-type-options'),
	);
	check(
		/sameorigin|deny/i.test(h('x-frame-options')) || /frame-ancestors/.test(csp + cspReport),
		`${path} frame protection`,
		h('x-frame-options'),
	);
	check(
		/strict-origin|no-referrer|same-origin/.test(h('referrer-policy')),
		`${path} Referrer-Policy`,
		h('referrer-policy'),
	);
	check(h('permissions-policy') !== '', `${path} Permissions-Policy`);
	check(h('cross-origin-opener-policy') !== '', `${path} Cross-Origin-Opener-Policy`);
	check(
		production ? csp !== '' : csp + cspReport !== '',
		`${path} Content-Security-Policy${production ? ' (enforcing)' : ''}`,
		csp ? 'enforcing' : cspReport ? 'report-only' : 'missing',
	);
	const policy = csp || cspReport;
	check(
		/default-src 'self'/.test(policy) &&
			/object-src 'none'/.test(policy) &&
			/base-uri 'self'/.test(policy) &&
			/frame-ancestors/.test(policy),
		`${path} CSP has default-src, object-src, base-uri, frame-ancestors`,
	);
	check(
		!/script-src[^;]*\*(?!\.)/.test(policy) && !/script-src[^;]*\s(https?:)(\s|;)/.test(policy),
		`${path} CSP script-src has no wildcard`,
	);
	if (https) {
		check(
			/max-age=\d{7,}/.test(h('strict-transport-security')),
			`${path} Strict-Transport-Security`,
			h('strict-transport-security'),
		);
	} else {
		check(
			!production,
			`${path} Strict-Transport-Security (needs https)`,
			'checked only on https',
			production ? 'fail' : 'skip',
		);
	}
	check(
		!/\d/.test(h('x-powered-by')),
		`${path} no version in X-Powered-By`,
		h('x-powered-by'),
		'warn',
	);
	check(!h('x-pingback'), `${path} no X-Pingback header`, h('x-pingback'));
}

// --- Redirect to https ---------------------------------------------------------------------------------------------
if (https) {
	const res = await fetch(base.replace('https:', 'http:') + '/en/', { redirect: 'manual' });
	check(
		[301, 308].includes(res.status) && (res.headers.get('location') ?? '').startsWith('https:'),
		'http:// redirects to https://',
		`${res.status}`,
	);
}

// --- Closed endpoints ----------------------------------------------------------------------------------------------
const xmlrpc = await get('/xmlrpc.php', {
	method: 'POST',
	body: '<?xml version="1.0"?><methodCall><methodName>system.listMethods</methodName></methodCall>',
});
const xmlrpcBody = await xmlrpc.text();
check(
	!/<string>wp\.|<string>system\.|methodResponse>\s*<params>/.test(xmlrpcBody),
	'XML-RPC lists no methods',
	`${xmlrpc.status}`,
);

const users = await get('/wp-json/wp/v2/users');
check([401, 403, 404].includes(users.status), 'REST user list is not public', `${users.status}`);

const author = await get('/en/?author=1');
check(
	!/\/author\//.test(author.headers.get('location') ?? ''),
	'?author=1 does not reveal a login name',
	author.headers.get('location') ?? `${author.status}`,
);

const login = await get('/wp-login.php', {
	method: 'POST',
	headers: {
		'Content-Type': 'application/x-www-form-urlencoded',
		Cookie: 'wordpress_test_cookie=WP%20Cookie%20check',
	},
	body: `log=cn-check-${Date.now()}&pwd=wrong&testcookie=1`,
});
const loginBody = await login.text();
check(
	!/not registered|Unknown username|ongeldige gebruikersnaam|incorrect for the username/i.test(
		loginBody,
	),
	'login error does not reveal whether the user exists',
);

// --- Exposed files -------------------------------------------------------------------------------------------------
const exposed = [
	['/wp-config.php', /DB_PASSWORD|DB_NAME/],
	['/wp-config.php.bak', /./],
	['/wp-config.php~', /./],
	['/.env', /./],
	['/.git/HEAD', /ref:/],
	['/composer.json', /"require"/],
	['/wp-content/debug.log', /./],
	['/wp-content/uploads/', /Index of/i],
	['/wp-content/plugins/', /Index of/i],
	['/wp-content/themes/', /Index of/i],
	['/wp-includes/', /Index of/i],
];
for (const [path, marker] of exposed) {
	const res = await get(path);
	const body = res.status === 200 ? await res.text() : '';
	check(res.status !== 200 || !marker.test(body), `${path} is not exposed`, `${res.status}`);
}
for (const path of ['/readme.html', '/license.txt']) {
	const res = await get(path);
	check(
		res.status !== 200,
		`${path} is not served (reveals the WordPress version)`,
		`${res.status}`,
		local ? 'warn' : 'fail',
	);
}

// --- Contact point -------------------------------------------------------------------------------------------------
const sec = await get('/.well-known/security.txt');
const secBody = sec.status === 200 ? await sec.text() : '';
check(
	/^Contact: mailto:security@chargenet\.energy/m.test(secBody),
	'/.well-known/security.txt names security@chargenet.energy',
	`${sec.status}`,
	local ? 'warn' : 'fail', // DDEV's nginx blocks dot-folders; Apache on the host does not.
);

// --- Report --------------------------------------------------------------------------------------------------------
let failed = 0;
for (const r of results) {
	const tag = r.ok ? 'ok  ' : r.level === 'warn' ? 'warn' : r.level === 'skip' ? 'skip' : 'FAIL';
	if (!r.ok && r.level === 'fail') failed++;
	if (!r.ok || args.includes('--verbose'))
		console.log(`${tag} ${r.name}${r.detail ? `  [${r.detail}]` : ''}`);
}
console.log(
	`\n${results.length} checks against ${base}${production ? ' (production rules)' : ''}: ${failed} failed, ${results.filter((r) => !r.ok && r.level === 'warn').length} warnings.`,
);
process.exit(failed ? 1 : 0);
