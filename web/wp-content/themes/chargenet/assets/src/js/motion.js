// Motion tokens live in CSS (tokens.json); read them here so JS and CSS stay in sync.
const rootStyle = () => getComputedStyle(document.documentElement);

const toSeconds = (value) => {
	const v = value.trim();
	return v.endsWith('ms') ? parseFloat(v) / 1000 : parseFloat(v);
};

export const duration = (name) => toSeconds(rootStyle().getPropertyValue(`--duration-${name}`));

export const prefersReducedMotion = () =>
	window.matchMedia('(prefers-reduced-motion: reduce)').matches;
