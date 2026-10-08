// SEO regression check on a running site. Usage: node bin/check-seo.mjs [baseUrl] [--production | --staging]
// Crawls the language homes, every URL in the Rank Math sitemaps and the internal links found on them, and reports:
// missing or duplicate titles and descriptions, missing, duplicate or wrong canonicals, duplicate meta tags, h1 count and
// heading order, missing Open Graph/Twitter tags, structured data, broken internal links, vague link text, images
// without alt attribute. Indexing: --production expects every sitemap page to be indexable; --staging expects noindex
// everywhere (the environment flag, see docs/seo.md). Without a flag the indexing check is skipped (local is noindex).
// Hreflang reciprocity: bin/check-hreflang.mjs. Exit code 1 when there are errors.
const args = process.argv.slice(2);
const mode = args.includes('--production')
	? 'production'
	: args.includes('--staging')
		? 'staging'
		: 'local';
const base = (args.find((a) => !a.startsWith('--')) ?? 'http://chargenet.ddev.site').replace(
	/\/$/,
	'',
);
const origin = new URL(base).origin;

const errors = [];
const warnings = [];
const error = (url, msg) => errors.push(`${url}  ${msg}`);
const warn = (url, msg) => warnings.push(`${url}  ${msg}`);

const fetchText = async (url) => {
	const res = await fetch(url, { redirect: 'follow' });
	return { status: res.status, url: res.url, body: await res.text() };
};

async function sitemapUrls() {
	const urls = [];
	const index = await fetchText(`${base}/sitemap_index.xml`);
	if (index.status !== 200) {
		error('/sitemap_index.xml', `status ${index.status}`);
		return urls;
	}
	for (const [, sitemap] of index.body.matchAll(/<loc>([^<]+)<\/loc>/g)) {
		const file = sitemap.replace(/^https?:\/\/[^/]+/, origin);
		const xml = await fetchText(file);
		const lang = file.match(/sitemap-([a-z]+)\.xml$/)?.[1];
		for (const [, entry] of xml.body.matchAll(/<url>([\s\S]*?)<\/url>/g)) {
			const loc = entry.match(/<loc>([^<]+)<\/loc>/)[1].replace(/^https?:\/\/[^/]+/, origin);
			const path = new URL(loc).pathname;
			if (lang && !path.startsWith(`/${lang}/`))
				error(path, `in the ${lang} sitemap but not a /${lang}/ URL`);
			if (!/<lastmod>/.test(entry)) error(path, 'sitemap entry without lastmod');
			if (urls.includes(loc)) error(path, 'listed twice in the sitemaps');
			urls.push(loc);
		}
	}
	return urls;
}

const attr = (tag, name) => tag.match(new RegExp(`${name}="([^"]*)"`))?.[1];
const metaTags = (html, key, value) =>
	[...html.matchAll(/<meta\s[^>]*>/g)].map((m) => m[0]).filter((t) => attr(t, key) === value);
const strip = (s) =>
	s
		.replace(/<[^>]+>/g, ' ')
		.replace(/\s+/g, ' ')
		.trim();
const vague = /^(click here|here|read more|more|lees meer|klik hier|hier|meer)$/i;

