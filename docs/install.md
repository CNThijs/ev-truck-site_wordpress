# Install on STRATO Hosting Basic

You install and maintain WordPress yourself. This repo ships only the theme zip; nothing here deploys for you.

## Requirements

- PHP 8.4 on STRATO (the theme runs on PHP 8.2 and up). Set it in the STRATO customer panel.
- WordPress 6.6 or newer (the theme header says `Requires at least: 6.6`).
- Free plugins only. No paid plugin or licence.

## Build the theme zip

The machine that builds the zip needs Node 22 or newer. To change translations it also needs `msgfmt` from gettext (`brew install gettext` on macOS, `sudo apt install gettext` on Debian/Ubuntu). STRATO needs neither.

```sh
npm ci
npm run package
```

Output: `dist/chargenet-<version>.zip` (built, hashed assets; no sources, no dev files).

## Install the theme

WordPress admin → Appearance → Themes → Add New → Upload Theme → choose the zip → Activate.
Or upload the extracted `chargenet/` folder to `wp-content/themes/` over SFTP.

## Plugins to install (free)

| Plugin               | When    | Notes                                                                                                                                                                                                         |
| -------------------- | ------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Polylang             | now     | Languages and translations.                                                                                                                                                                                   |
| Rank Math SEO (free) | Epic 9  | SEO: titles, descriptions, Open Graph, Twitter cards, article structured data, sitemap. No Rank Math account needed: `bin/setup-rankmath.php` skips the registration step and switches off the other modules. |
| WPConsent (free)     | Epic 11 | Cookie banner and cookie list. After activating run `wp eval-file bin/setup-wpconsent.php` (after the content is seeded). Details: `docs/tracking.md`.                                                        |
| Cache Enabler (free) | Epic 12 | Page cache, **production only** (leave it inactive in DDEV). Steps below.                                                                                                                                     |
| Two Factor (free)    | Epic 13 | Authenticator-app login; the theme requires it for administrators and editors. Activate on production only (DDEV keeps it off). Each user sets it up under Users → Profile. `docs/security.md`.               |

Install from Plugins → Add New (search the name) and activate. After activating Rank Math run `wp eval-file bin/setup-rankmath.php` (settings, no Rank Math account, the extra modules off).

**Page cache (production, after the site works):** 1) add the rules from `docs/htaccess.md` to `.htaccess` and run its checks; 2) install and activate Cache Enabler; 3) add `define( 'WP_CACHE', true );` to `wp-config.php` above the "stop editing" line; 4) run `wp eval-file bin/setup-cache.php` (10-hour lifetime, cache cleared on every save). Also add the Tag Manager ID constant described in `docs/tracking.md`. Check the server first with `bin/check-host.php` (`docs/performance.md`).

## Settings to apply

- Settings → Permalinks: Custom structure `/blog/%postname%/` (posts live under `/blog/`, pages stay at the top level).
- Settings → General: site language and title.
- Settings → Reading: choose the homepage and posts page once those pages exist.
- Polylang: with WP-CLI over SSH, run `wp eval-file bin/setup-polylang.php` from the repo (needs the file on the server; copy just that one file). It creates English (default, `en_US`) and Dutch (`nl_NL`) and sets every option below. By hand, in Polylang → Languages and Settings: add the two languages; URL modifications "The language is set from the directory name in pretty permalinks", "Remove /language/ in pretty permalinks" on, "Hide URL language information for default language" **off**, "The front page URL contains the language code" on, "Detect browser language" **off**, "Media translation" on; Synchronisation: featured image and publication date. Then run `wp eval-file bin/seed-content.php` to create the pages, media, front page and menus (see `docs/content.md`). Then Settings → Permalinks → Save (flushes rewrites). The bare `/` then redirects to `/en/`.
- Settings → Reading: a static front page per language (Polylang uses the translation of the page you pick). `bin/seed-pages.php` creates linked English and Dutch "Home" pages locally.
- Appearance → Menus: create a menu per language for each location (Primary, Header utility, Footer, Footer legal) and assign each in the Manage Locations tab; with Polylang active the tab has one column per language. Local DDEV seeds English and Dutch starter menus with placeholder URLs.
