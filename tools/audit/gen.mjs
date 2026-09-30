import fs from 'fs';
import crypto from 'crypto';
const D = '../../docs/audit';
const load = (slug, lang, vw) => { try { return JSON.parse(fs.readFileSync(`out/${slug}.${lang}.${vw}.json`)); } catch { return null; } };
const files = fs.readdirSync('out').filter(f => f.endsWith('.en.1440.json')).map(f => f.replace('.en.1440.json', ''));
const blogIdx = JSON.parse(fs.readFileSync(`${D}/blog/index.json`));
const h = s => crypto.createHash('md5').update(s).digest('hex').slice(0, 8);
const sanitize = s => String(s ?? '').replace(/\r/g, '');
const quote = s => sanitize(s).split('\n').map(l => '> ' + l).join('\n');

// ---- hand-written knowledge (source: App.tsx / components read in the clone + live probes) ----
const CANON = { locaties: 'locations', locatie: 'locations', vervoerder: 'carriers', vervoerders: 'carriers', 'over-ons': 'about', privacy: 'privacy-policy', routecheck: 'carriers', opbrengst: 'locations' };
const PURPOSE = {
  home: 'Landing page. Positions ChargeNet as the platform/marketplace for shared destination EV-truck charging; routes visitors to the two audiences (location owners → /locations, carriers → /carriers), shows credibility stats, projects, latest news and team contacts.',
  locations: 'Audience page for location owners (sites with spare grid capacity): pitch to sell unused charging capacity, benefits, partner logos; contact CTA.',
  carriers: 'Audience page for transport companies / fleet managers: pitch to charge at partner destinations and cut downtime and cost; benefits; contact CTA.',
  about: 'Company story, mission, values and team.',
  faq: 'FAQ for e-truck drivers (driver app, access, finding/reserving chargers, invoices, troubleshooting). Accordion.',
  careers: 'Employer-brand page: why join, and a contact route to the CEO. No open vacancies listed.',
  'privacy-policy': 'Legal: privacy policy (long prose). /privacy renders the identical component.',
  security: 'Trust page: ISO 27001 information-security statement.',
  'terms-and-conditions': 'Route is disabled in source (commented out in App.tsx). Live renders the 404 component with HTTP 200. Still listed in live sitemap.xml.',
  blog: 'News index: featured post + grid of all posts, EN/NL titles/excerpts, category badge. Some posts link out (externalUrl).',
  rapport2027: 'Campaign landing page (source component name Trend2026; the brief calls it /trend2026 but that URL is a 404 on live). Dutch trend report "Trendrapport 2027" lead-gen: benefits + lead form that emails the report. Bare URL redirects (client-side) to add UTM params.',
  trend2026: 'NOT a real route: renders the 404 component (HTTP 200). The campaign page lives at /rapport2027.',
  routecheck: 'DM-campaign shortlink; client-side redirect to /carriers with UTM params (utm_term=laders).',
  opbrengst: 'DM-campaign shortlink; client-side redirect to /locations with UTM params (utm_term=locaties).',
  '404': 'NotFound component. Served with HTTP 200 (SPA fallback), i.e. soft 404.',
};
const STEPPER = ['"How it works" is a 4-step stepper: each step has a "View Details" button; the active step shows "Currently Viewing" and its "Key Activities" list. Clicking changes the active step (verified live). Desktop vs mobile variant switches via useIsMobile (<768px).', 'Benefit cards and step blocks animate in on scroll (framer-motion).', 'No map, filter, calculator or API call (verified in network log).'];
const INTERACT = {
  home: ['Hero has two buttons, "Explore Use Cases" and "Contact Us". Clicking the first opens a Typeform slider overlay (iframe from typeform.com; form ID deliberately omitted) and posts a view-form-open analytics ping to Typeform. Hero video markup exists in source but is commented out: no video on live.', 'Section entrance animations via framer-motion (whileInView) on features / why / get-started.', 'Stats ("Why ChargeNet") count-up animation using requestAnimationFrame (2000ms) when in view.', 'Projects block: per source an auto-rotating carousel (interval, only while in view, pauses on hover) with touch swipe on mobile. NOT observed rotating within 7s in a live probe: needs a longer check (open question).', 'Blog preview cards link to /blog/<slug>.', '**Dead links:** the 4 project cards link to /projects/destination-charging, /chargebase, /ijmondaanzet, /bouw-pow; all four render the 404 page on live (verified).'],
  locations: STEPPER,
  carriers: STEPPER,
  blog: ['Cards link to /blog/<slug> (internal) or to externalUrl where set (see blog/index.json).'],
  faq: ['Radix accordion, single item open at a time (aria-expanded toggles; 12 question triggers + 3 navbar triggers counted, 0 open on load; verified live).'],
  rapport2027: ['Bare /rapport2027 client-side redirects to ?utm_source=trend_report&utm_medium=DM&utm_campaign=tr2027&utm_term=form-access.', '/rapport2027/download and /rapport2027/<code> redirect to the same page with utm_term=download / utm_term=<code>.', 'Lead form has two views: "with code" (allowlisted campaign code → only email needed) and "no code" (name + company + email). Max failed-code attempts tracked in sessionStorage; then user must choose "no code" path.', 'GA events: view, cta click, form start, form submit, report download.'],
};
const SHARED_INTERACT = ['Navbar: transparent, turns dark on scroll; desktop dropdown "Customers" (→ /locations, /carriers); "Contact Us" scrolls to #contact (footer) and does nothing visible on pages without that anchor target; "Login" opens https://portal.chargenet.energy in a new tab (external app, out of scope).', 'Mobile menu (hamburger) links: Home, About Us, /location-owners, /fleet-managers, News: the last two are 404 on live (bug).', 'Language switcher (EN/NL select). Sets localStorage key preferred-language; URL does not change; whole UI re-renders in the chosen language. Default EN unless stored.', 'Floating "contact" button appears after scrollY > 500 and scrolls to #contact.', 'Calendly badge widget bottom-right ("Schedule time with us"; text is not translated) opens Calendly popup for calendly.com/chargenet/introductie.'];
const THIRD = ['Google Tag Manager container + GA4 (gtag; consent defaults read from localStorage; IDs omitted here on purpose)', 'Calendly badge widget (assets.calendly.com widget.js/css)', 'Google Fonts: Source Sans 3 (fonts.googleapis.com / fonts.gstatic.com)', 'cdn.gpteng.co/gptengineer.js (Lovable/GPT-Engineer editor script: should NOT be carried to the WordPress build)', 'Silktide consent manager files exist in repo public/cookie-banner but no banner element was rendered on live in tests (see open questions)', 'Typeform embed (home hero CTA; loads on click; renderer-assets.typeform.com, font.typeform.com, typeform form host)', 'Apple App Store and Google Play badge images hot-linked in home "How to Get Started" (developer.apple.com, play.google.com); badges link to the driver app listings', 'EmailJS (browser SDK → api.emailjs.com; contact form + campaign lead form, keys omitted)', 'ipapi.co geolocation code exists in i18n.ts (detectDefaultLanguage) but was never called in live network logs'];

