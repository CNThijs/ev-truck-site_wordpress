// Automated accessibility check (axe-core, WCAG 2.2 AA rules plus best practices) on every page of the sitemaps, the
/* global document */
// language homes and the 404 page, in a headless Chrome with a mobile viewport. Each page is tested twice: as loaded
// (cookie banner open) and with the cookie settings panel open. Usage: node bin/check-a11y.mjs [baseUrl] [--only substring]
// Exit code 1 on any violation. Manual tests (keyboard, screen reader, zoom): docs/accessibility.md.
import { AxePuppeteer } from '@axe-core/puppeteer';
import { launch } from 'chrome-launcher';
import puppeteer from 'puppeteer-core';

const args = process.argv.slice(2);
const base = (args.find((a) => /^https?:/.test(a)) ?? 'http://chargenet.ddev.site').replace(
	/\/$/,
	'',
);
const only = args.includes('--only') ? args[args.indexOf('--only') + 1] : '';
const origin = new URL(base).origin;

async function sitemapUrls() {
	const urls = new Set([`${base}/en/`, `${base}/nl/`, `${base}/en/this-page-does-not-exist/`]);
	const index = await (await fetch(`${base}/sitemap_index.xml`)).text();
	for (const [, loc] of index.matchAll(/<loc>([^<]+)<\/loc>/g)) {
		const xml = await (await fetch(loc.replace(/^https?:\/\/[^/]+/, origin))).text();
		for (const [, page] of xml.matchAll(/<loc>([^<]+)<\/loc>/g))
			urls.add(page.replace(/^https?:\/\/[^/]+/, origin));
	}
	return [...urls].filter((u) => u.includes(only));
}

const chrome = await launch({
	chromeFlags: ['--headless=new', '--ignore-certificate-errors', '--no-sandbox'],
});
const browser = await puppeteer.connect({ browserURL: `http://127.0.0.1:${chrome.port}` });
const failures = [];
let tested = 0;

async function audit(page, label) {
	const { violations } = await new AxePuppeteer(page)
		.withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa', 'best-practice'])
		.analyze();
	for (const v of violations) {
		failures.push(
			`${label}  ${v.id} (${v.impact}) x${v.nodes.length}: ${v.help}\n      ${v.nodes[0].target.join(' ')}  ${v.helpUrl}`,
		);
	}
}

try {
	const urls = await sitemapUrls();
	const page = await browser.newPage();
	await page.setViewport({ width: 390, height: 844, isMobile: true });
	for (const url of urls) {
		await page.goto(url, { waitUntil: 'networkidle0' });
		await audit(page, `${url.replace(origin, '')} [banner]`);
		const opened = await page.evaluate(() => {
			const button =
				document.querySelector(
					'.wpconsent-preferences-button, [data-wpconsent-preferences], .wpconsent-banner-preferences-button',
				) ??
				[...document.querySelectorAll('button')].find((b) =>
					/cookie.?(settings|instellingen)|^(settings|instellingen)$/i.test(b.textContent.trim()),
				);
			button?.click();
			return Boolean(button);
		});
		if (opened) {
			await new Promise((resolve) => setTimeout(resolve, 400));
			await audit(page, `${url.replace(origin, '')} [cookie panel]`);
		}
		tested++;
	}
} finally {
	await browser.disconnect();
	await chrome.kill();
}

console.log(failures.length ? failures.join('\n') : 'No violations.');
console.log(`\n${tested} pages tested against ${base}: ${failures.length} violation group(s).`);
process.exit(failures.length ? 1 : 0);
