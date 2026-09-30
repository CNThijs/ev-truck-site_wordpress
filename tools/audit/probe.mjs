import { chromium } from 'playwright';
const b = await chromium.launch(); const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
await ctx.addInitScript(() => localStorage.setItem('preferred-language', 'en'));
const p = await ctx.newPage(); const reqs = [];
p.on('request', r => { if (r.method() !== 'GET' || !/chargenet\.energy/.test(r.url())) reqs.push(r.method() + ' ' + r.url().split('?')[0]); });
const out = {};
await p.goto('https://chargenet.energy/faq', { waitUntil: 'networkidle' });
out.faqExpandedBefore = await p.locator('[aria-expanded=true]').count();
const trig = p.locator('[aria-expanded]').first(); await trig.click(); await p.waitForTimeout(500);
out.faqExpandedAfter = await p.locator('[aria-expanded=true]').count();
out.faqTriggers = await p.locator('[aria-expanded]').count();
out.faqTag = await trig.evaluate(e => e.tagName + ' ' + (e.getAttribute('data-state')||''));
// floating contact button
await p.goto('https://chargenet.energy/locations', { waitUntil: 'networkidle' });
out.buttons = await p.locator('button, a[role=button], [class*=fixed]').evaluateAll(els => els.filter(e => getComputedStyle(e).position === 'fixed' || e.closest('[class*=fixed]')).map(e => (e.innerText || e.getAttribute('aria-label') || e.tagName).trim().slice(0, 60)));
out.navLinks = await p.locator('nav a, header a').evaluateAll(a => a.map(x => [x.innerText.trim(), x.getAttribute('href')]));
// language switcher
await p.locator('[role=combobox]').first().click(); await p.waitForTimeout(300);
out.langOptions = await p.locator('[role=option]').allInnerTexts();
await p.locator('[role=option]').last().click(); await p.waitForTimeout(500);
out.afterLang = { h1: await p.locator('h1').first().innerText(), url: p.url(), ls: await p.evaluate(() => localStorage.getItem('preferred-language')) };
// cookie banner
out.cookie = await p.evaluate(() => !!document.querySelector('#silktide-wrapper, [id*=silktide]'));
// calendly badge
out.calendly = await p.evaluate(() => [...document.querySelectorAll('.calendly-badge-widget, .calendly-badge-content')].map(e => e.innerText));
// mobile menu
const m = await b.newContext({ viewport: { width: 390, height: 844 } }); const mp = await m.newPage();
await mp.goto('https://chargenet.energy/', { waitUntil: 'networkidle' });
out.mobileMenuButtons = await mp.locator('header button, nav button').evaluateAll(a => a.map(x => (x.getAttribute('aria-label') || x.innerText || 'icon').trim()));
console.log(JSON.stringify(out, null, 1)); console.log('non-GET/3rd-party requests:', reqs);
await b.close();
