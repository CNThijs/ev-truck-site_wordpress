// End-to-end QA (Playwright). Engines: Chromium (Chrome and Edge), Firefox, WebKit (Safari); widths: mobile 390,
// tablet 820, desktop 1440. Usage: npm run test:e2e [-- --project=chromium-desktop] [-- tests/e2e/consent.spec.mjs]
//   E2E_BASE_URL   site to test (default http://chargenet.ddev.site)
//   E2E_FORMS=1    also submit the forms with test data (default: only on .ddev.site, because it sends real email elsewhere)
// Visual reference images (45 full-page PNGs, 40 MB) live in tests/e2e/__snapshots__, which is NOT in git: make them on
// the developer's Mac before a change with `npm run test:e2e:visual -- --update-snapshots`, then run
// `npm run test:e2e:visual` after the change and look at the diffs in playwright-report/. Docs: docs/qa-checklist.md.
import { defineConfig } from '@playwright/test';

const sizes = {
	mobile: { width: 390, height: 844 },
	tablet: { width: 820, height: 1180 },
	desktop: { width: 1440, height: 900 },
};
const engines = ['chromium', 'firefox', 'webkit'];

export default defineConfig({
	testDir: 'tests/e2e',
	snapshotPathTemplate: '{testDir}/__snapshots__/{arg}-{projectName}{ext}',
	timeout: 60_000,
	expect: { timeout: 10_000 },
	fullyParallel: true,
	workers: process.env.CI ? 2 : 4,
	retries: process.env.CI ? 1 : 0,
	reporter: [['list'], ['html', { open: 'never', outputFolder: 'playwright-report' }]],
	use: {
		baseURL: process.env.E2E_BASE_URL ?? 'http://chargenet.ddev.site',
		ignoreHTTPSErrors: true,
		trace: 'retain-on-failure',
	},
	projects: engines.flatMap((browserName) =>
		Object.entries(sizes).map(([size, viewport]) => ({
			name: `${browserName}-${size}`,
			use: { browserName, viewport, hasTouch: size !== 'desktop' },
		})),
	),
});
