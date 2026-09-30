import { chromium } from 'playwright';
const b = await chromium.launch(); const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
await ctx.addInitScript(() => localStorage.setItem('preferred-language', 'en'));
const p = await ctx.newPage();
await p.goto('https://chargenet.energy/', { waitUntil: 'networkidle' });
await p.getByRole('button', { name: 'Customers' }).first().hover(); await p.getByRole('button', { name: 'Customers' }).first().click(); await p.waitForTimeout(500);
console.log('dropdown:', JSON.stringify(await p.locator('[role=menu] a, [role=menuitem]').evaluateAll(a => a.map(x => [x.innerText.trim(), x.getAttribute('href')]))));
await p.keyboard.press('Escape');
await p.getByRole('button', { name: 'Contact Us' }).first().click(); await p.waitForTimeout(800);
console.log('after Contact click url:', p.url(), 'dialog:', await p.locator('[role=dialog]').count(), 'form fields:', JSON.stringify(await p.locator('form').first().evaluate(f => [...f.elements].map(e => [e.tagName, e.type, e.name, e.placeholder, e.required])).catch(() => null)));
console.log('dialog text:', (await p.locator('[role=dialog]').first().innerText().catch(() => '')).slice(0, 600));
for (const u of ['/location-owners','/carrier-managers']) { const r = await p.goto('https://chargenet.energy' + u, { waitUntil: 'networkidle' }); console.log(u, r.status(), await p.locator('h1').first().innerText()); }
// footer
await p.goto('https://chargenet.energy/', { waitUntil: 'networkidle' });
console.log('footer links:', JSON.stringify(await p.locator('footer a').evaluateAll(a => a.map(x => [x.innerText.trim(), x.getAttribute('href')]))));
await b.close();
