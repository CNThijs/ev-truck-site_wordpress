// Preset "counters": numbers marked data-count (Statistics) count up from 0 when they scroll into view.
// The final text is in the HTML and stays there until the count starts, so crawlers and no-JS visitors read the real
// figure. Prefix, suffix and decimals come from the markup (data-count-prefix / -suffix, decimals of data-count).
// While counting, the element is announced with its final value instead of the changing digits.
import { inFirstViewport, setup, settings } from '../runtime.js';

export default function counters(sections, { reduced }) {
	if (reduced) {
		return () => {};
	}
	const { gsap } = setup();
	const restore = [];

	const context = gsap.context(() => {
		sections.forEach((section) => {
			if (section.classList.contains('is-first-section')) {
				return;
			}
			section.querySelectorAll('[data-count]').forEach((element) => {
				const end = parseFloat(element.dataset.count);
				if (!Number.isFinite(end) || inFirstViewport(element)) {
					return;
				}
				const config = settings(element, { duration: 1.2 });
				const finalText = element.textContent;
				const decimals = (element.dataset.count.split('.')[1] ?? '').length;
				const prefix = element.dataset.countPrefix ?? '';
				const suffix = element.dataset.countSuffix ?? '';
				const state = { value: 0 };
				const render = () => {
					element.textContent = `${prefix}${state.value.toFixed(decimals)}${suffix}`;
				};
				const finish = () => {
					element.textContent = finalText;
					element.removeAttribute('role');
					element.removeAttribute('aria-label');
				};
				restore.push(finish);

				gsap.to(state, {
					value: end,
					duration: config.duration,
					delay: config.delay,
					ease: 'power2.out',
					immediateRender: false,
					onStart: () => {
						element.setAttribute('role', 'img');
						element.setAttribute('aria-label', finalText);
						render();
					},
					onUpdate: render,
					onComplete: finish,
					scrollTrigger: { trigger: element, start: 'top 90%', once: true },
				});
			});
		});
	});

	return () => {
		context.revert();
		restore.forEach((finish) => finish());
	};
}
