// Forms with test data: validation errors, successful submit, dataLayer events, no personal data in the dataLayer.
// Only on a local site by default: on any other site a test submission sends real email to the team
// (E2E_FORMS=1 forces it, then use a mailbox you watch and delete the submissions afterwards).
import { expect, test } from '@playwright/test';
import { execFileSync } from 'node:child_process';
import { FORMS_ENABLED, IS_LOCAL, blockGoogle, rejectCookies } from './helpers.mjs';

// The form allows 6 attempts an hour per address; every test here makes two. Clear the counters on a local site.
test.beforeEach(() => {
	if (IS_LOCAL) execFileSync('ddev', ['wp', 'transient', 'delete', '--all'], { stdio: 'ignore' });
});

test.skip(!FORMS_ENABLED, 'forms are only submitted on a local site (E2E_FORMS=1 to force)');

const forms = [
	{ lang: 'en', path: '/en/contact/', ok: /thank|sent|received/i },
	{ lang: 'nl', path: '/nl/contact-opnemen/', ok: /bedankt|verzonden|ontvangen/i },
];

for (const { lang, path, ok } of forms) {
	test(`contact form ${lang}: errors, then success, events without personal data`, async ({
		page,
		context,
	}) => {
		await rejectCookies(context);
		await blockGoogle(page);
		await page.addInitScript(() => (window.dataLayer = window.dataLayer ?? []));
		await page.goto(path);
		const form = page.locator('form[data-cn-form="contact"]');
		await expect(form).toBeVisible();

		// Empty submit (after the minimum time, otherwise the form answers "that was very fast"): errors are shown and announced.
		await page.waitForTimeout(3500);
		await form.getByRole('button', { name: /send|verstuur|verzend|bericht/i }).click();
		await expect(form.locator('[role=alert]').first()).toBeVisible();
		await expect(form.locator('[aria-invalid=true]').first()).toBeAttached();

		// Fill in test data and send (the form refuses an instant submit: wait for the minimum time).
		await form.locator('input[name=name]').fill('QA Test');
		await form.locator('input[name=email]').fill(`qa-test+${lang}@example.com`);
		await form.locator('textarea[name=message]').fill('Automated QA test. Please ignore.');
		await form.locator('input[name=consent]').check();
		await page.waitForTimeout(3500);
		await form.getByRole('button', { name: /send|verstuur|verzend|bericht/i }).click();
		await expect(page.locator('.cn-form__success')).toContainText(ok);

		const events = await page.evaluate(() =>
			window.dataLayer.filter((e) => String(e.event).startsWith('form_')),
		);
		const names = events.map((e) => e.event);
		expect(names).toEqual(expect.arrayContaining(['form_start', 'form_error', 'form_submit']));
		expect(JSON.stringify(events)).not.toMatch(/qa-test|QA Test|Automated QA/);
		expect(events.every((e) => e.form_id === 'contact' && e.form_language === lang)).toBe(true);
	});
}
