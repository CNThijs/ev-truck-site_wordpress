// Every page of both languages: loads without errors, one h1, no horizontal scroll, images load, language and canonical
// are right, hreflang alternates are present.
import { expect, test } from '@playwright/test';
import { BASE, blockGoogle, ignorableConsole, rejectCookies, sitemapPaths } from './helpers.mjs';

const paths = [...(await sitemapPaths()), '/en/this-page-does-not-exist/'];

for (const path of paths) {
	test(`page ${path}`, async ({ page, context }) => {
		const is404 = path.includes('does-not-exist');
		await rejectCookies(context);
		await blockGoogle(page);
		const problems = [];
		page.on(
			'console',
			(m) =>
				m.type() === 'error' &&
				!ignorableConsole(m.text()) &&
				!(is404 && /status of 404/.test(m.text())) &&
				problems.push(`console: ${m.text()}`),
		);
		page.on('pageerror', (e) => problems.push(`script error: ${e.message}`));
		page.on('response', (r) => {
			if (r.url().startsWith(BASE) && r.status() >= 400 && r.url() !== `${BASE}${path}`)
				problems.push(`${r.status()} ${r.url()}`);
		});

		const response = await page.goto(path, { waitUntil: 'load' });
		expect(response.status()).toBe(is404 ? 404 : 200);
		// Scroll through the page so that lazy images load, then settle.
		await page.evaluate(async () => {
			for (let y = 0; y < document.body.scrollHeight; y += 600) {
				window.scrollTo(0, y);
				await new Promise((r) => setTimeout(r, 60));
			}
			window.scrollTo(0, 0);
		});
		await page.waitForLoadState('networkidle');

		const info = await page.evaluate(() => ({
			h1: document.querySelectorAll('h1').length,
			overflow: document.documentElement.scrollWidth - window.innerWidth,
			lang: document.documentElement.lang,
			broken: [...document.images]
				.filter((i) => i.complete && i.naturalWidth === 0 && i.currentSrc)
				.map((i) => i.currentSrc),
			canonical: document.querySelector('link[rel=canonical]')?.href ?? '',
			alternates: [...document.querySelectorAll('link[rel=alternate][hreflang]')].map(
				(l) => l.hreflang,
			),
		}));

		expect(info.h1, 'exactly one h1').toBe(1);
		expect(info.overflow, 'no horizontal scrolling').toBeLessThanOrEqual(1);
		expect(info.broken, 'images that did not load').toEqual([]);
		expect(info.lang).toBe(path.startsWith('/nl/') ? 'nl-NL' : 'en-US');
		if (!is404) {
			expect(new URL(info.canonical).pathname).toBe(path);
			expect(info.alternates).toEqual(
				expect.arrayContaining([path.startsWith('/nl/') ? 'nl' : 'en', 'x-default']),
			);
		}
		expect(problems, 'errors while loading').toEqual([]);
	});
}
