// Renders each URL x lang x viewport; screenshots + structured extract + network log.
import { chromium } from 'playwright';
import fs from 'fs';
const BASE = 'https://chargenet.energy';
const sitemap = fs.readFileSync('../../docs/audit/sitemap.xml', 'utf8');
const fromMap = [...sitemap.matchAll(/<loc>([^<]+)<\/loc>/g)].map(m => new URL(m[1]).pathname);
const extra = ['/faq','/careers','/privacy','/privacy-policy','/security','/terms-and-conditions','/blog',
  '/locatie','/vervoerders','/rapport2027','/trend2026','/routecheck','/opbrengst','/this-page-does-not-exist'];
const blogIdx = JSON.parse(fs.readFileSync('../../docs/audit/blog/index.json','utf8')).map(b => '/blog/' + b.slug);
const paths = [...new Set([...fromMap, ...extra, ...blogIdx])];
const only = process.argv[2] ? paths.filter(p => p.includes(process.argv[2])) : paths;
const slug = p => (p === '/' ? 'home' : p.slice(1).replace(/\//g, '__'));
const SENSITIVE = /(key|token|secret|auth|apikey|password|signature|sig)/i;
const scrub = u => { try { const x = new URL(u); for (const [k] of [...x.searchParams]) if (SENSITIVE.test(k)) x.searchParams.set(k, '[REDACTED]'); return x.toString(); } catch { return u; } };

const extract = () => {
  const txt = e => (e.innerText || '').replace(/\s+\n/g, '\n').trim();
  const meta = {};
  document.querySelectorAll('meta').forEach(m => { const k = m.getAttribute('name') || m.getAttribute('property'); if (k) (meta[k] ||= []).push(m.content); });
  const wrap = document.querySelector('#root .min-h-screen') || document.body;
  let blocks = [...wrap.children];
  // descend into <main> / single-child wrappers so hero + content blocks are listed separately
  const flat = [];
  const walk = (el, depth) => {
    const kids = [...el.children].filter(k => !['SCRIPT', 'STYLE'].includes(k.tagName));
    if (depth < 3 && kids.length > 1 && (el.tagName === 'MAIN' || (el.tagName === 'DIV' && !/^(SECTION|NAV|FOOTER|HEADER)$/.test(el.tagName) && el.querySelector(':scope > section, :scope > div > section')))) kids.forEach(k => walk(k, depth + 1));
    else if (depth < 3 && kids.length === 1 && el.tagName === 'DIV') walk(kids[0], depth + 1);
    else flat.push(el);
  };
  blocks.forEach(b => walk(b, 0));
  const bgImg = e => { const out = []; [e, ...e.querySelectorAll('*')].forEach(n => { const bi = getComputedStyle(n).backgroundImage; if (bi && bi !== 'none') out.push(bi.slice(0, 300)); }); return out; };
  const sections = flat.filter(e => e.getBoundingClientRect().height > 0 || (e.innerText || '').trim()).map(s => ({
    tag: s.tagName.toLowerCase(), id: s.id, cls: s.className && String(s.className).slice(0, 200),
    height: Math.round(s.getBoundingClientRect().height),
    headings: [...s.querySelectorAll('h1,h2,h3,h4,h5,h6')].map(h => ({ l: h.tagName, t: txt(h) })),
    text: txt(s),
    images: [...s.querySelectorAll('img')].map(i => ({ src: i.currentSrc || i.src, alt: i.alt })),
    bgImages: bgImg(s),
    videos: [...s.querySelectorAll('video,iframe')].map(v => ({ tag: v.tagName, src: v.currentSrc || v.src, poster: v.poster })),
    ctas: [...s.querySelectorAll('a,button')].map(a => ({ t: txt(a) || a.getAttribute('aria-label') || '', href: a.getAttribute('href'), tag: a.tagName.toLowerCase(), target: a.target })),
    interactive: {
      accordions: s.querySelectorAll('[aria-expanded]').length,
      tabs: s.querySelectorAll('[role=tab]').length, selects: s.querySelectorAll('select,[role=combobox]').length,
      maps: s.querySelectorAll('.mapboxgl-map,.leaflet-container,.gm-style,canvas').length,
      carousels: s.querySelectorAll('[class*=swiper],[class*=carousel],[class*=embla],[class*=slick]').length,
      forms: s.querySelectorAll('form').length,
      marquee: s.querySelectorAll('[class*=animate-scroll]').length,
    },
  }));
  return {
    title: document.title, lang: document.documentElement.lang,
    canonical: document.querySelector('link[rel=canonical]')?.href,
    hreflang: [...document.querySelectorAll('link[rel=alternate][hreflang]')].map(l => ({ l: l.hreflang, h: l.href })),
    meta, jsonld: [...document.querySelectorAll('script[type="application/ld+json"]')].map(s => s.textContent),
    headings: [...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].map(h => ({ l: h.tagName, t: txt(h) })),
    links: [...document.querySelectorAll('a[href]')].map(a => ({ t: txt(a), h: a.getAttribute('href') })),
    forms: [...document.querySelectorAll('form')].map(f => ({ action: f.action, method: f.method, id: f.id,
      fields: [...f.elements].map(e => ({ tag: e.tagName, type: e.type, name: e.name, id: e.id, ph: e.placeholder, required: e.required, pattern: e.pattern, min: e.minLength, max: e.maxLength, label: e.labels?.[0]?.innerText, opts: e.tagName === 'SELECT' ? [...e.options].map(o => o.text) : undefined })) })),
    scripts: [...document.querySelectorAll('script[src]')].map(s => s.src.split('?')[0]), // query stripped: carries GA/GTM ids
    stylesheets: [...document.querySelectorAll('link[rel=stylesheet]')].map(l => l.href),
    sections, bodyText: txt(document.body), height: document.documentElement.scrollHeight,
  };
};

const browser = await chromium.launch();
fs.mkdirSync('out', { recursive: true });
for (const p of only) for (const lang of ['en', 'nl']) for (const [vw, w, h] of [['1440', 1440, 900], ['390', 390, 844]]) {
  const ctx = await browser.newContext({ viewport: { width: w, height: h }, locale: lang === 'nl' ? 'nl-NL' : 'en-US' });
  await ctx.addInitScript(l => { try { localStorage.setItem('preferred-language', l); } catch {} }, lang);
  const page = await ctx.newPage();
  const reqs = [];
  page.on('response', async r => { const q = r.request(); reqs.push({ url: scrub(r.url()), method: q.method(), type: q.resourceType(), status: r.status(), ct: r.headers()['content-type'] }); });
  let status = null;
  try {
    const resp = await page.goto(BASE + p, { waitUntil: 'networkidle', timeout: 45000 });
    status = resp?.status();
    // scroll to trigger lazy/scroll-reveal animations
    await page.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 400) { window.scrollTo(0, y); await new Promise(r => setTimeout(r, 120)); } window.scrollTo(0, 0); });
    await page.waitForTimeout(1200);
    const name = `${slug(p)}.${lang}.${vw}`;
    await page.screenshot({ path: `../../docs/audit/screenshots/${name}.png`, fullPage: true });
    const data = vw === '1440' ? await page.evaluate(extract) : { height: await page.evaluate(() => document.documentElement.scrollHeight) };
    fs.writeFileSync(`out/${name}.json`, JSON.stringify({ path: p, lang, vw, status, finalUrl: scrub(page.url()), reqs, data }, null, 1));
    console.log('ok', name, status, page.url());
  } catch (e) { console.log('FAIL', p, lang, vw, e.message.split('\n')[0]); }
  await ctx.close();
}
await browser.close();
