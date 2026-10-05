// Preset "text-lines": headings marked data-split (every chargenet_heading()) reveal line by line from a clipped
// edge. The text is real HTML from the server; SplitText only wraps lines (and keeps an accessible name), splits again
// on resize and when web fonts load, and reverts when the preset stops. SplitText is imported here, so only pages with
// this preset download it. The first section and the first viewport are never touched.
import { SplitText } from 'gsap/SplitText';
import { inFirstViewport, setup, settings } from '../runtime.js';

export default function textLines(sections, { reduced }) {
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
			section.querySelectorAll('[data-split]').forEach((heading) => {
				if (inFirstViewport(heading)) {
					return;
				}
				const config = settings(heading, { stagger: 0.08 });
				SplitText.create(heading, {
					type: 'lines',
					mask: 'lines',
					autoSplit: true,
					aria: 'auto',
					onSplit: (split) =>
						gsap.from(split.lines, {
							yPercent: 110,
							duration: config.duration,
							delay: config.delay,
							stagger: config.stagger,
							ease: 'power4.out',
							scrollTrigger: { trigger: heading, start: 'top 90%', once: true },
						}),
				});
			});
		});
	});

	return () => context.revert();
}
