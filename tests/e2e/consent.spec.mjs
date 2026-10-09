// Consent behaviour in both languages: nothing is requested from Google before a choice, accepting loads Tag Manager and
// sets the campaign cookie, rejecting does neither, every choice is logged, and the footer button reopens the banner.
// Google is blocked in the browser: a test never reaches the live Analytics property.
import { expect, test } from '@playwright/test';
import { blockGoogle, rejectCookies } from './helpers.mjs';

const languages = [
	{
		lang: 'en',
		path: '/en/',
		accept: /accept all/i,
		reject: /reject all/i,
		settings: /cookie settings/i,
	},
	{
		lang: 'nl',
		path: '/nl/',
		accept: /alles accepteren/i,
		reject: /alles weigeren/i,
		settings: /cookie-instellingen/i,
	},
];
const campaign = '?utm_source=qa&utm_medium=test&utm_campaign=qa2027&utm_term=x';

for (const { lang, path, accept, reject, settings } of languages) {
	test.describe(`consent ${lang}`, () => {
		test('nothing from Google before a choice', async ({ page, context }) => {
			const google = await blockGoogle(page);
			await page.goto(path + campaign);
			await expect(page.getByRole('button', { name: accept })).toBeVisible();
			await page.waitForTimeout(1500);
			expect(google, 'requests to Google before consent').toEqual([]);
			expect((await context.cookies()).map((c) => c.name)).not.toContain('cn_campaign');
			expect((await context.cookies()).map((c) => c.name)).not.toContain('_ga');
		});

		test('accept: Tag Manager is requested, campaign cookie set, choice logged', async ({
			page,
			context,
		}) => {
			const google = await blockGoogle(page);
			const logged = page.waitForRequest(
				(r) => r.url().includes('/chargenet/v1/consent') && r.method() === 'POST',
			);
			await page.goto(path + campaign);
			await page.getByRole('button', { name: accept }).click();
			const body = (await logged).postDataJSON();
			expect(body).toMatchObject({ statistics: true, lang });
			expect(body.version).toMatch(/^[a-f0-9]{8}$/);
			await expect
				.poll(() => google.some((u) => u.includes('googletagmanager.com/gtm.js')))
				.toBe(true);
			const cookies = Object.fromEntries((await context.cookies()).map((c) => [c.name, c.value]));
			expect(decodeURIComponent(cookies.cn_campaign)).toContain('utm_campaign=qa2027');
			expect(JSON.parse(decodeURIComponent(cookies.wpconsent_preferences)).statistics).toBe(true);
		});

		test('reject: no Google, no campaign cookie, choice logged', async ({ page, context }) => {
			const google = await blockGoogle(page);
			const logged = page.waitForRequest(
				(r) => r.url().includes('/chargenet/v1/consent') && r.method() === 'POST',
			);
			await page.goto(path + campaign);
			await page.getByRole('button', { name: reject }).click();
			expect((await logged).postDataJSON()).toMatchObject({
				statistics: false,
				marketing: false,
				lang,
			});
			await page.waitForTimeout(1000);
			expect(google).toEqual([]);
			const names = (await context.cookies()).map((c) => c.name);
			expect(names).not.toContain('cn_campaign');
			expect(names).toContain('wpconsent_preferences');
		});

		test('the choice is remembered and can be changed from the footer', async ({ page }) => {
			await blockGoogle(page);
			await page.goto(path);
			await page.getByRole('button', { name: reject }).click();
			await page.reload();
			await expect(page.getByRole('button', { name: reject })).toBeHidden();
			await page.getByRole('button', { name: settings }).first().click();
			await expect(page.getByRole('dialog').first()).toBeVisible();
		});

		test('forms carry the campaign parameters from the URL', async ({ page, context }) => {
			test.skip(lang === 'en', 'the campaign form is on the Dutch page');
			await blockGoogle(page);
			await rejectCookies(context);
			await page.goto('/nl/rapport2027/' + campaign);
			const form = page.locator('form[data-cn-form]').first();
			await expect(form.locator('input[name=cn_utm_campaign]')).toHaveValue('qa2027');
			await expect(form.locator('input[name=cn_utm_source]')).toHaveValue('qa');
		});
	});
}
