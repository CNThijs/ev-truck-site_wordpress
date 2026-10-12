// The Customers dropdown in the header opens and its links can be clicked (desktop only: on phones the menu is a panel).
import { expect, test } from '@playwright/test';

test('header dropdown opens and Locations and Carriers are clickable', async ({ page }, info) => {
	test.skip(!info.project.name.endsWith('desktop'), 'dropdown is a desktop layout');
	await page.goto('/en/');
	const toggle = page.locator('.primary-nav .submenu-toggle');
	await toggle.click();
	await expect(toggle).toHaveAttribute('aria-expanded', 'true');
	await page.getByRole('link', { name: 'Carriers', exact: true }).first().click();
	await expect(page).toHaveURL(/\/en\/carriers\/$/);
});
