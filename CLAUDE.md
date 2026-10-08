# ChargeNet website (WordPress)

New WordPress site for https://chargenet.energy replacing a React SPA. Audit of the old site: `docs/audit/` (read before content or SEO work). Owner hosts it on STRATO Hosting Basic (PHP 8.4, SSH, Git, WP-CLI), installs WordPress by hand and controls DNS.

## Decisions (confirmed)

- WordPress + custom theme `chargenet`. No page-builder plugin. No ACF.
- Sections are native Gutenberg blocks rendered in PHP (server-side).
- English + Dutch with free Polylang. Rank Math (free, installed, no account) for SEO. A free cookie-consent plugin. GSAP + ScrollTrigger for animation.
- Free plugins only. No paid plugin or licence anywhere.
- Language URLs: `/en/` (default) and `/nl/`, both prefixed; `/` redirects to `/en/`; no browser-language redirect. Settings live in `bin/setup-polylang.php`. Dutch uses u/uw.
- Local env: DDEV (Docker). CSS: SCSS, component partials. Bundler: Vite. Node 22, PHP 8.4 target, code compatible with 8.2+.
- Repo: https://github.com/CNThijs/ev-truck-site_wordpress. CI: GitHub Actions.
- No deployment or hosting automation. Ever. Delivery is a theme zip the owner uploads.

## Commands

| Command                         | What                                                                                                          |
| ------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| `npm ci && ddev start`          | Local site at https://chargenet.ddev.site (admin / admin, local only)                                         |
| `npm run dev`                   | Vite dev server with hot reload (port 5273, theme reads `themes/chargenet/hot`) plus block rebuilds on change |
| `npm run build`                 | Tokens, block build (`build/blocks`) and hashed Vite build (`assets/dist`)                                    |
| `npm run make:section <name>`   | Scaffold a new section block                                                                                  |
| `npm run lint`                  | PHPCS, ESLint, Stylelint, Prettier check                                                                      |
| `npm run format`                | Prettier write                                                                                                |
| `npm run package`               | `dist/chargenet-<version>.zip`                                                                                |
| `ddev wp …` / `ddev composer …` | WP-CLI / Composer in the container                                                                            |

`ddev start` also creates the Polylang languages (en default, nl). Pre-commit hook (`.githooks`, set by `npm install`) runs lint-staged.

## Structure

```
composer.json            plugin manifest (wpackagist, free only) + PHPCS dev tools
.ddev/                   local environment
bin/setup-wp.sh          idempotent local setup run by ddev start
bin/package.sh           theme zip
web/                     docroot; only wp-content/themes/chargenet is versioned
  wp-content/themes/chargenet/
    functions.php        requires only; logic in inc/
    inc/                 small includes (setup, assets)
    template-parts/      partials
    assets/src/{js,scss} sources; assets/dist is build output (ignored)
    blocks/              section block sources; build/blocks is build output (ignored)
    patterns/            starter page patterns
docs/                    install.md, audit/
```

Not committed: WordPress core, uploads, vendor plugins, `vendor/`, `node_modules/`, `dist/`.

## Design system (direction A, "Deep Green")

- Source of truth: `web/wp-content/themes/chargenet/tokens.json`. `npm run tokens` (also run by `dev` and `build`) generates `assets/src/scss/_tokens.generated.scss` (CSS custom properties) and `_breakpoints.generated.scss` (`$breakpoints`) and mirrors tokens into `theme.json`. Never edit generated files by hand; commit them. CI fails on drift. It also fails if a contrast pair in `tokens.json` drops below WCAG AA.
- Palette: dark forest green `#083A0B` surfaces, logo yellow `#FAE104` as accent on dark, green `#0F5F1A` for interactive elements on light. The logo is yellow/orange and only readable on dark: header and footer are always `is-dark`.
- Section variants `is-light`, `is-paper`, `is-dark` set the semantic variables (`--bg`, `--fg`, `--accent`, `--btn-*`, `--focus`). Components read those, not raw colours.
- Type: Source Sans 3, self-hosted variable woff2 (Latin + Latin Extended) in `assets/fonts/`, declared inline in `inc/fonts.php` with preload and a metric-matched fallback. Weights: 700 headings, 400 body, 600 labels, 300 stat numbers. No Google Fonts.
- Motion tokens live in CSS and collapse under `prefers-reduced-motion`; JS reads them via `assets/src/js/motion.js`. Presets (fade-rise, parallax, draw) are chosen per section in Section Settings; reveal targets carry `data-reveal`. System, budget (`npm run check:budget`, CI) and admin-only Motion Lab (`/motion-lab/`): `docs/motion.md`.
- Style guide: `/style-guide/`, administrators only (everyone else gets 404, noindex). Update `page-templates/style-guide.php` when adding a component.
- Menus: locations `primary`, `utility` (Login), `footer`, `legal`. `bin/setup-wp.sh` seeds English starter menus with placeholder URLs.

## Sections (blocks)

