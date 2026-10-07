// Usage: npm run check:blog-urls [base-url]
// Checks that every news post of the old site (docs/audit/blog/index.json) still works: /blog/<slug> (the old URL) must
// answer with one 301 to the English post, which must answer 200; the Dutch post must answer 200; and the post
// must carry a canonical, Open Graph and structured data. Needs `ddev start`.
import { readFileSync } from 'node:fs';

process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'; // DDEV's self-signed certificate.
const base = process.argv[2] || 'https://chargenet.ddev.site';
const posts = JSON.parse(
	readFileSync(new URL('../docs/audit/blog/index.json', import.meta.url), 'utf8'),
);
let failures = 0;
const fail = (message) => {
	failures += 1;
	console.error(`FAIL: ${message}`);
};

for (const { slug } of posts) {
	const old = await fetch(`${base}/blog/${slug}`, { redirect: 'manual' });
	const target = old.headers.get('location') || '';
	if (old.status !== 301 || !target.includes(`/en/blog/${slug}/`)) {
		fail(`/blog/${slug}: expected one 301 to /en/blog/${slug}/, got ${old.status} ${target}`);
		continue;
	}
	const page = await fetch(target, { redirect: 'manual' });
	const html = await page.text();
	if (page.status !== 200) fail(`${target}: ${page.status}`);
	if (!html.includes('rel="canonical"')) fail(`${target}: no canonical`);
	if (!html.includes('property="og:title"')) fail(`${target}: no Open Graph title`);
	if (!html.includes('"@type":"BlogPosting"')) fail(`${target}: no BlogPosting data`);
	if (!/<h1\b/.test(html)) fail(`${target}: no h1`);
	const nl = await fetch(`${base}/nl/blog/${slug}-nl/`, { redirect: 'manual' });
	if (nl.status !== 200) fail(`/nl/blog/${slug}-nl/: ${nl.status}`);
}
console.log(`${posts.length} posts checked, ${failures} problems.`);
process.exit(failures ? 1 : 0);
