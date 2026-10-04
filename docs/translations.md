# Languages and translations

English is the default content language; Dutch is always provided; more languages will follow (Polylang).

## Two different things

- **Content** (pages, posts, sections): each language is its own page, linked by Polylang. See "Polylang" in `docs/sections.md`.
- **Theme and editor strings** (menus labels, footer text, button labels, block inspector labels): gettext, text domain `chargenet`, in `web/wp-content/themes/chargenet/languages/`. Front-end strings follow the page language (Polylang switches the locale); editor labels follow the editor's own user language (Users > Profile).

## Add or update strings

1. Write strings in English with `__( 'Text', 'chargenet' )` (PHP) or `__( 'Text', 'chargenet' )` from `@wordpress/i18n` (block JS). Do not translate content defaults such as the starter button label; those are content, not UI.
2. `npm run i18n` regenerates `languages/chargenet.pot` (needs `ddev start`).
3. Merge into each language: `msgmerge -U languages/chargenet-nl_NL.po languages/chargenet.pot`, then fill in the new `msgstr` entries.
4. `npm run translations` compiles every `languages/chargenet-<locale>.po` into the `.mo` (PHP) and one `.json` per block editor script (editor). Commit the generated files. Needs `msgfmt` (`brew install gettext`).

## Add a language

Copy `languages/chargenet.pot` to `languages/chargenet-<locale>.po` (for example `fr_FR`), set the `Language:` header, translate, run `npm run translations`, add the language in Polylang, and create Dutch-style twins of the starter patterns for it.

## URL structure and checks

- Every language is prefixed: `/en/…` (default) and `/nl/…`. The bare `/` redirects to `/en/`. No automatic redirect by browser language. Dutch pages have Dutch slugs (`/nl/over-ons/`), posts live at `/en/blog/<slug>/` and `/nl/blog/<slug>/`.
- Settings are code, not clicks: `bin/setup-polylang.php` (languages, locale codes, options). Change a setting there and rerun it (`ddev wp eval-file bin/setup-polylang.php`).
- hreflang: Polylang prints the alternates (self link included) for every page that has a translation; `inc/hreflang.php` sets `x-default` to the English version of the same page. `<html lang>` follows the page (`en-US`, `nl-NL`). The canonical is the page's own language URL.
- Check a running site: `npm run check:hreflang [baseUrl]`. It crawls the language homes and the core sitemaps and fails when an alternate does not link back, a page lacks its self link, `x-default` is wrong or `<html lang>` does not match.
- Old URLs: `docs/redirects.csv` maps every old root-level URL of the React site to its new `/en/` or `/nl/` URL (301). Applied in Epic 14, not before. Rows marked PROVISIONAL wait on open questions.
- New Dutch text addresses the reader formally (u/uw), like most of the live copy.
- Left in English on purpose: design-token labels from `theme.json` (colour, size and spacing names, editor only) and the theme header URI and description.
