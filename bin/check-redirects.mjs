// Checks docs/redirects.csv against a running site: every old path answers with the listed status and a single hop to
// the listed new path (a query string such as campaign parameters may be added), and the target answers 200.
// Usage: node bin/check-redirects.mjs [baseUrl]. Rows marked PROVISIONAL are reported but do not fail the check.
import { readFileSync } from 'node:fs';

const base = (process.argv[2] ?? 'http://chargenet.ddev.site').replace(/\/$/, '');
const rows = readFileSync(new URL('../docs/redirects.csv', import.meta.url), 'utf8')
	.trim()
	.split('\n')
	.slice(1)
	.map((line) => {
		const [oldPath, newPath, status, ...note] = line.split(',');
		return { oldPath, newPath, status: Number(status), note: note.join(',') };
	});

let failed = 0;
let provisional = 0;
for (const row of rows) {
	const res = await fetch(base + row.oldPath, { redirect: 'manual' });
	const location = res.headers.get('location') ?? '';
	const target = location ? new URL(location, base) : null;
	const problems = [];
	if (res.status !== row.status) problems.push(`status ${res.status}, expected ${row.status}`);
	else if (target && target.pathname !== row.newPath)
		problems.push(`goes to ${target.pathname}, expected ${row.newPath}`);
	if (!problems.length && target) {
		const end = await fetch(target.href, { redirect: 'manual' });
		if (end.status !== 200) problems.push(`target answers ${end.status}`);
	}
	if (problems.length) {
		const soft = row.note.startsWith('PROVISIONAL');
		console.log(`${soft ? 'provisional' : 'FAIL       '} ${row.oldPath}: ${problems.join('; ')}`);
		soft ? provisional++ : failed++;
	}
}
console.log(
	`${rows.length} redirects checked, ${failed} failed, ${provisional} provisional rows not met.`,
);
process.exit(failed ? 1 : 0);
