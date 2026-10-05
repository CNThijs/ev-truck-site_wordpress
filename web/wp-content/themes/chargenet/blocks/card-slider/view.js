// Front-end script module for Card Slider. Loaded only on pages that use this block.
// Content must already be in the server-rendered HTML; this file only enhances it: it shows the
// previous/next buttons when the cards overflow and scrolls the row by one card.
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function init(root) {
	const viewport = root.querySelector('.card-slider__viewport');
	const controls = root.querySelector('.card-slider__controls');
	const prev = root.querySelector('[data-slider-prev]');
	const next = root.querySelector('[data-slider-next]');
	if (!viewport || !controls || !prev || !next) {
		return;
	}

	// aria-disabled instead of disabled, so focus stays on the button at either end.
	const setState = (button, disabled) => button.setAttribute('aria-disabled', String(disabled));

	const update = () => {
		const max = viewport.scrollWidth - viewport.clientWidth;
		controls.hidden = max <= 2;
		setState(prev, viewport.scrollLeft <= 2);
		setState(next, viewport.scrollLeft >= max - 2);
	};

	const step = () => {
		const card = viewport.querySelector('.slide');
		if (!card) {
			return viewport.clientWidth;
		}
		const gap = parseFloat(getComputedStyle(card.parentElement).columnGap) || 0;
		return card.getBoundingClientRect().width + gap;
	};

	const scroll = (direction) => () => {
		const button = direction < 0 ? prev : next;
		if (button.getAttribute('aria-disabled') === 'true') {
			return;
		}
		viewport.scrollBy({
			left: direction * step(),
			behavior: reduceMotion.matches ? 'auto' : 'smooth',
		});
	};

	prev.addEventListener('click', scroll(-1));
	next.addEventListener('click', scroll(1));
	viewport.addEventListener('scroll', update, { passive: true });
	new ResizeObserver(update).observe(viewport);
	update();
}

document.querySelectorAll('[data-card-slider]').forEach(init);
