// Shared helpers for the end-to-end tests.
export const BASE = (process.env.E2E_BASE_URL ?? 'http://chargenet.ddev.site').replace(/\/$/, '');
export const IS_LOCAL = /\.ddev\.site$|^localhost/.test(new URL(BASE).hostname);
export const FORMS_ENABLED =
	process.env.E2E_FORMS === '1' || (IS_LOCAL && process.env.E2E_FORMS !== '0');

/** Every URL in the sitemaps, as a path, plus the language homes. */
export async function sitemapPaths() {
	const origin = new URL(BASE).origin;
	const paths = new Set(['/en/', '/nl/']);
	const index = await (await fetch(`${BASE}/sitemap_index.xml`)).text();
	for (const [, loc] of index.matchAll(/<loc>([^<]+)<\/loc>/g)) {
		const xml = await (await fetch(loc.replace(/^https?:\/\/[^/]+/, origin))).text();
		for (const [, page] of xml.matchAll(/<loc>([^<]+)<\/loc>/g)) paths.add(new URL(page).pathname);
	}
	return [...paths].sort();
}

/** Console messages that are not a problem of the page: report-only CSP notices and our blocked analytics. */
export const ignorableConsole = (text) =>
	/\[Report Only\]|Cross-Origin-Opener-Policy header has been ignored|googletagmanager|google-analytics|ERR_BLOCKED_BY_CLIENT|ERR_FAILED/i.test(
		text,
	);

/** Stop all calls to Google (nothing may reach the live Analytics property from a test) and record the attempts. */
export async function blockGoogle(page) {
	const hits = [];
	await page.route(
		/googletagmanager\.com|google-analytics\.com|analytics\.google\.com/,
		(route) => {
			hits.push(route.request().url());
			return route.abort();
		},
	);
	return hits;
}

/** The cookie banner as an already-made choice, so that it does not cover the page. */
export async function rejectCookies(context) {
	const url = new URL(BASE);
	await context.addCookies([
		{
			name: 'wpconsent_preferences',
			value: JSON.stringify({ essential: true, statistics: false, marketing: false }),
			domain: url.hostname,
			path: '/',
		},
	]);
}
