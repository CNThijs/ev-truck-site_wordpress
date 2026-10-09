// Checks docs/redirects.csv against a running site: every old URL answers with exactly one redirect (the listed status,
// 301) to the listed final URL, and that final URL answers 200 itself (no second hop). When the listed target has a query
// string it must match exactly (campaign parameters); otherwise only the path is compared. `{code}` in a row stands for
// a campaign code and is tested with a sample. On a production address (not .ddev.site / localhost) it also requests
// three rows through http:// and www. and expects the same single hop to https://<host>/.
// Usage: node bin/check-redirects.mjs [baseUrl] [--no-target]
//   --no-target  do not request the final URLs (for testing the server rules without WordPress behind them)
import { readFileSync } from 'node:fs';

const args = process.argv.slice(2);
const base = (args.find((a) => /^https?:/.test(a)) ?? 'http://chargenet.ddev.site').replace(
	/\/$/,
	'',
);
const skipTarget = args.includes('--no-target');
const local = /\.ddev\.site$|^localhost|\.test$/.test(new URL(base).hostname);
const SAMPLE_CODE = 'AB12cd';

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

const rows = parseCsv(readFileSync(new URL('../docs/redirects.csv', import.meta.url), 'utf8'))
	.slice(1)
	.map(([oldPath, newPath, status, note]) => ({
		oldPath,
		newPath,
		status: Number(status),
		note: note ?? '',
	}));

let failed = 0;
const fail = (what, problems) => {
	console.log(`FAIL ${what}: ${problems.join('; ')}`);
	failed++;
};

async function checkOne(origin, row, label = row.oldPath) {
	const oldPath = row.oldPath.replace('{code}', SAMPLE_CODE);
	const wanted = row.newPath.replace('{code}', SAMPLE_CODE);
	const res = await fetch(origin + oldPath, { redirect: 'manual' });
	const location = res.headers.get('location') ?? '';
	const target = location ? new URL(location, origin) : null;
	const problems = [];
	const expectedStatus = row.oldPath === '/' && local && res.status === 302 ? 302 : row.status;
	if (res.status !== expectedStatus) problems.push(`status ${res.status}, expected ${row.status}`);
	else if (target) {
		const [wantedPath, wantedQuery] = wanted.split('?');
		if (target.pathname !== wantedPath)
			problems.push(`goes to ${target.pathname}, expected ${wantedPath}`);
		else if (wantedQuery !== undefined && target.search !== `?${wantedQuery}`)
			problems.push(`query is ${target.search}, expected ?${wantedQuery}`);
		if (!problems.length && origin === base && !skipTarget) {
			const end = await fetch(target.href, { redirect: 'manual' });
			if (end.status !== 200)
				problems.push(`final URL answers ${end.status} (a second hop or an error)`);
		}
		if (!problems.length && origin !== base && target.origin !== base)
			problems.push(`goes to ${target.origin}, expected ${base}`);
	}
	if (problems.length) fail(label, problems);
}

for (const row of rows) await checkOne(base, row);

let variants = 0;
if (!local && base.startsWith('https:')) {
	const host = new URL(base).hostname.replace(/^www\./, '');
	for (const row of rows.filter((r) =>
		['/about', '/rapport2027/{code}', '/blog'].includes(r.oldPath),
	)) {
		for (const origin of [`http://${host}`, `http://www.${host}`, `https://www.${host}`]) {
			variants++;
			await checkOne(origin, row, `${origin}${row.oldPath}`);
		}
	}
}

console.log(
	`${rows.length} redirects${variants ? ` and ${variants} http/www variants` : ''} checked, ${failed} failed.`,
);
process.exit(failed ? 1 : 0);
