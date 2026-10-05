// Preset "stagger": the [data-reveal] items of a section appear one after another, in the order they enter the
// viewport (ScrollTrigger.batch). Same start state and hand-over as "fade-rise", with a default 0.08 s step
// (data-motion-stagger). Transform and opacity only; nothing in the first section or first viewport is touched.
import { inFirstViewport, setup, settings } from '../runtime.js';

export default function stagger(sections, { reduced, signal }) {
	if (reduced) {
		return () => {};
	}
	const { gsap, ScrollTrigger } = setup();

	const context = gsap.context(() => {
		sections.forEach((section) => {
			if (section.classList.contains('is-first-section')) {
				return;
			}
			const items = [...section.querySelectorAll('[data-reveal]')].filter(
				(element) => !inFirstViewport(element),
			);
			if (items.length === 0) {
				return;
			}
			const config = settings(section, { stagger: 0.08 });

			gsap.set(items, { opacity: 0, y: config.distance });
			ScrollTrigger.batch(items, {
				start: 'top 90%',
				once: true,
				onEnter: (batch) =>
					gsap.to(batch, {
						opacity: 1,
						y: 0,
						duration: config.duration,
						delay: config.delay,
						stagger: config.stagger,
						ease: 'power4.out',
						clearProps: 'opacity,transform',
						overwrite: true,
					}),
			});
			// A keyboard user tabbing into a not yet revealed item sees it at once.
			items.forEach((element) =>
				element.addEventListener(
					'focusin',
					() => {
						gsap.killTweensOf(element);
						gsap.set(element, { clearProps: 'opacity,transform' });
					},
					{ once: true, signal },
				),
			);
		});
	});

	return () => context.revert();
}
