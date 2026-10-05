// Motion core: finds the animation preset chosen in Section Settings (data-animation on a section) and loads
// only the matching preset modules. GSAP lives in those lazy chunks, so pages without animated sections never
// download it. The html element's data-motion attribute is the hand-over from the CSS start state to JS:
//   (absent)  CSS hides the reveal targets below the first section; head.php sets "timeout" after 4 s as a safety net
//   active    presets are running (or there was nothing to animate)
//   failed    a preset failed to load: everything is shown
import { prefersReducedMotion } from '../motion.js';

// Section presets, chosen per section in Section Settings (data-animation).
const presets = {
	'fade-rise': () => import('./presets/fade-rise.js'),
	stagger: () => import('./presets/stagger.js'),
	parallax: () => import('./presets/parallax.js'),
	draw: () => import('./presets/draw.js'),
	counters: () => import('./presets/counters.js'),
	'text-lines': () => import('./presets/text-lines.js'),
	horizontal: () => import('./presets/horizontal.js'),
};

// Page features that are not tied to a section: found by selector, same module contract as presets.
const features = {
	'[data-scroll-progress]': () => import('./features/progress.js'),
};

const root = document.documentElement;
let stop = () => {};
let run = 0;

async function start() {
	stop();
	const current = ++run;
	const controller = new AbortController();
	const cleanups = [];
	stop = () => {
		controller.abort();
		cleanups.splice(0).forEach((cleanup) => cleanup());
	};

	const found = new Map();
	document.querySelectorAll('[data-animation]').forEach((section) => {
		const name = section.dataset.animation;
		if (presets[name]) {
			found.set(name, [...(found.get(name) ?? []), section]);
		}
	});

	const loaders = [...found.keys()].map((name) => presets[name]);
	const groups = [...found.values()];
	Object.entries(features).forEach(([selector, load]) => {
		const elements = [...document.querySelectorAll(selector)];
		if (elements.length > 0) {
			loaders.push(load);
			groups.push(elements);
		}
	});

	try {
		const modules = await Promise.all(loaders.map((load) => load()));
		if (current !== run) {
			return; // A newer start() superseded this one.
		}
		const options = { reduced: prefersReducedMotion(), signal: controller.signal };
		groups.forEach((elements, index) => {
			cleanups.push(modules[index].default(elements, options));
		});
		root.dataset.motion = 'active';
	} catch {
		root.dataset.motion = 'failed';
	}
}

start();

// Re-run when the OS setting changes or when the Motion Lab asks (replay, simulated reduced motion).
window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', start);
document.addEventListener('chargenet:motion-restart', start);
