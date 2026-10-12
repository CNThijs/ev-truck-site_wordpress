// Decorative photos below the first screen (the Bento photo cards) carry their address in data-lazy-* attributes and start
// loading 300px before they come into view, not with the page: they must not compete with the hero image for bandwidth.
// Without JavaScript the <noscript> copy next to each of them loads normally.
const images = document.querySelectorAll('img[data-lazy-src]');

function load(image) {
	['srcset', 'sizes', 'src'].forEach((name) => {
		const value = image.getAttribute(`data-lazy-${name}`);
		if (value) {
			image.setAttribute(name, value);
		}
	});
	image.removeAttribute('data-lazy-src');
}

if (images.length > 0) {
	if (!('IntersectionObserver' in window)) {
		images.forEach(load);
	} else {
		const observer = new IntersectionObserver(
			(entries) =>
				entries.forEach((entry) => {
					if (entry.isIntersecting) {
						observer.unobserve(entry.target);
						load(entry.target);
					}
				}),
			{ rootMargin: '300px 0px' },
		);
		images.forEach((image) => observer.observe(image));
	}
}