function classify(s, pg) {
  if (s.tag === 'a' && s.height < 5) return 'skip-anchor';
  if (s.tag === 'nav') return 'navbar';
  if (s.tag === 'footer') return 'footer';
  if (s.tag === 'article') return 'article';
  if (s.id === 'contact-info') return 'contact-team-cards';
  if (s.headings[0]?.l === 'H1') return s.height >= 500 ? 'hero-campaign' : s.height > 300 ? 'hero-image-banner' : 'hero-title-band';
  if (s.id === 'features') return 'audience-feature-split';
  if (s.id === 'whyChargeNet') return 'stats-band';
  if (s.id === 'projects') return 'card-carousel';
  if (s.id === 'getStarted') return 'step-process';
  if (s.id === 'blog') return 'blog-preview-cards';
  if (s.id === 'benefits') return 'benefit-list';
  if (s.id === 'download') return 'lead-form';
  if (s.interactive.accordions > 3) return 'faq-accordion';
  if (pg === 'blog') return 'post-grid';
  if (pg === 'locations' || pg === 'carriers') return 'pitch-and-benefits-grid';
  if (pg === 'about') return 'story-mission-values-team';
  if (pg === 'careers') return 'value-props-3col';
  if (pg === 'security') return 'prose-sections';
  if (pg === 'privacy-policy') return 'legal-prose';
  return 'content-block';
}
const inventory = {};
const addInv = (type, pg, note) => { (inventory[type] ||= {})[pg] = note || ''; };