Every page section is a dynamic block in `web/wp-content/themes/chargenet/blocks/<name>/` (block.json apiVersion 3, `render.php`, `index.js`, `style.scss`), built by `@wordpress/scripts` into `build/blocks/` (git-ignored). Vite builds only the global theme assets. Shared settings, the PHP wrapper (`chargenet_section_open/close`), page restrictions and Polylang notes: `docs/sections.md`. New section: `npm run make:section <name>`. Keep `render.php` and the editor `edit()` markup identical. Library (hero, feature grid and columns, stats, steps, accordion, team, logo strip, card slider, post grid, plus the Epic 3 sections): fields and variants in `docs/sections.md`. Admin-only Section Gallery at `/section-gallery/` renders every variant with sample content: add a section's variants to `inc/gallery-samples.php`. Images go through `chargenet_image()`, icons through `chargenet_the_icon()`. Posts can carry an original source URL (`inc/post-source.php`).

## Content

Pages, media and menus are code: `content/` (copy of both languages side by side, media in `content/media/`) built by `bin/seed-content.php` (run by `ddev start`). Rules, how to add a page and the app-badge files: `docs/content.md`. Dutch texts that were added or translated are listed in `docs/translation-review.md`: update it with every page.

## News (blog)

18 posts in both languages, six categories, an author reference per post (default ChargeNet), no comments, newsletter sign-up stored in WordPress, Rank Math (free, no account) for SEO and structured data. URLs, templates, patterns, checks: `docs/blog.md`. `npm run test:blog`, `npm run check:blog-urls`.

## SEO

Rank Math free (structured data, breadcrumbs, 404 monitor; the sitemaps are theme code, `inc/sitemap.php`, one per language; its redirect manager has no CSV import, so redirects are theme code in `inc/redirects.php`) plus `inc/seo.php`: canonical fix for News, noindex for paged lists and search, staging protection through `WP_ENVIRONMENT_TYPE` (anything but `production` and `local` is noindex and `Disallow: /`), Organization details, default share image. Page titles and descriptions: `content/seo.php`. Setup, structured data, checks and the Search Console / Bing launch checklist: `docs/seo.md`. `npm run check:seo -- <url> --production`, `npm run check:redirects`, `npm run check:hreflang`.

## Forms and email

Contact form and trend report form: handler, private submissions post type, report codes table (CSV import), emails through Microsoft 365 (OAuth SMTP, secrets only as constants in wp-config.php), privacy exporter/eraser and 12-month retention. All in the theme (`inc/forms/`); how it works, DNS, setup and checks: `docs/integrations.md`. `npm run test:forms`. Never put credentials in the repository; never write a BCC address into a visible header.

## Consent and tracking

WPConsent (free) banner, texts in `inc/consent.php`, settings in `bin/setup-wpconsent.php`. Tag Manager loads only after statistics consent (`CHARGENET_GTM_ID` in wp-config.php), campaign cookie and a 36-month consent log in `inc/tracking.php`. Details and checks: `docs/tracking.md`.

## Performance

Lighthouse mobile baseline, measured changes, budgets and Search Console/CrUX guide: `docs/performance.md`. `npm run perf` (per-template numbers, `--check` against `bin/perf-budget.json`, run in CI), `npm run check:budget`. Production page cache: Cache Enabler (`bin/setup-cache.php`), server rules in `docs/htaccess.md`; neither is active in DDEV. Pages must stay cacheable: nothing visitor-specific in server-rendered HTML (forms get campaign fields and a fresh token from JavaScript).

## Languages

English is the default content language and Dutch is always provided; more languages will follow. Every user-facing string, pattern and default text needs an English and a Dutch version. UI strings use gettext (`chargenet` domain); `npm run translations` compiles `.po` to `.mo` and editor `.json`. Workflow: `docs/translations.md`.

## Open questions from the audit

Planned for later epics: see `docs/audit/README.md` (language URLs, campaign `/rapport2027`, forms and lead storage, dead content, blog import, consent, Calendly/Login, redirects).

## Coding standards

- PHP: WordPress Coding Standards (`phpcs.xml.dist`), prefix `chargenet_`, text domain `chargenet`, escape late, PHP 8.2 compatible.
- JS: ESLint recommended, ES modules. SCSS: stylelint-config-standard-scss, one partial per component. Prettier for the rest. Tabs.
- Prefer server-rendered HTML; JS only for enhancement and animation. Respect `prefers-reduced-motion`. Animation system, presets, budget and Motion Lab (`/motion-lab/`): `docs/motion.md`.
- Enqueue assets only through `inc/assets.php` (Vite manifest).

## Rules

- Ask when unsure. Do not assume anything the owner did not say.
- Free plugins only. Add plugins to `composer.json` (wpackagist) and `docs/install.md`.
- Never commit secrets (keys, passwords, EmailJS keys from the old site, `.env`). The local admin/admin is local only.
- Keep the theme free of page-builder plugins and dependencies on them.
- Do not build deployment or hosting automation.
