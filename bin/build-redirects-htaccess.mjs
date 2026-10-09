// Builds the server-level redirect block for .htaccess from docs/redirects.csv, so the old URLs are answered by Apache
// (one 301 straight to the final https address, also from http:// and www.) before PHP starts. The theme keeps the same
// redirects as a fallback (inc/redirects.php). Usage: node bin/build-redirects-htaccess.mjs [--base https://chargenet.energy]
// Paste the output into the site's .htaccess ABOVE the "BEGIN ChargeNet performance" block (docs/launch-runbook.md).
// Test the rules on a real Apache with bin/test-redirect-rules.sh.
import { readFileSync } from 'node:fs';

const args = process.argv.slice(2);
const base = (
	args.includes('--base') ? args[args.indexOf('--base') + 1] : 'https://chargenet.energy'
).replace(/\/$/, '');
const { hostname: host, protocol } = new URL(base);
const bareHost = host.replace(/^www\./, '');

function parseCsv(text) {
	const rows = [];
	let row = [];
	let field = '';
	let quoted = false;
	for (let i = 0; i < text.length; i++) {
		const c = text[i];
		if (quoted) {
			if (c === '"' && text[i + 1] === '"') ((field += '"'), i++);
			else if (c === '"') quoted = false;
			else field += c;
		} else if (c === '"') quoted = true;
		else if (c === ',') (row.push(field), (field = ''));
		else if (c === '\n') (row.push(field), rows.push(row), (row = []), (field = ''));
		else field += c;
	}
	if (field || row.length) (row.push(field), rows.push(row));
	return rows;
}

const escapeRe = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
const rows = parseCsv(
	readFileSync(new URL('../docs/redirects.csv', import.meta.url), 'utf8'),
).slice(1);
const out = ['# BEGIN ChargeNet redirects', '<IfModule mod_rewrite.c>', '\tRewriteEngine On', ''];
const seen = new Set();

for (const [oldPath, newPath, status] of rows) {
	const key = oldPath.toLowerCase().replace(/(.)\/$/, '$1');
	if (!oldPath || seen.has(key)) continue;
	seen.add(key);
	if (oldPath === '/') {
		out.push(`\tRewriteRule ^$ ${base}${newPath} [R=${status},L]`);
		continue;
	}
	const trimmed = oldPath.replace(/^\/|\/$/g, '');
	if (oldPath.includes('{code}')) {
		const pattern = `^${escapeRe(trimmed).replace('\\{code\\}', '([A-Za-z0-9_-]+)')}/?$`;
		out.push(
			`\tRewriteRule ${pattern} ${base}${newPath.replace('{code}', '$1')} [R=${status},L,NE,NC]`,
		);
		continue;
	}
	const pattern = `^${escapeRe(trimmed)}/?$`;
	if (oldPath === '/rapport2027') {
		// The bare address gets the campaign parameters; one with its own query string keeps it.
		out.push('\tRewriteCond %{QUERY_STRING} ^$');
		out.push(`\tRewriteRule ${pattern} ${base}${newPath} [R=${status},L,NE,NC]`);
		out.push(`\tRewriteRule ${pattern} ${base}${newPath.split('?')[0]} [R=${status},L,NC]`);
		continue;
	}
	if (/^\/rapport2027\/?$/.test(oldPath)) continue; // Covered by the rule above (it accepts a trailing slash).
	out.push(`\tRewriteRule ${pattern} ${base}${newPath} [R=${status},L,NE,NC]`);
}

out.push('', '\t# Everything else: one hop to the canonical address (https, without www).');
if (protocol === 'https:') {
	out.push('\tRewriteCond %{HTTPS} !=on [OR]');
	out.push(`\tRewriteCond %{HTTP_HOST} ^www\\.${escapeRe(bareHost)}$ [NC]`);
	out.push(`\tRewriteRule ^(.*)$ ${base}/$1 [R=301,L,NE]`);
} else {
	out.push(`\tRewriteCond %{HTTP_HOST} ^www\\.${escapeRe(bareHost)}$ [NC]`);
	out.push(`\tRewriteRule ^(.*)$ ${base}/$1 [R=301,L,NE]`);
}
out.push('</IfModule>', '# END ChargeNet redirects', '');
process.stdout.write(out.join('\n'));
