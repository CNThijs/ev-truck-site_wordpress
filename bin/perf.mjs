// Lighthouse (mobile profile) over the key templates: median of N runs, per-template numbers, regressions against a
// saved baseline. Usage: node bin/perf.mjs [baseUrl] [--runs 3] [--out file.json] [--compare file.json] [--check]
// --check exits 1 when a template breaks bin/perf-budget.json or regresses past the tolerance. docs/performance.md.
import { readFileSync, writeFileSync } from 'node:fs';
import { launch } from 'chrome-launcher';
import lighthouse from 'lighthouse';

const args = process.argv.slice(2);
const opt = (name, fallback) => {
	const i = args.indexOf(`--${name}`);
	return i === -1 ? fallback : args[i + 1];
};
const base = (args.find((a) => /^https?:/.test(a)) ?? 'https://chargenet.ddev.site').replace(
	/\/$/,
	'',
);
const runs = Number(opt('runs', 3));
const budget = JSON.parse(readFileSync(new URL('./perf-budget.json', import.meta.url), 'utf8'));

const pages = {
	home: '/en/',
	carriers: '/en/carriers/',
	locations: '/en/locations/',
	'blog-post': '/en/blog/chargenet-and-maxem-announce-ev-truck-charging-partnership/',
	'landing-nl': '/nl/rapport2027/',
};

const median = (list) => [...list].sort((a, b) => a - b)[Math.floor(list.length / 2)];
const kb = (bytes) => Math.round((bytes / 1024) * 10) / 10;

async function once(chrome, url) {
	const { lhr } = await lighthouse(url, {
		port: chrome.port,
		output: 'json',
		onlyCategories: ['performance'],
		logLevel: 'error',
	});
	const a = lhr.audits;
	const byType = Object.fromEntries(
		(a['resource-summary'].details?.items ?? []).map((i) => [i.resourceType, i]),
	);
	const lcpNode =
		JSON.stringify(a['lcp-breakdown-insight']?.details ?? {}).match(
			/"snippet":"((?:[^"\\]|\\.)*)"/,
		)?.[1] ?? '';
	const blocking =
		JSON.stringify(a['render-blocking-insight']?.details ?? {}).match(
			/https?:[^"]+\.(?:css|js)[^"]*/g,
		) ?? [];
	return {
		score: Math.round(lhr.categories.performance.score * 100),
		fcp: a['first-contentful-paint'].numericValue,
		lcp: a['largest-contentful-paint'].numericValue,
		tbt: a['total-blocking-time'].numericValue,
		cls: a['cumulative-layout-shift'].numericValue,
		si: a['speed-index'].numericValue,
		ttfb: a['server-response-time'].numericValue,
		weightKb: kb(a['total-byte-weight'].numericValue),
		requests: byType.total?.requestCount ?? 0,
		jsKb: kb(byType.script?.transferSize ?? 0),
		cssKb: kb(byType.stylesheet?.transferSize ?? 0),
		imageKb: kb(byType.image?.transferSize ?? 0),
		fontKb: kb(byType.font?.transferSize ?? 0),
		lcpElement: lcpNode,
		renderBlocking: [...new Set(blocking)].map((u) => u.replace(base, '')),
	};
}

const chrome = await launch({
	chromeFlags: ['--headless=new', '--ignore-certificate-errors', '--no-sandbox'],
});
const result = {};
try {
	for (const [name, path] of Object.entries(pages).filter(
		([n]) => !opt('only') || opt('only').split(',').includes(n),
	)) {
		const samples = [];
		for (let i = 0; i < runs; i++) samples.push(await once(chrome, base + path));
		const pick = [...samples].sort((x, y) => x.lcp - y.lcp)[Math.floor(samples.length / 2)];
		result[name] = { path, ...pick };
		for (const k of ['score', 'fcp', 'lcp', 'tbt', 'cls', 'si', 'ttfb'])
			result[name][k] = median(samples.map((s) => s[k]));
	}
} finally {
	await chrome.kill();
}

const failures = [];
const baseline = opt('compare') ? JSON.parse(readFileSync(opt('compare'), 'utf8')) : null;
console.log('template      score  LCP ms  FCP ms  TBT ms   CLS   weight KB  JS KB  req');
for (const [name, r] of Object.entries(result)) {
	console.log(
		`${name.padEnd(13)} ${String(r.score).padStart(5)} ${String(Math.round(r.lcp)).padStart(7)} ${String(Math.round(r.fcp)).padStart(7)} ${String(Math.round(r.tbt)).padStart(7)} ${r.cls.toFixed(3).padStart(6)} ${String(r.weightKb).padStart(10)} ${String(r.jsKb).padStart(6)} ${String(r.requests).padStart(4)}`,
	);
	for (const [key, limit] of Object.entries(budget)) {
		if (!key.startsWith('_') && r[key] > limit)
			failures.push(`${name}: ${key} ${Math.round(r[key] * 1000) / 1000} > budget ${limit}`);
	}
	const old = baseline?.[name];
	if (old) {
		const tol = budget._tolerance ?? 0.15;
		for (const key of ['lcp', 'tbt', 'weightKb', 'jsKb']) {
			if (
				r[key] > old[key] * (1 + tol) &&
				r[key] - old[key] > (key === 'weightKb' || key === 'jsKb' ? 10 : 100)
			)
				failures.push(`${name}: ${key} regressed ${Math.round(old[key])} -> ${Math.round(r[key])}`);
		}
		if (r.cls > old.cls + 0.02)
			failures.push(`${name}: cls regressed ${old.cls.toFixed(3)} -> ${r.cls.toFixed(3)}`);
	}
}
if (opt('out')) writeFileSync(opt('out'), JSON.stringify(result, null, '\t') + '\n');
if (failures.length) {
	console.log('\n' + failures.map((f) => `FAIL ${f}`).join('\n'));
	if (args.includes('--check')) process.exit(1);
}
