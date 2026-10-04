// Checks hreflang on a running site: every alternate must link back (reciprocal), each page lists itself,
// x-default is the default-language URL, and <html lang> matches. Usage: node bin/check-hreflang.mjs [baseUrl]
// Start URLs: language homes plus every URL in the core sitemaps. Needs `ddev start`.
const base = (process.argv[2] ?? 'http://chargenet.ddev.site').replace(/\/$/, '');
const defaultLang = 'en';

const get = async (url) => (await fetch(url, { redirect: 'follow' })).text();
const norm = (url) => url.replace(/#.*$/, '');

async function sitemapUrls() {
	const urls = [];
	try {
		const index = await get(`${base}/wp-sitemap.xml`);
		for (const [, sitemap] of index.matchAll(/<loc>([^<]+)<\/loc>/g)) {
			const xml = await get(sitemap);
			for (const [, loc] of xml.matchAll(/<loc>([^<]+)<\/loc>/g)) {
				urls.push(loc);
			}
		}
	} catch {
		// No sitemap: start URLs only.
	}
	return urls;
}

function parse(html) {
	const alternates = {};
	for (const [tag] of html.matchAll(/<link[^>]+rel="alternate"[^>]*>/g)) {
		const hreflang = tag.match(/hreflang="([^"]+)"/)?.[1];
		const href = tag.match(/href="([^"]+)"/)?.[1];
		if (hreflang && href) {
			alternates[hreflang] = norm(href);
		}
	}
	return { alternates, lang: html.match(/<html[^>]*\slang="([^"]+)"/)?.[1] };
}

const queue = new Set([`${base}/en/`, `${base}/nl/`, ...(await sitemapUrls())].map(norm));
const pages = new Map();
for (const url of queue) {
	pages.set(url, parse(await get(url)));
	for (const alt of Object.values(pages.get(url).alternates)) {
		queue.add(alt);
	}
}

const errors = [];
for (const [url, { alternates, lang }] of pages) {
	const entries = Object.entries(alternates).filter(([code]) => code !== 'x-default');
	if (entries.length === 0) {
		continue; // untranslated page: no hreflang expected
	}
	if (!entries.some(([, href]) => href === url)) {
		errors.push(`${url}: no self-referencing hreflang`);
	}
	if (!entries.some(([code]) => lang?.startsWith(code))) {
		errors.push(`${url}: <html lang="${lang}"> has no matching hreflang`);
	}
	if (alternates['x-default'] !== alternates[defaultLang]) {
		errors.push(
			`${url}: x-default (${alternates['x-default']}) is not the ${defaultLang} URL (${alternates[defaultLang]})`,
		);
	}
	for (const [code, href] of entries) {
		const back = pages.get(href)?.alternates ?? {};
		if (!Object.values(back).includes(url)) {
			errors.push(`${url}: ${code} alternate ${href} does not link back`);
		}
	}
}

console.log(`${pages.size} pages checked.`);
if (process.env.VERBOSE) {
	console.log(JSON.stringify([...pages], null, 2));
}
if (errors.length > 0) {
	console.error(errors.join('\n'));
	process.exit(1);
}
console.log('hreflang OK: reciprocal, self-referencing, x-default is the default language.');
