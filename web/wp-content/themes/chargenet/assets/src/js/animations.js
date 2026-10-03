import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { duration, prefersReducedMotion } from './motion.js';

gsap.registerPlugin(ScrollTrigger);

// Fade-and-rise for [data-reveal] elements that start below the fold. Anything already visible is
// left alone, so there is no flash, and nothing is hidden if JS or GSAP fails.
if (!prefersReducedMotion()) {
	document.querySelectorAll('[data-reveal]').forEach((el) => {
		if (el.getBoundingClientRect().top < window.innerHeight) {
			return;
		}
		gsap.from(el, {
			autoAlpha: 0,
			y: 24,
			duration: duration('slow'),
			ease: 'power3.out',
			scrollTrigger: { trigger: el, start: 'top 90%', once: true },
		});
	});
}
