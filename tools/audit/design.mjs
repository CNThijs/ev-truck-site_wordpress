import { chromium, request } from 'playwright';
import fs from 'fs';
const pages = ['/', '/locations', '/carriers', '/about', '/faq', '/blog', '/rapport2027'];
const b = await chromium.launch();
const tally = { color: {}, bg: {}, border: {}, font: {}, textStyles: {}, btn: {}, sectionPad: {}, radius: {}, shadow: {}, container: {} };
const inc = (o, k) => { o[k] = (o[k] || 0) + 1; };
for (const p of pages) {
  const ctx = await b.newContext({ viewport: { width: 1440, height: 900 } });
  await ctx.addInitScript(() => localStorage.setItem('preferred-language', 'en'));
  const page = await ctx.newPage(); await page.goto('https://chargenet.energy' + p, { waitUntil: 'networkidle' });
  const r = await page.evaluate(() => {
    const o = { els: [], sections: [], btns: [], containers: [] };
    for (const e of document.body.querySelectorAll('*')) {
      const s = getComputedStyle(e); if (s.display === 'none') continue;
      const hasText = [...e.childNodes].some(n => n.nodeType === 3 && n.textContent.trim());
      o.els.push({ tag: e.tagName, color: hasText ? s.color : null, bg: s.backgroundColor, border: s.borderTopWidth !== '0px' ? s.borderTopColor : null, ff: hasText ? s.fontFamily.split(',')[0].replace(/"/g, '') : null, fs: hasText ? s.fontSize : null, fw: hasText ? s.fontWeight : null, lh: hasText ? s.lineHeight : null, ls: hasText ? s.letterSpacing : null, radius: s.borderTopLeftRadius, shadow: s.boxShadow });
      if (e.tagName === 'SECTION') o.sections.push(s.paddingTop + ' / ' + s.paddingBottom);
      if (e.tagName === 'BUTTON' || (e.tagName === 'A' && /rounded|btn/.test(e.className))) o.btns.push([s.backgroundColor, s.color, s.borderRadius, s.padding, s.fontSize, s.fontWeight].join(' | '));
      if (/container|max-w-/.test(String(e.className))) o.containers.push(s.maxWidth + ' pad ' + s.paddingLeft);
    }
    return o;
  });
  for (const e of r.els) {
    if (e.color) inc(tally.color, e.color); if (e.bg && e.bg !== 'rgba(0, 0, 0, 0)') inc(tally.bg, e.bg); if (e.border) inc(tally.border, e.border);
    if (e.ff) { inc(tally.font, e.ff); inc(tally.textStyles, `${e.tag} ${e.fs}/${e.lh} w${e.fw} ls${e.ls}`); }
    if (e.radius !== '0px') inc(tally.radius, e.radius); if (e.shadow !== 'none') inc(tally.shadow, e.shadow);
  }
  r.sections.forEach(s => inc(tally.sectionPad, s)); r.btns.forEach(s => inc(tally.btn, s)); r.containers.forEach(s => inc(tally.container, s));
  await ctx.close();
}
const top = (o, n = 15) => Object.entries(o).sort((a, b) => b[1] - a[1]).slice(0, n);
const out = Object.fromEntries(Object.entries(tally).map(([k, v]) => [k, top(v, k === 'textStyles' ? 30 : 15)]));
fs.writeFileSync('out/design.json', JSON.stringify(out, null, 1)); console.log(JSON.stringify(out, null, 1));
// logo/favicon downloads
const api = await request.newContext(); fs.mkdirSync('../../docs/audit/design', { recursive: true });
for (const f of ['ChargeNet-logo.svg','ChargeNet-logo.png','ChargeNet-icon.png','favicons/favicon.ico','favicons/favicon-16x16.png','favicons/favicon-32x32.png','favicons/apple-touch-icon.png','favicons/android-chrome-192x192.png','favicons/android-chrome-512x512.png','favicons/safari-pinned-tab.svg','favicons/site.webmanifest']) {
  const r = await api.get('https://chargenet.energy/uploads/' + f); console.log(f, r.status(), r.headers()['content-type'], (await r.body()).length);
  if (r.ok()) fs.writeFileSync('../../docs/audit/design/' + f.replace('/', '__'), await r.body());
}
await b.close();
