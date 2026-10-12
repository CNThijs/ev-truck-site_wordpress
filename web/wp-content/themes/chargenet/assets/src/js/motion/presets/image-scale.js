// Preset "image-scale": the photo of every card in the section (Bento photo cards, round team photos) starts slightly zoomed in and
// settles to its normal size while the card scrolls into view (scrubbed, so scrolling back zooms in again). Only the photo moves, inside
// the card's clipping frame; text never moves or fades, so contrast does not change. Nothing happens with reduced motion or in the
// first section.
import { setup } from '../runtime.js';

export default function imageScale(sections, { reduced }) {
	if (reduced) {
		return () => {};
	}
	const { gsap } = setup();

	const context = gsap.context(() => {
		sections.forEach((section) => {
			if (section.classList.contains('is-first-section')) {
				return;
			}
			section.querySelectorAll('.bento-item__media img, .person__photo img').forEach((image) => {
				const frame = image.closest('.bento-item, .person') ?? image;
				gsap.fromTo(
					image,
					{ scale: 1.18 },
					{
						scale: 1,
						ease: 'none',
						scrollTrigger: { trigger: frame, start: 'top bottom', end: 'top 25%', scrub: true },
					},
				);
			});
		});
	});

	return () => context.revert();
}
