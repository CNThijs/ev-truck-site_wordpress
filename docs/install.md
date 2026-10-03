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

| Plugin                       | When       | Notes                       |
| ---------------------------- | ---------- | --------------------------- |
| Polylang                     | now        | Languages and translations. |
| Rank Math SEO (free)         | later epic | SEO.                        |
| A free cookie-consent plugin | later epic | Not chosen yet.             |

Install from Plugins → Add New (search the name) and activate.

## Settings to apply

- Settings → Permalinks: Post name (`/%postname%/`).
- Settings → General: site language and title.
- Settings → Reading: choose the homepage and posts page once those pages exist.
- Polylang → Languages: add English (default) and Dutch (local DDEV does this automatically; the live site needs it done by hand). Language URLs as directory (`/nl/…`) is the default.
- Appearance → Menus: create a menu per language for each location (Primary, Header utility, Footer, Footer legal) and assign each in the Manage Locations tab; with Polylang active the tab has one column per language. Local DDEV seeds English and Dutch starter menus with placeholder URLs.
