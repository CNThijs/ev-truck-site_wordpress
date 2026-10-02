# ChargeNet website (WordPress)

New WordPress site for https://chargenet.energy replacing a React SPA. Audit of the old site: `docs/audit/` (read before content or SEO work). Owner hosts it on STRATO Hosting Basic (PHP 8.4, SSH, Git, WP-CLI), installs WordPress by hand and controls DNS.

## Decisions (confirmed)

- WordPress + custom theme `chargenet`. No page-builder plugin. No ACF.
- Sections are native Gutenberg blocks rendered in PHP (server-side).
- English + Dutch with free Polylang. Rank Math (free) for SEO. A free cookie-consent plugin. GSAP + ScrollTrigger for animation.
- Free plugins only. No paid plugin or licence anywhere.
- Local env: DDEV (Docker). CSS: SCSS, component partials. Bundler: Vite. Node 22, PHP 8.4 target, code compatible with 8.2+.
- Repo: https://github.com/CNThijs/ev-truck-site_wordpress. CI: GitHub Actions.
- No deployment or hosting automation. Ever. Delivery is a theme zip the owner uploads.

## Commands

| Command                         | What                                                                             |
| ------------------------------- | -------------------------------------------------------------------------------- |
| `npm ci && ddev start`          | Local site at https://chargenet.ddev.site (admin / admin, local only)            |
| `npm run dev`                   | Vite dev server with hot reload (port 5273) (theme reads `themes/chargenet/hot`) |
| `npm run build`                 | Hashed production build to `assets/dist`                                         |
| `npm run lint`                  | PHPCS, ESLint, Stylelint, Prettier check                                         |
| `npm run format`                | Prettier write                                                                   |
| `npm run package`               | `dist/chargenet-<version>.zip`                                                   |
| `ddev wp …` / `ddev composer …` | WP-CLI / Composer in the container                                               |

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
docs/                    install.md, audit/
```

Not committed: WordPress core, uploads, vendor plugins, `vendor/`, `node_modules/`, `dist/`.

## Design tokens

`theme.json` holds a starter palette and Source Sans 3 (self-hosted via `@fontsource-variable/source-sans-3`), taken from `docs/audit/design-baseline.md`. Provisional until the new design is decided.

## Open questions from the audit

Planned for later epics: see `docs/audit/README.md` (language URLs, campaign `/rapport2027`, forms and lead storage, dead content, blog import, consent, Calendly/Login, redirects).

## Coding standards

- PHP: WordPress Coding Standards (`phpcs.xml.dist`), prefix `chargenet_`, text domain `chargenet`, escape late, PHP 8.2 compatible.
- JS: ESLint recommended, ES modules. SCSS: stylelint-config-standard-scss, one partial per component. Prettier for the rest. Tabs.
- Prefer server-rendered HTML; JS only for enhancement and animation. Respect `prefers-reduced-motion`.
- Enqueue assets only through `inc/assets.php` (Vite manifest).

## Rules

- Ask when unsure. Do not assume anything the owner did not say.
- Free plugins only. Add plugins to `composer.json` (wpackagist) and `docs/install.md`.
- Never commit secrets (keys, passwords, EmailJS keys from the old site, `.env`). The local admin/admin is local only.
- Keep the theme free of page-builder plugins and dependencies on them.
- Do not build deployment or hosting automation.
