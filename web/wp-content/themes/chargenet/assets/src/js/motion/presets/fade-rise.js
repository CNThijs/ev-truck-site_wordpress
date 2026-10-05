// Preset "fade-rise": every [data-reveal] element in the section fades in and rises a few pixels when it
// scrolls into view. Transform and opacity only. Nothing in the first section or the first viewport is touched.
import { inFirstViewport, setup, settings } from '../runtime.js';

export default function fadeRise(sections, { reduced, signal }) {
	if (reduced) {
		return () => {}; // Content stays exactly where it is.
	}
	const { gsap } = setup();

	const context = gsap.context(() => {
		sections.forEach((section) => {
			if (section.classList.contains('is-first-section')) {
				return;
			}
			const groupCounts = new Map();
			section.querySelectorAll('[data-reveal]').forEach((element) => {
				if (inFirstViewport(element)) {
					return;
				}
				const config = settings(element);
				// Stagger: items of the same [data-reveal-group] follow each other (off unless data-motion-stagger is set).
				const group = element.closest('[data-reveal-group]') ?? section;
				const position = groupCounts.get(group) ?? 0;
				groupCounts.set(group, position + 1);

				const tween = gsap.from(element, {
					opacity: 0,
					y: config.distance,
					duration: config.duration,
					delay: config.delay + position * config.stagger,
					ease: 'power4.out', // closest GSAP ease to --ease-out
					clearProps: 'opacity,transform',
					scrollTrigger: { trigger: element, start: 'top 90%', once: true },
				});
				// A keyboard user tabbing into a not yet revealed element sees it at once.
				element.addEventListener('focusin', () => tween.progress(1), { once: true, signal });
			});
		});
	});

	return () => context.revert();
}