function inspect(url, html, path) {
	const titles = [...html.matchAll(/<title>([^<]*)<\/title>/g)];
	const title = titles[0]?.[1].trim() ?? '';
	if (titles.length !== 1) error(path, `${titles.length} title tags`);
	const descs = metaTags(html, 'name', 'description');
	if (descs.length !== 1) (descs.length ? error : warn)(path, `${descs.length} meta descriptions`);
	const description = descs[0] ? attr(descs[0], 'content') : '';

	const canonicals = [...html.matchAll(/<link rel="canonical"[^>]*>/g)].map((m) =>
		attr(m[0], 'href'),
	);
	const robots = metaTags(html, 'name', 'robots')
		.map((t) => attr(t, 'content'))
		.join(' ');
	const noindex = /noindex/.test(robots);
	if (canonicals.length > 1) error(path, 'more than one canonical');
	if (canonicals.length === 0 && !noindex) error(path, 'missing canonical');
	const canonical = canonicals[0]?.replace(/^https?:\/\/[^/]+/, origin);
	if (canonical && !noindex && canonical !== url) error(path, `canonical is ${canonical}`);

	for (const [key, value] of [
		['property', 'og:title'],
		['property', 'og:description'],
		['property', 'og:image'],
		['property', 'og:url'],
		['name', 'twitter:card'],
	]) {
		const n = metaTags(html, key, value).length;
		if (n === 0 && !noindex) warn(path, `missing ${value}`);
		if (n > 1) error(path, `${n} ${value} tags`);
	}

	const h1 = [...html.matchAll(/<h1[\s>]/g)].length;
	if (h1 !== 1) error(path, `${h1} h1 elements`);
	let last = 0;
	for (const [, level] of html.matchAll(/<h([1-6])[\s>]/g)) {
		if (last && Number(level) > last + 1)
			warn(path, `heading level jumps from h${last} to h${level}`);
		last = Number(level);
	}

	for (const [, text] of html.matchAll(/<a\s[^>]*href="[^"]*"[^>]*>([\s\S]*?)<\/a>/g)) {
		if (vague.test(strip(text))) warn(path, `link text "${strip(text)}"`);
	}
	for (const [tag] of html.matchAll(/<img\s[^>]*>/g)) {
		if (!/\salt=/.test(tag)) error(path, `image without alt attribute: ${attr(tag, 'src')}`);
	}

	const graph = [];
	for (const [, json] of html.matchAll(
		/<script type="application\/ld\+json"[^>]*>([\s\S]*?)<\/script>/g,
	)) {
		try {
			const data = JSON.parse(json);
			graph.push(...(data['@graph'] ?? [data]));
		} catch {
			error(path, 'invalid JSON-LD');
		}
	}
	const types = graph.flatMap((e) => [].concat(e['@type']));
	const need = ['Organization', 'WebSite', 'WebPage'];
	if (
		!/^\/(en|nl)\/$/.test(path) &&
		!types.includes('CollectionPage') &&
		!types.includes('SearchResultsPage')
	)
		need.push('BreadcrumbList');
	if (/\/blog\/[^/]+\/$/.test(path) && !/\/(category|page)\//.test(path)) need.push('BlogPosting');
	if (!noindex || mode === 'production') {
		for (const t of need)
			if (!types.includes(t) && !(t === 'WebPage' && types.some((x) => /Page$/.test(x))))
				error(path, `structured data lacks ${t}`);
	}
	for (const e of graph) {
		if (e['@type'] === 'BlogPosting')
			for (const f of ['headline', 'datePublished', 'author', 'image'])
				if (!e[f]) error(path, `BlogPosting lacks ${f}`);
		if (e['@type'] === 'Organization')
			for (const f of ['name', 'url', 'logo', 'sameAs'])
				if (!e[f]) error(path, `Organization lacks ${f}`);
	}

	if (mode === 'production' && noindex && !/\/page\/\d+\/$/.test(path))
		error(path, `noindex in production (${robots})`);
	if (mode === 'staging' && !noindex) error(path, 'indexable on staging');
	return {
		title,
		description,
		noindex,
		links: [...html.matchAll(/<a\s[^>]*href="([^"#]+)[^"]*"/g)].map((m) => m[1]),
	};
}

const seen = new Map();
const queue = [`${base}/en/`, `${base}/nl/`, ...(await sitemapUrls())];
const checkedLinks = new Map();
while (queue.length && seen.size < 400) {
	const url = queue.shift();
	if (seen.has(url)) continue;
	const res = await fetchText(url);
	const path = new URL(url).pathname;
	if (res.status !== 200) {
		error(path, `status ${res.status}`);
		seen.set(url, null);
		continue;
	}
	const page = inspect(url, res.body, path);
	seen.set(url, page);
	for (const href of page.links) {
		let target;
		try {
			target = new URL(href, url);
		} catch {
			continue;
		}
		if (
			target.origin !== origin ||
			/\.(pdf|zip|jpe?g|png|svg|webp|xml|css|js|woff2?)$/i.test(target.pathname) ||
			/wp-(json|admin|login)|\/feed\/?$|\/downloads\//.test(target.pathname)
		)
			continue;
		target.hash = '';
		if (!checkedLinks.has(target.href)) checkedLinks.set(target.href, path);
		if (!seen.has(target.href) && !/\?/.test(target.search) && seen.size + queue.length < 400)
			queue.push(target.href);
	}
}
for (const [href, from] of checkedLinks) {
	if (!seen.has(href)) {
		const res = await fetch(href, { redirect: 'manual' });
		if (res.status >= 400) error(from, `broken link ${href} (${res.status})`);
	} else if (seen.get(href) === null) {
		error(from, `broken link ${href}`);
	}
}

// Duplicate titles and descriptions among indexable pages.
for (const field of ['title', 'description']) {
	const byValue = new Map();
	for (const [url, page] of seen) {
		if (!page || page.noindex || !page[field]) continue;
		const path = new URL(url).pathname;
		const key = `${path.split('/')[1]}|${page[field]}`; // Same text in two languages is fine.
		byValue.set(key, [...(byValue.get(key) ?? []), path]);
	}
	for (const [key, paths] of byValue)
		if (paths.length > 1)
			error(
				paths[0],
				`duplicate ${field} on ${paths.length} pages: "${key.split('|')[1].slice(0, 60)}" (${paths.join(', ')})`,
			);
}
for (const [url, page] of seen) {
	if (!page || page.noindex) continue;
	const path = new URL(url).pathname;
	if (!page.title) error(path, 'missing title');
	if (page.title.length > 70) warn(path, `title is ${page.title.length} characters`);
	if (page.description.length > 160)
		warn(path, `description is ${page.description.length} characters`);
}

console.log(
	`SEO check (${mode}) on ${base}: ${seen.size} pages, ${checkedLinks.size} internal links.`,
);
for (const w of warnings) console.log(`warning  ${w}`);
for (const e of errors) console.log(`error    ${e}`);
console.log(`${errors.length} errors, ${warnings.length} warnings.`);
process.exit(errors.length ? 1 : 0);
