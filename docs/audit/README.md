# ChargeNet site audit (discovery only, no WordPress code)

Audited 2026-09-30. Live https://chargenet.energy, bundle `index-BxIGrRbs.js`, matches the local clone `~/Sites/ev-truck-launch-site` HEAD `4c024cd` (same bundle hash in `dist/`). Tooling: `tools/audit/` (Playwright, `crawl.mjs`, `blog-export.mjs`, `gen*.mjs`; raw data in `tools/audit/out/`, node_modules git-ignored).

| Deliverable | Where |
|---|---|
| Page specs (39 URLs; aliases are stubs) + shared chrome | `pages/*.md`, `pages/_shared-layout.md` |
| Screenshots 1440 + 390, EN + NL (156) | `screenshots/` |
| Network audit Locations/Carriers | `functionality.md` |
| Blog export (18 posts: post.json, body.en/nl.html, images) | `blog/` |
| SEO baseline + robots + sitemap | `seo-baseline.md`, `robots.txt`, `sitemap.xml` |
| Design baseline + logo/favicons | `design-baseline.md`, `design/` |
| Section types | `section-inventory.md` |

## Key findings

1. **One React SPA, every URL returns HTTP 200.** Unknown URLs, `/terms-and-conditions` and `/trend2026` render a 404 body (soft 404).
2. **Language is not in the URL.** EN/NL is `localStorage.preferred-language` (default EN). `/locations` = `/locaties`, `/carriers` = `/vervoerder(s)`, `/about` = `/over-ons`, `/privacy` = `/privacy-policy`: identical content, no hreflang. I crawled every URL in both language states.
3. **Campaign page is `/rapport2027`, not `/trend2026`.** Component is named Trend2026; Dutch-only; bare URL redirects client-side to add UTM params. Shortlinks `/routecheck`, `/opbrengst`, `/rapport2027/download`, `/rapport2027/<code>` also redirect client-side.
4. **Locations and Carriers have no map, API, filter or form.** Static copy + a static PNG. The premise of task 4 does not hold; see `functionality.md`.
5. **No first-party backend at all.** Contact form and campaign lead form send email from the browser via EmailJS (keys exist in source; deliberately not copied). Contact form component is not rendered on any crawled page; "Contact Us" just scrolls to the footer `#contact`.
6. **SEO is weak:** canonical = homepage on all 39 URLs; same title/description on all non-blog pages; duplicated meta tags on Helmet pages; no hreflang; sitemap stale (23 URLs, 2025 dates, lists the soft-404 T&C page, only 9/18 posts, no `/rapport2027`, duplicate alias URLs).
7. **Live bugs worth not porting:** 4 home project cards link to 404s; mobile menu links `/location-owners` and `/fleet-managers` are 404; one blog post has a malformed section type that renders nothing; `og:image:secure_url`/JSON-LD logo point to `/img/ChargeNet-icon.png` (file lives in `/uploads/`); `cdn.gpteng.co` editor script ships to production; Calendly badge text is English-only.
8. **Blog:** 18 posts, all with EN+NL, 15 have an `externalUrl` (mostly LinkedIn) shown as "external article" link. Dates are free text ("Sept 1, 2025", "June 12, 2025"); no ISO dates, no per-post meta title (rendered as `<title> - ChargeNet`). Sitemap dates disagree with post dates for some posts.
9. **Design:** Source Sans 3; dark green `#083A0B` + Tailwind greens/greys; shadcn tokens are a greyscale default and mostly overridden.

## Not verified / limits

- Project carousel auto-rotation (source says yes, 7s probe saw no change).
- Typeform form contents and what the hero "Explore Use Cases" form collects (opens a third-party form; not submitted).
- Nothing was submitted to any form. `portal.chargenet.energy` (Login) is a separate authenticated app and was not touched.
- Terms & Conditions copy exists in the clone (`TermsAndConditions.tsx`, route disabled) but was not exported.

## Open questions for you

1. **Language URLs:** in WordPress, do you want real `/en/…` and `/nl/…` URLs (Polylang/WPML) replacing the paired slugs, with redirects from `/locaties`, `/vervoerder`, `/over-ons`? Current paired slugs do not actually switch language.
2. **Campaign URL:** keep `/rapport2027` (and its DM shortlinks/UTM redirects), or also create `/trend2026`? Is the campaign-code allowlist (source config) to be carried over, and where should codes live?
3. **Forms/back end:** replace EmailJS with WordPress-side forms (CF7/WPForms/Gravity)? Where should leads go (email, Pipedrive, which one)? Where does the report PDF live (`/downloads/` is disallowed in the repo robots but not on live)?
4. **Dead content:** should the 4 "projects" pages (source files exist, routes disabled) and Terms & Conditions be rebuilt, dropped or left out? Home currently links to the project pages.
5. **Blog:** import all 18 posts? Keep the LinkedIn `externalUrl` behaviour? "Connectr3" (author on one post) looks like a typo for "External: Connectr": confirm. Which date is authoritative where sitemap and post date differ?
6. **Consent:** the repo has a Silktide cookie banner but none rendered on live; is consent handling currently absent or done inside GTM? GA4/GTM: reuse the same property/container? (IDs not stored here on purpose.)
7. **Calendly badge and Login button:** keep both? Badge copy is English only.
8. **Partner logos** (`/uploads/partners/*`) and `map-connectr.png`, `CCS2-cable.jpg` exist in the repo but appear nowhere on live: intended for the new site?
9. **Contact details:** all three contact cards share `info@chargenet.energy`; confirm whether individual addresses/phones should be shown. Footer address: Industrial Park Kleefsewaard, Westervoortsedijk 73, 6827 AV Arnhem.
10. **Language default:** keep EN default with NL on user choice, or detect browser/geo (code exists but unused)?
11. **Repo state:** I used your local clone at HEAD `4c024cd` because `gh` was not installed and unauthenticated git could not see the private repo. It matches the live bundle, but say so if you want me to verify against a fresh `origin/main`.
12. **Tools path:** I used `./tools/audit` (project-relative), not the filesystem root `/tools/audit`.
