import { request } from 'playwright';
import fs from 'fs';
import path from 'path';
import { pathToFileURL } from 'url';
const { blogPosts } = await import(pathToFileURL(process.env.HOME + '/Sites/ev-truck-launch-site/src/data/blogPosts.ts').href);
const OUT = '../../docs/audit/blog';
const ctx = await request.newContext();
const esc = s => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
const imgs = new Set();
const img = (u) => { if (u) imgs.add(u); return u; };
const cell = c => Array.isArray(c) ? c.join(' ') : (c ?? '');
function toHtml(sections) {
  return sections.map(s => {
    switch (s.type) {
      case 'heading': return `<h2>${s.content}</h2>`;
      case 'subheading': return `<h3>${s.content}</h3>`;
      case 'paragraph': return `<p>${s.content}</p>`;
      case 'quote': return `<blockquote>${s.content}</blockquote>`;
      case 'list': return `<ul>\n${(s.items || []).map(i => `  <li>${i}</li>`).join('\n')}\n</ul>`;
      case 'image': { const d = s.imageData || {}; const u = d.url || d.src; img(u);
        const cap = [d.title, d.subtitle, d.caption].filter(Boolean).join(' — ');
        return `<figure data-align="${esc(d.align || '')}"><img src="${esc(u)}" alt="${esc(d.alt || '')}">${cap ? `<figcaption>${cap}</figcaption>` : ''}</figure>`; }
      case 'table': { const t = s.tableData; return `<table>\n<thead><tr>${t.headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>\n<tbody>\n${t.rows.map(r => `<tr>${r.map(c => `<td>${c}</td>`).join('')}</tr>`).join('\n')}\n</tbody>\n</table>`; }
      case 'icon-list': return (s.items?.rows || []).map(([t, d, l]) => `<div class="icon-item" data-icons="${esc((s.items.icons||[]).join(','))}"><p><strong>${cell(t)}</strong></p><p>${cell(d)}</p>${Array.isArray(l) ? `<ul>${l.map(i => `<li>${i}</li>`).join('')}</ul>` : ''}</div>`).join('\n');
      default: return `<!-- NOT RENDERED on live (unknown section type "${s.type}"; likely intended as a subheading): ${s.content ?? ''} -->`;
    }
  }).join('\n');
}
const index = [];
for (const p of blogPosts) {
  imgs.clear();
  const dir = `${OUT}/${p.slug}`; fs.mkdirSync(dir, { recursive: true });
  img(p.imageUrl);
  const html = { en: toHtml(p.content.en), nl: toHtml(p.content.nl) };
  fs.writeFileSync(`${dir}/body.en.html`, html.en + '\n');
  fs.writeFileSync(`${dir}/body.nl.html`, html.nl + '\n');
  const files = [];
  for (const u of imgs) {
    if (!u) continue;
    const abs = u.startsWith('http') ? u : 'https://chargenet.energy' + u;
    const r = await ctx.get(abs);
    if (r.ok()) { const fn = path.basename(new URL(abs).pathname); fs.writeFileSync(`${dir}/${fn}`, await r.body()); files.push({ url: u, file: fn, bytes: (await r.body()).length }); }
    else files.push({ url: u, error: r.status() });
  }
  const meta = {
    id: p.id, slug: p.slug, url: `https://chargenet.energy/blog/${p.slug}`, date: p.date, author: p.author,
    title: p.title, category: p.category, excerpt: p.excerpt, keywords: p.keywords || [],
    metaTitle: { en: `${p.title.en} - ChargeNet`, nl: `${p.title.nl} - ChargeNet` }, // as rendered by SEO component; verify in seo-baseline
    metaDescription: p.metaDescription || null, externalUrl: p.externalUrl || null,
    featuredImage: p.imageUrl || null, images: files,
    sectionTypes: [...new Set([...p.content.en, ...p.content.nl].map(s => s.type))],
  };
  fs.writeFileSync(`${dir}/post.json`, JSON.stringify(meta, null, 2));
  index.push({ id: p.id, slug: p.slug, date: p.date, author: p.author, category: p.category.en, externalUrl: p.externalUrl || null, images: files.length, failed: files.filter(f => f.error).length });
  console.log(p.id, p.slug, files.map(f => f.error ? 'ERR' + f.error : 'ok').join(','));
}
fs.writeFileSync(`${OUT}/index.json`, JSON.stringify(index, null, 2));
