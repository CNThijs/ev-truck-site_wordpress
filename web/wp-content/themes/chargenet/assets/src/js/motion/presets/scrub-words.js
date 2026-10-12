// Preset "scrub-words": the words of a [data-scrub] paragraph go from faint to full strength one after the other while the
// reader scrolls through it (scrubbed, so scrolling back dims them again). Unread words stay at 55% strength, which keeps the
// text above the WCAG AA contrast ratio (4.5:1) on every section variant at every moment. The text is real HTML from the server; SplitText
// only wraps words, keeps the accessible name and reverts when the preset stops. Without JavaScript, with reduced motion
// and in the first section nothing changes: the sentence is simply fully visible.
import { SplitText } from 'gsap/SplitText';
import { setup } from '../runtime.js';

export default function scrubWords(sections, { reduced }) {
	if (reduced) {
		return () => {};
	}
	const { gsap } = setup();
	gsap.registerPlugin(SplitText);

	const context = gsap.context(() => {
		sections.forEach((section) => {
			if (section.classList.contains('is-first-section')) {
				return;
			}
			section.querySelectorAll('[data-scrub]').forEach((text) => {
				SplitText.create(text, {
					type: 'words',
					tag: 'span',
					aria: 'none', // The words stay readable text; no aria-label is added to the paragraph.
					autoSplit: true,
					onSplit: (split) =>
						gsap.fromTo(
							split.words,
							{ opacity: 0.55 },
							{
								opacity: 1,
								ease: 'none',
								stagger: 0.12,
								scrollTrigger: { trigger: text, start: 'top 80%', end: 'bottom 45%', scrub: 0.6 },
							},
						),
				});
			});
		});
	});

	return () => context.revert();
}
