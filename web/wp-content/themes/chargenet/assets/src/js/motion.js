// Motion tokens live in CSS (tokens.json); read them here so JS and CSS stay in sync.
const rootStyle = () => getComputedStyle(document.documentElement);

const toSeconds = (value) => {
	const v = value.trim();
	return v.endsWith('ms') ? parseFloat(v) / 1000 : parseFloat(v);
};

// Seconds, from --duration-<name>.
export const duration = (name) => toSeconds(rootStyle().getPropertyValue(`--duration-${name}`));

// Pixels, from --distance-<name>.
export const distance = (name) => parseFloat(rootStyle().getPropertyValue(`--distance-${name}`));

// True when the visitor asked for reduced motion, or an admin simulates it in the Motion Lab.
export const prefersReducedMotion = () =>
	window.matchMedia('(prefers-reduced-motion: reduce)').matches ||
	document.documentElement.dataset.motionSimulate === 'reduce';
