// Preset "horizontal": on a Card Slider, vertical scrolling moves the cards sideways while the section is pinned.
// Wide screens with a mouse and no reduced-motion preference only; everywhere else (and without JavaScript) the
// slider stays the native, swipeable row. Tabbing to a card scrolls the page to where that card is in view.
import { setup } from '../runtime.js';

export default function horizontal(sections, { reduced, signal }) {
	if (reduced) {
		return () => {};
	}
	const { gsap } = setup();
	const media = gsap.matchMedia();

	media.add(
		'(min-width: 64rem) and (hover: hover) and (prefers-reduced-motion: no-preference)',
		() => {
			const pinned = [];
			sections.forEach((section) => {
				const slider = section.querySelector('.card-slider');
				const viewport = slider?.querySelector('.card-slider__viewport');
				const track = slider?.querySelector('.card-slider__track');
				if (!track || section.classList.contains('is-first-section')) {
					return;
				}
				const distance = () => Math.max(0, track.scrollWidth - viewport.clientWidth);
				if (distance() < 2) {
					return;
				}
				slider.classList.add('is-pinned');
				pinned.push(slider);

				const tween = gsap.to(track, {
					x: () => -distance(),
					ease: 'none',
					scrollTrigger: {
						trigger: section,
						start: 'top 96px',
						end: () => `+=${distance()}`,
						pin: true,
						scrub: true,
						anticipatePin: 1,
						invalidateOnRefresh: true,
					},
				});

				track.addEventListener(
					'focusin',
					(event) => {
						const card = event.target.closest('.slide');
						const trigger = tween.scrollTrigger;
						if (!card || !trigger || distance() === 0) {
							return;
						}
						const progress = Math.min(1, Math.max(0, card.offsetLeft / distance()));
						window.scrollTo({
							top: trigger.start + progress * (trigger.end - trigger.start),
							behavior: 'auto',
						});
					},
					{ signal },
				);
			});
			return () => pinned.forEach((slider) => slider.classList.remove('is-pinned'));
		},
	);

	return () => media.revert();
}
