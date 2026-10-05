import { prefersReducedMotion } from './motion.js';

const header = document.querySelector('[data-header]');
const toggle = document.querySelector('[data-nav-toggle]');
const panel = document.querySelector('[data-nav-panel]');

if (header && toggle && panel) {
	const setMenu = (open) => {
		toggle.setAttribute('aria-expanded', String(open));
		panel.classList.toggle('is-open', open);
	};
	toggle.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));

	// Sticky header: a sentinel above the page tells us when the user has scrolled.
	const sentinel = document.createElement('div');
	sentinel.setAttribute('aria-hidden', 'true');
	sentinel.style.cssText = 'position:absolute;top:0;height:1px;width:1px;pointer-events:none';
	document.body.prepend(sentinel);
	new IntersectionObserver(([entry]) =>
		header.classList.toggle('is-scrolled', !entry.isIntersecting),
	).observe(sentinel);

	// Submenus: disclosure buttons.
	const closeSubmenus = (except) => {
		panel.querySelectorAll('.menu-item-has-children.is-open').forEach((item) => {
			if (item !== except) {
				item.classList.remove('is-open');
				item.querySelector('.submenu-toggle')?.setAttribute('aria-expanded', 'false');
			}
		});
	};
	panel.querySelectorAll('.submenu-toggle').forEach((button) => {
		button.addEventListener('click', () => {
			const item = button.closest('.menu-item-has-children');
			const open = !item.classList.contains('is-open');
			closeSubmenus(item);
			item.classList.toggle('is-open', open);
			button.setAttribute('aria-expanded', String(open));
		});
	});

	document.addEventListener('click', (event) => {
		if (!header.contains(event.target)) {
			closeSubmenus();
		}
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') {
			return;
		}
		const openButton = panel.querySelector('.is-open > .submenu-toggle[aria-expanded="true"]');
		if (openButton) {
			closeSubmenus();
			openButton.focus();
		} else if (toggle.getAttribute('aria-expanded') === 'true') {
			setMenu(false);
			toggle.focus();
		}
	});

	// Following an in-page link (e.g. the Contact CTA) closes the mobile menu.
	panel.addEventListener('click', (event) => {
		if (event.target.closest('a[href^="#"]')) {
			setMenu(false);
		}
	});

	// Hide on scroll down, return on scroll up. Never while the menu or a submenu is open, while focus is inside the
	// header, near the top of the page, or when the visitor prefers reduced motion. Transform only.
	let lastY = window.scrollY;
	let frame = 0;
	const onScroll = () => {
		frame = 0;
		const y = window.scrollY;
		const delta = y - lastY;
		const locked =
			prefersReducedMotion() ||
			toggle.getAttribute('aria-expanded') === 'true' ||
			header.contains(document.activeElement) ||
			panel.querySelector('.is-open');
		if (locked || y < 160 || delta < -6) {
			header.classList.remove('is-hidden');
		} else if (delta > 6) {
			header.classList.add('is-hidden');
		}
		lastY = y;
	};
	window.addEventListener(
		'scroll',
		() => {
			frame ||= requestAnimationFrame(onScroll);
		},
		{ passive: true },
	);
	header.addEventListener('focusin', () => header.classList.remove('is-hidden'));
}
