// Daily post-launch check over HTTP (docs/launch-runbook.md, "Monitoring"). Usage:
//   node bin/monitor.mjs [baseUrl] [--psi]       (default https://chargenet.energy)
// Reports: every sitemap URL answers 200 and is indexable with a title; old URLs redirect in one hop (bin/check-redirects.mjs);
// robots.txt and the sitemap are right; security headers; and with --psi the Core Web Vitals field data (Chrome UX Report,
// via the free PageSpeed Insights API, 28-day p75) and the lab score for three pages. Exit code 1 on any FAIL.
// Server-side numbers (form submissions, 404s, mail failures): bin/monitor-server.php.
import { execFileSync } from 'node:child_process';

const args = process.argv.slice(2);
const base = (args.find((a) => /^https?:/.test(a)) ?? 'https://chargenet.energy').replace(
	/\/$/,
	'',
);
const origin = new URL(base).origin;
const lines = [];
let fails = 0;
let warns = 0;
const say = (level, text) => {
	if (level === 'FAIL') fails++;
	if (level === 'WARN') warns++;
	lines.push(`${level.padEnd(4)} ${text}`);
};

// --- Pages ---------------------------------------------------------------------------------------------------------
const robots = await fetch(`${base}/robots.txt`)
	.then((r) => r.text())
	.catch(() => '');
say(/Disallow:\s*\/\s*$/m.test(robots) ? 'FAIL' : 'OK', 'robots.txt does not block the whole site');
say(
	/Sitemap:\s*\S+sitemap_index\.xml/i.test(robots) ? 'OK' : 'WARN',
	'robots.txt names sitemap_index.xml',
);

const urls = new Set([`${base}/en/`, `${base}/nl/`]);
const index = await fetch(`${base}/sitemap_index.xml`)
	.then((r) => r.text())
	.catch(() => '');
if (!index.includes('<loc>')) say('FAIL', 'sitemap_index.xml is missing or empty');
for (const [, loc] of index.matchAll(/<loc>([^<]+)<\/loc>/g)) {
	const xml = await fetch(loc.replace(/^https?:\/\/[^/]+/, origin))
		.then((r) => r.text())
		.catch(() => '');
	for (const [, page] of xml.matchAll(/<loc>([^<]+)<\/loc>/g))
		urls.add(page.replace(/^https?:\/\/[^/]+/, origin));
}
let slow = 0;
const bad = [];
for (const url of urls) {
	const t0 = Date.now();
	const res = await fetch(url, { redirect: 'manual' }).catch((e) => ({
		status: `error ${e.message}`,
		text: async () => '',
		headers: new Headers(),
	}));
	const ms = Date.now() - t0;
	const html = res.status === 200 ? await res.text() : '';
	if (res.status !== 200) bad.push(`${url} answers ${res.status}`);
	else if (/<meta name=["']robots["'][^>]*noindex/i.test(html)) bad.push(`${url} is noindex`);
	else if (!/<title>[^<]{3,}<\/title>/.test(html)) bad.push(`${url} has no title`);
	if (ms > 2000) slow++;
}
say(
	bad.length ? 'FAIL' : 'OK',
	`${urls.size} sitemap URLs: ${bad.length ? bad.slice(0, 5).join('; ') : 'all 200, indexable, with a title'}`,
);
say(slow ? 'WARN' : 'OK', `${slow} page(s) took longer than 2 s`);

// --- Redirects: one hop each --------------------------------------------------------------------------------------
try {
	const out = execFileSync(
		'node',
		[new URL('./check-redirects.mjs', import.meta.url).pathname, base],
		{ encoding: 'utf8' },
	);
	say('OK', out.trim().split('\n').pop());
} catch (e) {
	say(
		'FAIL',
		`redirects: ${String(e.stdout ?? e.message)
			.trim()
			.split('\n')
			.slice(-4)
			.join(' | ')}`,
	);
}

// --- Headers -------------------------------------------------------------------------------------------------------
const head = await fetch(`${base}/en/`, { redirect: 'manual' })
	.then((r) => r.headers)
	.catch(() => new Headers());
say(head.get('strict-transport-security') ? 'OK' : 'WARN', 'Strict-Transport-Security present');
say(
	head.get('content-security-policy')
		? 'OK'
		: head.get('content-security-policy-report-only')
			? 'WARN'
			: 'FAIL',
	`Content-Security-Policy ${head.get('content-security-policy') ? 'enforcing' : head.get('content-security-policy-report-only') ? 'still report-only' : 'missing'}`,
);

// --- Core Web Vitals (field data) ---------------------------------------------------------------------------------
if (args.includes('--psi')) {
	for (const path of ['/en/', '/nl/rapport2027/', '/en/carriers/']) {
		const api = `https://www.googleapis.com/pagespeedonline/v5/runPagespeed?strategy=mobile&category=performance&url=${encodeURIComponent(base + path)}`;
		const data = await fetch(api)
			.then((r) => r.json())
			.catch(() => ({}));
		const field = data.loadingExperience?.metrics;
		const lab = data.lighthouseResult?.categories?.performance?.score;
		if (!data.lighthouseResult) {
			say(
				'WARN',
				`PageSpeed API gave no result for ${path} (${data.error?.message ?? 'no answer'})`,
			);
			continue;
		}
		const p75 = (key) => field?.[key]?.percentile;
		const verdict = (value, good, poor) =>
			value === undefined ? 'n/a' : value <= good ? 'good' : value <= poor ? 'needs work' : 'poor';
		const lcp = p75('LARGEST_CONTENTFUL_PAINT_MS');
		const cls = p75('CUMULATIVE_LAYOUT_SHIFT_SCORE');
		const inp = p75('INTERACTION_TO_NEXT_PAINT');
		say(
			field
				? verdict(lcp, 2500, 4000) === 'poor' || verdict(inp, 200, 500) === 'poor'
					? 'WARN'
					: 'OK'
				: 'OK',
			`${path} lab score ${Math.round((lab ?? 0) * 100)}; field p75 LCP ${lcp ?? 'n/a'} ms (${verdict(lcp, 2500, 4000)}), INP ${inp ?? 'n/a'} ms (${verdict(inp, 200, 500)}), CLS ${cls === undefined ? 'n/a' : cls / 100} ${field ? '' : '(no field data yet: too little traffic)'}`,
		);
	}
}

console.log(`Monitor ${new Date().toISOString().slice(0, 10)} ${base}`);
console.log(lines.join('\n'));
console.log(`\n${fails} failed, ${warns} warnings.`);
process.exit(fails ? 1 : 0);
