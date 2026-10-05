// Shared GSAP runtime for the presets. Imported only by preset modules, so it ships in the lazy chunk.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { DrawSVGPlugin } from 'gsap/DrawSVGPlugin';
import { distance, duration } from '../motion.js';

gsap.registerPlugin(ScrollTrigger, DrawSVGPlugin);

let refreshing = false;

// Keep trigger positions right when the layout changes after the first paint: web fonts, late images,
// content that grows. Runs once; ScrollTrigger itself handles window resize and orientation change.
function watchLayout() {
	if (refreshing) {
		return;
	}
	refreshing = true;
	document.fonts?.ready.then(() => ScrollTrigger.refresh());
	window.addEventListener('load', () => ScrollTrigger.refresh(), { once: true });
	let timer;
	new ResizeObserver(() => {
		clearTimeout(timer);
		timer = setTimeout(() => ScrollTrigger.refresh(), 200);
	}).observe(document.body);
}

const KEYS = ['delay', 'duration', 'distance', 'stagger'];

// Preset settings: data-motion-delay / -duration / -distance / -stagger on the element or any ancestor,
// falling back to the motion tokens (or the preset's own defaults). Delay, duration and stagger are seconds;
// distance is pixels.
export function settings(element, presetDefaults = {}) {
	const defaults = {
		delay: 0,
		duration: duration('slow'),
		distance: distance('md'),
		stagger: 0,
		...presetDefaults,
	};
	return Object.fromEntries(
		KEYS.map((key) => {
			const raw = element.closest(`[data-motion-${key}]`)?.dataset[
				`motion${key[0].toUpperCase()}${key.slice(1)}`
			];
			const value = parseFloat(raw);
			return [key, Number.isFinite(value) ? value : defaults[key]];
		}),
	);
}

export function setup() {
	watchLayout();
	return { gsap, ScrollTrigger };
}

// Content already in (or near) the first viewport is never hidden or moved: it must be readable at once and must
// not delay the largest contentful paint.
export const inFirstViewport = (element) =>
	element.getBoundingClientRect().top < window.innerHeight * 0.9;
