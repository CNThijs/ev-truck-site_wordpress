// Feature "scroll progress": a thin bar at the top ([data-scroll-progress], printed on posts and pages) fills as you
// read. Plain JavaScript, no GSAP. It stays hidden until the page is clearly longer than the window, and without
// JavaScript it never shows. It indicates state rather than moving content, so it also stays with reduced motion.
export default function progress([bar]) {
	let frame = 0;

	const update = () => {
		frame = 0;
		const max = document.documentElement.scrollHeight - window.innerHeight;
		bar.hidden = max < window.innerHeight * 1.5;
		bar.style.setProperty('--progress', max > 0 ? String(Math.min(1, window.scrollY / max)) : '0');
	};
	const schedule = () => {
		frame ||= requestAnimationFrame(update);
	};

	window.addEventListener('scroll', schedule, { passive: true });
	window.addEventListener('resize', schedule);
	update();

	return () => {
		window.removeEventListener('scroll', schedule);
		window.removeEventListener('resize', schedule);
		cancelAnimationFrame(frame);
		bar.hidden = true;
	};
}
