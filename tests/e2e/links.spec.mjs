// Broken links: every internal link on every sitemap page answers below 400; mailto and tel links are well formed.
// Runs once (Chromium desktop); external links are listed, not requested.
import { expect, test } from '@playwright/test';
import { BASE, sitemapPaths } from './helpers.mjs';

// Absolute links to the live address are tested against the site under test.
const local = (url) =>
	/^(www\.)?chargenet\.energy$/.test(url.hostname) ? BASE + url.pathname + url.search : url.href;

test('internal links and images resolve', async ({ page, request }, info) => {
	test.skip(info.project.name !== 'chromium-desktop', 'runs once');
	test.setTimeout(300_000);
	const internal = new Set();
	const bad = [];
	for (const path of await sitemapPaths()) {
		await page.goto(path, { waitUntil: 'domcontentloaded' });
		const found = await page.evaluate(() => ({
			links: [...document.querySelectorAll('a[href]')].map((a) => a.getAttribute('href')),
			images: [...document.querySelectorAll('img[src], source[srcset]')].map(
				(e) => e.getAttribute('src') ?? e.getAttribute('srcset').split(' ')[0],
			),
		}));
		for (const href of found.links) {
			if (
				href.startsWith('mailto:') &&
				!/^mailto:([^@\s?]+@[^@\s?]+\.[a-z]{2,})?(\?|$)/i.test(href)
			)
				bad.push(`${path}: bad mailto ${href}`);
			else if (href.startsWith('tel:') && !/^tel:\+?[\d\s()-]{6,}$/.test(href))
				bad.push(`${path}: bad tel ${href}`);
			else if (href === '#' || href === '') bad.push(`${path}: empty link target "${href}"`);
			else if (
				!/^(mailto:|tel:|https?:\/\/(?!(www\.)?chargenet\.energy|chargenet\.ddev\.site)|#|javascript:)/.test(
					href,
				)
			)
				internal.add(local(new URL(href, BASE + path)).split('#')[0]);
		}
		for (const src of found.images)
			if (src && !src.startsWith('data:')) internal.add(local(new URL(src, BASE + path)));
	}
	for (const url of internal) {
		const res = await request
			.get(url, { maxRedirects: 0 })
			.catch((e) => ({ status: () => `error ${e.message}` }));
		if (typeof res.status() !== 'number' || res.status() >= 400) bad.push(`${res.status()} ${url}`);
	}
	expect(bad).toEqual([]);
});
