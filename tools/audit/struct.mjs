import { chromium } from 'playwright';
const b = await chromium.launch(); const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
await ctx.addInitScript(() => localStorage.setItem('preferred-language', 'en'));
const p = await ctx.newPage();
for (const u of ['/', '/locations', '/blog', '/blog/move-east-2025', '/rapport2027']) {
  await p.goto('https://chargenet.energy' + u, { waitUntil: 'networkidle' });
  console.log('##', u, await p.evaluate(() => { let e = document.getElementById('root'); const chain = []; while (e && e.children.length === 1) { chain.push(e.tagName + '.' + String(e.className).slice(0, 40)); e = e.children[0]; } chain.push('>> ' + e.tagName + ' children:'); return chain.join(' / ') + '\n' + [...e.children].map(c => `  ${c.tagName}#${c.id}.${String(c.className).slice(0, 50)} h=${Math.round(c.getBoundingClientRect().height)} text=${(c.innerText || '').slice(0, 40).replace(/\n/g, ' ')}`).join('\n'); }));
}
await b.close();