const pathOf = slug => load(slug, 'en', 1440).path;
const pageDocs = [];
for (const slug of files) {
  const en = load(slug, 'en', 1440), nl = load(slug, 'nl', 1440); const path = en.path;
  const isBlogPost = slug.startsWith('blog__');
  let pg = CANON[slug] || slug;
  if (slug === 'this-page-does-not-exist') pg = '404';
  const same = en.data.bodyText === (load(pg, 'en', 1440)?.data.bodyText);
  const ident = `${h(en.data.bodyText)} / ${h(nl.data.bodyText)}`;
  let md = `# ${path}\n\n`;
  md += `- **Live URL:** https://chargenet.energy${path}\n- **HTTP status seen:** ${en.status}${['terms-and-conditions', 'trend2026', 'this-page-does-not-exist'].includes(slug) ? ' (soft 404: page body is the 404 component)' : ''}\n- **Final URL after client redirects:** ${en.finalUrl.replace('https://chargenet.energy', '')}\n- **Screenshots:** \`screenshots/${slug}.{en,nl}.{1440,390}.png\`\n- **Page height:** 1440px→${en.data.height}px (EN), ${nl.data.height}px (NL); 390px→${load(slug, 'en', 390)?.data.height}px (EN)\n- **Content fingerprint EN / NL:** ${ident}\n`;
  if (CANON[slug] && CANON[slug] !== slug && !['routecheck', 'opbrengst'].includes(slug)) md += `- **Alias of:** \`${CANON[slug]}.md\`. ${same ? 'Body text is byte-identical to the canonical page (verified).' : '**Body differs from canonical: see below.**'}\n`;
  if (['routecheck', 'opbrengst'].includes(slug)) md += `- **Redirect to:** ${en.finalUrl}\n`;
  md += `\n## Purpose\n\n${isBlogPost ? 'Single news article. Full export (body HTML EN/NL, images, meta): see `../blog/' + slug.slice(6) + '/`.' : (PURPOSE[pg] || PURPOSE[slug] || 'TODO')}\n`;
  if (!isBlogPost && pg === 'locations' || pg === 'carriers') md += `\nNote: the brief expects an API/map on this page; none exists (see \`../functionality.md\`).\n`;
  md += `\n## Sections (ordered, top to bottom)\n\nShared blocks (navbar, footer, contact cards) are documented in \`_shared-layout.md\`; they are only referenced here.\n`;
  en.data.sections.forEach((s, i) => {
    const t = classify(s, pg); const n = nl.data.sections[i];
    if (!['terms-and-conditions','trend2026','this-page-does-not-exist'].includes(slug)) addInv(t, isBlogPost ? 'blog/<slug>' : (['locaties','locatie','vervoerder','vervoerders','over-ons','privacy','routecheck','opbrengst'].includes(slug) ? null : pg), '');
    if (!isBlogPost || !['navbar', 'footer', 'contact-team-cards'].includes(t)) { /* keep */ }
    const shared = ['navbar', 'footer', 'contact-team-cards'].includes(t);
    md += `\n### ${i + 1}. ${t}${s.id ? ` (#${s.id})` : ''}\n\n- Element: \`<${s.tag}>\`, height ${s.height}px\n`;
    if (shared) { md += `- See \`_shared-layout.md\` (${t}).\n`; return; }
    md += `- Headings (EN): ${s.headings.map(x => `${x.l} "${x.t}"`).join(' · ') || '—'}\n`;
    if (n) md += `- Headings (NL): ${n.headings.map(x => `${x.l} "${x.t}"`).join(' · ') || '—'}\n`;
    const im = [...s.images.map(x => `${x.src.replace('https://chargenet.energy', '')} (alt "${x.alt}")`), ...s.bgImages.flatMap(x => [...x.matchAll(/url\("?([^")]+)"?\)/g)].map(m => 'bg: ' + m[1].replace('https://chargenet.energy', '')))];
    md += `- Images/backgrounds: ${im.length ? '\n' + [...new Set(im)].map(x => '  - ' + x).join('\n') : 'none'}\n`;
    if (s.videos.length) md += `- Video/iframe: ${JSON.stringify(s.videos)}\n`;
    const c = s.ctas.filter(x => x.t || x.href).map(x => `  - "${x.t}" → ${x.href ?? '(button, no href)'}${x.target ? ' [' + x.target + ']' : ''}`);
    md += `- CTAs/links: ${c.length ? '\n' + [...new Set(c)].join('\n') : 'none'}\n`;
    const it = Object.entries(s.interactive).filter(([, v]) => v).map(([k, v]) => `${k}=${v}`).join(', ');
    if (it) md += `- Interactive elements detected in DOM: ${it}\n`;
    md += `\n**Copy (EN)**\n\n${quote(s.text)}\n\n**Copy (NL)**\n\n${n ? quote(n.text) : '—'}\n`;
  });
  md += `\n## Interactive behaviour\n\n${(INTERACT[pg] || INTERACT[slug] || []).map(x => '- ' + x).join('\n') || '- No page-specific interaction beyond shared layout (below).'}\n- Shared layout behaviour: see \`_shared-layout.md\`.\n`;
  if (isBlogPost) md += `- Featured image is the hero background. Body rendered with EnhancedBlogContent: images either centered card (h=300px, object-contain) or floated left/right at 40% width.\n`;
  md += `\n## Forms\n\n`;
  const forms = en.data.forms;
  md += forms.length ? forms.map(f => `- form (method ${f.method}, action ${f.action.replace(/\?.*/, '')}) fields:\n${f.fields.filter(x => x.tag !== 'BUTTON').map(x => `  - ${x.tag.toLowerCase()} type=${x.type} name="${x.name}" label="${x.label ?? ''}" placeholder="${x.ph ?? ''}"`).join('\n')}`).join('\n') + `\n- Submit target: EmailJS from the browser (no first-party endpoint). Validation (zod): email format; honeypot field "website" must stay empty; min 3s since load; code/name/company rules as in Interactive behaviour.\n` : `- No form rendered on this page. (Contact form component exists in source: ContactForm.tsx: fields name (min 2), email (valid), message (min 10), honeypot, ≥3s timing check; submits via EmailJS from the browser; success/failure toast. It is not rendered on any crawled page.)\n`;
  md += `\n## Third-party scripts\n\n${[...new Set(en.data.scripts.map(x => new URL(x).hostname))].map(x => '- ' + x).join('\n')}\n`;
  if (CANON[slug] && !['routecheck','opbrengst'].includes(slug) && same) md = `# ${path}\n\n- **Live URL:** https://chargenet.energy${path}\n- **Alias of:** [\`${CANON[slug]}.md\`](${CANON[slug]}.md). Rendered body text is byte-identical in EN and NL (fingerprint ${ident}), same component, same <head> (canonical points to \`/\`, no hreflang).\n- **Screenshots:** \`screenshots/${slug}.{en,nl}.{1440,390}.png\`\n- **Why it exists:** paired Dutch/English slug in App.tsx and live sitemap. The slug does NOT switch language; language comes from localStorage.\n`;
  const file = `${D}/pages/${slug}.md`; fs.writeFileSync(file, md); pageDocs.push(slug);
}

// ---- shared layout ----
const home = load('home', 'en', 1440).data, homeNl = load('home', 'nl', 1440).data;
const sh = t => home.sections.find(s => s.tag === t || s.id === t), shn = t => homeNl.sections.find(s => s.tag === t || s.id === t);
let sm = `# Shared layout blocks\n\nPresent on (nearly) every page. Captured from \`/\`.\n\n## Behaviour\n\n${SHARED_INTERACT.map(x => '- ' + x).join('\n')}\n\n## Third-party scripts (site-wide)\n\n${THIRD.map(x => '- ' + x).join('\n')}\n`;
for (const [name, key] of [['navbar', 'nav'], ['contact-team-cards', 'contact-info'], ['footer', 'footer']]) {
  const s = sh(key), n = shn(key);
  sm += `\n## ${name}\n\n- Element \`<${s.tag}>\`, height ${s.height}px\n- Images: ${[...new Set(s.images.map(i => `${i.src.replace('https://chargenet.energy', '')} (alt "${i.alt}")`))].join('; ') || 'none'}\n- Links/CTAs:\n${[...new Set(s.ctas.map(c => `  - "${c.t}" → ${c.href ?? '(button)'}${c.target ? ' [' + c.target + ']' : ''}`))].join('\n')}\n\n**Copy (EN)**\n\n${quote(s.text)}\n\n**Copy (NL)**\n\n${quote(n.text)}\n`;
}
fs.writeFileSync(`${D}/pages/_shared-layout.md`, sm);

// ---- SEO baseline ----
let seo = `# SEO baseline (live site, rendered in Chromium)\n\nMethod: each URL rendered after client-side JS, EN and NL language state. Values are what a crawler executing JS sees; a non-JS fetch only sees the static \`index.html\` head (identical on every URL).\n\n## robots.txt (verbatim)\n\n\`\`\`\n${fs.readFileSync(`${D}/robots.txt`, 'utf8').trim()}\n\`\`\`\n\nNote: source repo's public/robots.txt additionally has \`Disallow: /downloads/\`; live does not.\n\n## sitemap.xml\n\nFull copy: \`sitemap.xml\` (in this folder). Live lists ${(fs.readFileSync(`${D}/sitemap.xml`, 'utf8').match(/<loc>/g) || []).length} URLs; lastmod dates are 2025 (stale). Problems:\n- Lists \`/terms-and-conditions\` → soft 404.\n- Lists only 9 of the 18 blog posts; lists none of /rapport2027.\n- Lists /locaties, /vervoerder, /over-ons as separate URLs but they are duplicates of /locations, /carriers, /about (identical content, no hreflang, canonical points to \`/\`).\n- Lists both /privacy and /privacy-policy (duplicate content).\n\n## Site-wide findings\n\n- **Language is not in the URL.** EN/NL is a localStorage flag; a crawler always gets English. No hreflang tags exist anywhere.\n- **Canonical is wrong**: every page (including blog posts, /about, /faq) has \`<link rel=canonical href="https://chargenet.energy/">\` (see table). Blog posts: see per-page row.\n- **Duplicate meta tags**: on every page that sets its own SEO via react-helmet (blog index, all 18 posts, /rapport2027; 63 duplicated tags counted) \`description\`, \`og:title\`, \`og:url\` appear twice: the static index.html value first, the page-specific value second. Parsers that take the first tag see the generic homepage values. Canonical is NOT duplicated and is the homepage on all 39 crawled URLs.
- **Structured data**: JSON-LD Organization on 2 pages, Organization + BlogPosting on all 18 posts, a Report object on /rapport2027, none elsewhere.\n- **Same title on most pages** ("ChargeNet · Keep Charging Ahead"): only blog list, blog posts and rapport2027 set their own.\n- **Soft 404s**: unknown URLs return HTTP 200.\n- \`og:image\` relative path \`/uploads/ChargeNet-icon.png\` in static head (not absolute).\n\n## Per page (EN state; NL differences noted)\n\n`;
const uniq = a => [...new Set(a)];
for (const slug of files) {
  const e = load(slug, 'en', 1440), n = load(slug, 'nl', 1440), d = e.data, dn = n.data;
  const m = k => (d.meta[k] || []).join(' ¦ ') || '—';
  seo += `### ${e.path}\n\n- **title:** ${d.title}${dn.title !== d.title ? `\n- **title (NL):** ${dn.title}` : ''}\n- **meta description:** ${m('description')}${(dn.meta.description || []).join('¦') !== (d.meta.description || []).join('¦') ? `\n- **meta description (NL):** ${(dn.meta.description || []).join(' ¦ ')}` : ''}\n- **canonical:** ${d.canonical ?? '—'}\n- **hreflang:** ${d.hreflang.length ? JSON.stringify(d.hreflang) : 'none'}\n- **html lang:** ${d.lang}${dn.lang !== d.lang ? ` (NL state: ${dn.lang})` : ''}\n- **robots:** ${m('robots')}\n- **Open Graph:** ${['og:title', 'og:description', 'og:image', 'og:url', 'og:type', 'og:locale'].map(k => `${k}=${m(k)}`).join(' | ')}\n- **Twitter:** ${['twitter:card', 'twitter:title', 'twitter:image'].map(k => `${k}=${m(k)}`).join(' | ')}\n- **keywords:** ${m('keywords')}\n- **structured data:** ${d.jsonld.length ? d.jsonld.map(j => { try { const o = JSON.parse(j); return '`' + (o['@type'] || 'graph') + '`'; } catch { return 'invalid JSON'; } }).join(', ') : 'none'}\n- **heading outline:**\n${d.headings.filter(x => !/^(Company|Get in Touch|Contact Us Today|Sebastiaan|Thijs|Piotr)/.test(x.t) && x.t).map(x => `  - ${x.l} ${x.t}`).join('\n') || '  (none)'}\n- **internal links (unique):** ${uniq(d.links.map(l => l.h).filter(x => x && x.startsWith('/'))).join(', ') || 'none'}\n- **external links (unique):** ${uniq(d.links.map(l => l.h).filter(x => x && /^https?:/.test(x))).join(', ') || 'none'}\n\n`;
}
seo += `## Structured data (full JSON-LD as rendered: one BlogPosting post + the Report page)\n\n\`\`\`json\n${['blog__move-east-2025','rapport2027'].map(k => (load(k,'en',1440).data.jsonld||[]).join('\n')).join('\n')}\n\`\`\`\n`;
fs.writeFileSync(`${D}/seo-baseline.md`, seo);

// ---- section inventory ----
const rows = Object.entries(inventory).map(([t, pgs]) => `| \`${t}\` | ${Object.keys(pgs).filter(x => x !== 'null').sort().join(', ')} |`).join('\n');
fs.writeFileSync(`out/section-inventory.skeleton.md`, `<!-- generated skeleton; variants filled by hand below -->\n# Section inventory\n\n| Section type | Pages using it |\n|---|---|\n${rows}\n`);
console.log('done', pageDocs.length, 'pages');
