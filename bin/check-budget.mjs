// Enforces the JavaScript budget from bin/budget.json against the Vite build (run after `npm run build`).
//   initial: the entry script and its static imports, downloaded on every page.
//   motion:  the heaviest single preset (its chunk plus the GSAP chunks it shares), downloaded only on pages
//            that use it.
import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';
import { gzipSync } from 'node:zlib';

const dist = resolve(import.meta.dirname, '../web/wp-content/themes/chargenet/assets/dist');
const manifest = JSON.parse(readFileSync(resolve(dist, '.vite/manifest.json'), 'utf8'));
const budget = JSON.parse(readFileSync(resolve(import.meta.dirname, 'budget.json'), 'utf8'));

const entry = Object.keys(manifest).find((key) => manifest[key].isEntry && key.endsWith('main.js'));
if (!entry) {
	console.error('No main.js entry in the Vite manifest. Run npm run build first.');
	process.exit(1);
}

const collect = (key, field, seen = new Set()) => {
	if (seen.has(key)) {
		return seen;
	}
	seen.add(key);
	(manifest[key][field] ?? []).forEach((child) => collect(child, field, seen));
	return seen;
};

const initial = collect(entry, 'imports');
const kb = (keys) =>
	[...keys].reduce(
		(sum, key) => sum + gzipSync(readFileSync(resolve(dist, manifest[key].file))).length,
		0,
	) / 1024;

// Each lazily loaded preset or feature costs its own chunk plus the chunks it shares (GSAP runtime).
// A page pays for the presets it uses; the budget applies to the single most expensive one.
const lazyEntries = [...initial].flatMap((key) => manifest[key].dynamicImports ?? []);
const perPreset = lazyEntries.map((key) => {
	const closure = collect(key, 'imports');
	[...initial].forEach((shared) => closure.delete(shared));
	return [manifest[key].src ?? key, kb(closure)];
});
const worst = perPreset.reduce((max, entry) => (entry[1] > max[1] ? entry : max), ['', 0]);

const rows = [
	['initial JS (every page)', kb(initial), budget.initialJsKb],
	[`motion JS, heaviest preset (${worst[0].split('/').pop()})`, worst[1], budget.motionJsKb],
];
perPreset.forEach(([name, size]) =>
	console.log(`     ${name.split('/').pop()}: ${size.toFixed(1)} KB gzip`),
);

let failed = false;
rows.forEach(([name, size, limit]) => {
	const over = size > limit;
	failed ||= over;
	console.log(`${over ? 'FAIL' : 'ok  '} ${name}: ${size.toFixed(1)} KB gzip (budget ${limit} KB)`);
});
process.exit(failed ? 1 : 0);
