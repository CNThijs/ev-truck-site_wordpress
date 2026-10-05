// Preset "parallax": the background image of a section ([data-hero-media], used by Hero and Statistics) drifts
// a little slower than the page. Scroll-scrubbed transform, wide screens only. CSS makes the image taller
// than its frame for sections with this preset, so the edges never show.
import { setup, settings } from '../runtime.js';

export default function parallax(sections, { reduced }) {
	if (reduced) {
		return () => {};
	}
	const { gsap } = setup();
	const media = gsap.matchMedia();

	media.add('(min-width: 48rem)', () => {
		sections.forEach((section) => {
			const image = section.querySelector('[data-hero-media] img');
			if (!image) {
				return;
			}
			// Percent of the image height; --distance-lg (48px) maps to roughly 8 %.
			const shift = (settings(section).distance / 48) * 8;
			// A section that starts in view (the hero) begins at rest and drifts down as the page scrolls away.
			const startsInView = section.getBoundingClientRect().top < window.innerHeight * 0.2;
			gsap.fromTo(
				image,
				{ yPercent: startsInView ? 0 : -shift },
				{
					yPercent: startsInView ? shift * 2 : shift,
					ease: 'none',
					scrollTrigger: {
						trigger: section,
						start: startsInView ? 'top top' : 'top bottom',
						end: 'bottom top',
						scrub: true,
					},
				},
			);
		});
	});

	return () => media.revert();
}
