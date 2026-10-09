// Visual regression: full-page screenshots of one page per template at three widths, Chromium only, made on the
// developer's Mac (fonts render differently elsewhere). The reference images are not in git (40 MB): make them first
// with `npm run test:e2e:visual -- --update-snapshots` on the version you compare against.
import { expect, test } from '@playwright/test';
import { blockGoogle, rejectCookies } from './helpers.mjs';

const pages = {
	'home-en': '/en/',
	'home-nl': '/nl/',
	'carriers-en': '/en/carriers/',
	'locations-en': '/en/locations/',
	'about-en': '/en/about/',
	'contact-en': '/en/contact/',
	'faq-en': '/en/faq/',
	'security-en': '/en/security/',
	'privacy-en': '/en/privacy-policy/',
	'blog-en': '/en/blog/',
	'post-en': '/en/blog/chargenet-and-maxem-announce-ev-truck-charging-partnership/',
	'report-nl': '/nl/rapport2027/',
	'project-en': '/en/destination-charging/',
	'cookies-en': '/en/cookie-policy/',
	'notfound-en': '/en/this-page-does-not-exist/',
};

test.use({ reducedMotion: 'reduce' });

for (const [name, path] of Object.entries(pages)) {
	test(`visual ${name}`, async ({ page, context, browserName }) => {
		test.skip(
			browserName !== 'chromium' || process.platform !== 'darwin',
			'reference images are made with Chromium on macOS',
		);
		await rejectCookies(context);
		await blockGoogle(page);
		await page.goto(path, { waitUntil: 'load' });
		await page.evaluate(async () => {
			for (let y = 0; y < document.body.scrollHeight; y += 500) {
				window.scrollTo(0, y);
				await new Promise((r) => setTimeout(r, 50));
			}
			window.scrollTo(0, 0);
			await document.fonts.ready;
		});
		await page.waitForLoadState('networkidle');
		await expect(page).toHaveScreenshot(`${name}.png`, {
			fullPage: true,
			animations: 'disabled',
			maxDiffPixelRatio: 0.01,
		});
	});
}
