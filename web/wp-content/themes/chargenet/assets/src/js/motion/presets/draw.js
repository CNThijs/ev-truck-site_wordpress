// Preset "draw": SVG paths marked data-draw draw themselves (GSAP DrawSVGPlugin, free under the standard
// licence). data-draw-mode="scrub" ties the line to scrolling (default), "enter" plays it once on entering.
// Without JavaScript, or with reduced motion, the full line is simply there.
import { setup, settings } from '../runtime.js';

export default function draw(sections, { reduced }) {
	if (reduced) {
		return () => {};
	}
	const { gsap } = setup();

	const context = gsap.context(() => {
		sections.forEach((section) => {
			section.querySelectorAll('[data-draw]').forEach((path) => {
				const scope = path.closest('[data-draw-scope]') ?? path.closest('svg') ?? section;
				const config = settings(path);
				const scrub = path.dataset.drawMode !== 'enter';
				gsap.fromTo(
					path,
					{ drawSVG: '0%' },
					{
						drawSVG: '100%',
						ease: scrub ? 'none' : 'power2.inOut',
						duration: config.duration * 2,
						delay: config.delay,
						scrollTrigger: scrub
							? { trigger: scope, start: 'top 80%', end: 'bottom 55%', scrub: true }
							: { trigger: scope, start: 'top 85%', once: true },
					},
				);
			});
		});
	});

	return () => context.revert();
}
