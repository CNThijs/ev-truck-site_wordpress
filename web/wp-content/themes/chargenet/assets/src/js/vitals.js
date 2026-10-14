// Field Core Web Vitals (LCP, CLS, INP) sent once per page view as one dataLayer event when the page is hidden.
// Only a number per metric, no URL parameters or visitor data. Tag Manager (loaded after statistics consent) picks the
// event up; nothing leaves the browser without it. Docs: docs/tracking.md.
if ('PerformanceObserver' in window) {
	const v = { lcp: 0, cls: 0, inp: 0 };
	const watch = (type, fn, opts = {}) => {
		try {
			new PerformanceObserver((list) => list.getEntries().forEach(fn)).observe({
				type,
				buffered: true,
				...opts,
			});
		} catch {
			/* entry type not supported */
		}
	};

	watch('largest-contentful-paint', (e) => {
		v.lcp = e.startTime;
	});
	watch('layout-shift', (e) => {
		if (!e.hadRecentInput) v.cls += e.value;
	});
	watch(
		'event',
		(e) => {
			if (e.interactionId && e.duration > v.inp) v.inp = e.duration;
		},
		{ durationThreshold: 40 },
	);

	let sent = false;
	addEventListener('visibilitychange', () => {
		if (document.visibilityState !== 'hidden' || sent || !v.lcp) return;
		sent = true;
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({
			event: 'web_vitals',
			vitals_lcp: Math.round(v.lcp),
			vitals_cls: Math.round(v.cls * 1000) / 1000,
			vitals_inp: Math.round(v.inp),
		});
	});
}
