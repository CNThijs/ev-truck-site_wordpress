import { chromium } from 'playwright';
const b = await chromium.launch(); const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
await ctx.addInitScript(() => localStorage.setItem('preferred-language', 'en'));
const p = await ctx.newPage(); const reqs = [];
p.on('request', r => { if (!/chargenet\.energy|googletag|google-analytics|calendly|gpteng|fonts\.g/.test(r.url())) reqs.push(r.method() + ' ' + r.url().split('?')[0]); });
await p.goto('https://chargenet.energy/locations', { waitUntil: 'networkidle' });
const steps = p.getByRole('button', { name: 'View Details' });
console.log('viewdetails buttons', await steps.count());
const before = await p.getByText('Currently Viewing').count();
await steps.first().click(); await p.waitForTimeout(600);
console.log('active step text after click:', await p.evaluate(() => [...document.querySelectorAll('h3')].map(h => h.innerText).slice(-7).join(' | ')));
// home carousel
await p.goto('https://chargenet.energy/', { waitUntil: 'networkidle' });
await p.locator('#projects').scrollIntoViewIfNeeded(); const t1 = await p.locator('#projects h3').first().innerText(); await p.waitForTimeout(7000); const t2 = await p.locator('#projects h3').first().innerText();
console.log('carousel first h3 before/after 7s:', t1.slice(0,40), '/', t2.slice(0,40));
// hero CTA
const cta = p.locator('#root button, #root a').filter({ hasText: /get started|start|demo|contact|schedule/i }); console.log('hero ctas', await p.locator('div.banner-container button, div.banner-container a').evaluateAll(a => a.map(x => [x.innerText.trim(), x.getAttribute('href')])));
await p.locator('div.banner-container button').first().click().catch(e => console.log('no hero button', e.message.split('\n')[0])); await p.waitForTimeout(2500);
console.log('typeform frame:', await p.locator('iframe[src*=typeform]').count());
console.log('extra 3rd-party requests:', [...new Set(reqs)]);
await b.close();
