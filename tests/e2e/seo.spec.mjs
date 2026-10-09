// Structured data and language links on every page (parsed, not just present), and hreflang/canonical reciprocity.
// Runs once (Chromium desktop).
import { expect, test } from '@playwright/test';
import { BASE, sitemapPaths } from './helpers.mjs';

test('structured data is valid JSON-LD with the right types; hreflang pairs link back', async ({
	page,
	request,
}, info) => {
	test.skip(info.project.name !== 'chromium-desktop', 'runs once');
	test.setTimeout(300_000);
	const problems = [];
	const alternates = new Map();
	for (const path of await sitemapPaths()) {
		const html = await (await request.get(path)).text();
		const blocks = [
			...html.matchAll(/<script type="application\/ld\+json"[^>]*>([\s\S]*?)<\/script>/g),
		].map((m) => m[1]);
		const types = new Set();
		for (const raw of blocks) {
			try {
				const data = JSON.parse(raw);
				for (const node of data['@graph'] ?? [data])
					[].concat(node['@type'] ?? []).forEach((t) => types.add(t));
			} catch (e) {
				problems.push(`${path}: invalid JSON-LD (${e.message})`);
			}
		}
		const isPost = /\/blog\/[^/]+\/$/.test(path);
		if (!types.has('Organization')) problems.push(`${path}: no Organization`);
		if (!types.has('WebSite') && /^\/(en|nl)\/$/.test(path)) problems.push(`${path}: no WebSite`);
		if (isPost && !types.has('BlogPosting') && !types.has('Article'))
			problems.push(`${path}: no BlogPosting`);
		if (!/^\/(en|nl)\/$/.test(path) && !types.has('BreadcrumbList'))
			problems.push(`${path}: no BreadcrumbList`);
		const links = Object.fromEntries(
			[...html.matchAll(/<link rel="alternate" hreflang="([^"]+)" href="([^"]+)"/g)].map((m) => [
				m[1],
				new URL(m[2]).pathname,
			]),
		);
		alternates.set(path, links);
	}
	for (const [path, links] of alternates) {
		for (const [hreflang, target] of Object.entries(links)) {
			if (hreflang === 'x-default' || target === path) continue;
			if (!alternates.has(target))
				problems.push(
					`${path}: hreflang ${hreflang} points to ${target}, which is not in the sitemap`,
				);
			else if (!Object.values(alternates.get(target)).includes(path))
				problems.push(`${path}: ${target} does not link back`);
		}
	}
	expect(problems).toEqual([]);
	expect(BASE).toBeTruthy();
});
