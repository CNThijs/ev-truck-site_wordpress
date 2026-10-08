# Content: pages, media and menus

The site's pages, media and menus are code. One command rebuilds them in a fresh environment, in English and Dutch, linked as Polylang translations.

```sh
ddev wp eval-file bin/seed-content.php          # create or update
ddev wp eval-file bin/seed-content.php force    # also overwrite pages edited in the editor
```

`ddev start` runs it (via `bin/setup-wp.sh`) after `bin/setup-polylang.php`. On a fresh STRATO install run the same two scripts with WP-CLI over SSH (see `docs/install.md`); copy the `bin/` and `content/` folders next to WordPress for that, they are not in the theme zip.

## Where things live

```
content/
  blog.php           the news posts import (see News posts below)
  blog/<slug>/       one post of the old site: post.json, body.en.html, body.nl.html and its images
  lib.php            block markup helpers (cn_block, cn_p, cn_list, ...) and shared sections (contact cards, app badges)
  media.php          media key => file in content/media, title, alt text per language ('' alt = decorative)
  media/             the image files (committed; WordPress makes the WebP/AVIF sizes on import)
  menus.php          menus per language and location
  pages/index.php    page keys in build order
  pages/<key>.php    one page: slugs and titles per language, and a build() closure with the copy of both languages
  pages/_audience.php  shared layout of Carriers and Locations
bin/seed-content.php the seeder
```

A page file keeps English and Dutch copy side by side in one array and builds the same sections for both, so the structure can never drift apart. Copy, structure and animation presets are all set there.

## How the seeder behaves

- **Idempotent.** Pages are found by the meta key `_chargenet_seed_key`; media by `_chargenet_seed_media`. Nothing is duplicated.
- **Editor edits are safe.** The seeder stores a hash of the content it wrote. A page whose content no longer matches (someone edited it in the editor) is skipped with a warning, unless you pass `force`.
- **Two passes.** Pass 1 creates the pages, pass 2 fills in links between them (stored as relative paths, so they survive a domain change). A link to a page that does not exist yet is simply left out.
- **Media.** Every image is imported once per language (Polylang media translation is on), so alt text can be Dutch. Missing files are skipped; the sections that need them print nothing. SVG is allowed during the seed run only.
- **Menus** are rebuilt every run from `content/menus.php`. A menu item for a page that does not exist yet, and a parent without children, are skipped.
- **Front page:** the English Home page; Polylang serves its Dutch twin at `/nl/`.

## App store badges

Apple and Google ask for their official badge artwork, so the files are not generated. Put them in `content/media/` as `app-store-badge.svg` and `google-play-badge.png` and rerun the seeder; the Home page then shows the badge strip. Until then that strip is left out.

## Add a page

1. Create `content/pages/<key>.php` like `carriers.php` (slugs, titles, build closure).
2. Add the key to `content/pages/index.php`; add media to `media.php` and the page to `menus.php` if needed.
3. Run the seeder, check both languages, run `npm run check:hreflang`.

## Copy rules

- Dutch is the live copy from `docs/audit/pages/`. Where a Dutch text was missing or still English on the live site, it was translated and is listed in `docs/translation-review.md` for review.
- Obvious English typos are fixed; claims and figures (statistics, savings percentages, ISO 27001 wording, team bios, partner and funder names) are kept exactly.
- New Dutch text uses `u/uw`.

## Slugs

Every page needs a different slug in English and Dutch (except Home): Polylang cannot tell two pages or posts of one slug apart when it looks up the page, so the Dutch URL would redirect to the English page. The seeder stops with an error when two languages share a slug.

## News posts

`content/blog.php` (called by the seeder) creates the 18 posts of the old site in both languages, linked as Polylang translations, with category per language, featured image, text as core blocks (paragraphs, headings, lists, quotes, tables, images), the excerpt, the date and the original source URL (`_chargenet_source_url`, the "Read the original on ..." link). The Dutch slug is the English slug plus `-nl`. The SEO title and description go into the Rank Math meta keys (`rank_math_title`, `rank_math_description`) so they are in place when that plugin is installed. Not imported: the "author" label (for example "External: Connectr"; "Connectr3" on one post looks like a typo, still open) and the keyword list. The category of each post comes from `content/blog/_categories.php` (six categories, not the old seven); the author reference becomes the post meta `_chargenet_author` (empty for ChargeNet). The full description of the blog: `docs/blog.md`. The editor can change the posts afterwards: a post edited in the editor is skipped on the next run unless you pass `force`.

News index: the page with key `blog` ("News"/"Nieuws") is the posts page. The theme's `home.php` prints its content: a title band and a Post Grid with paging (`paginate` attribute, 9 per page).

Single posts: `template-parts/content-single.php` (title band with category and date, featured image, text, source link, link back to the news).

## Redirects from the old site

`inc/redirects.php` (in the theme) sends the old URLs to the new ones with a 301: the paired slugs (`/locations`, `/locaties`, `/about`, `/over-ons`, `/faq`, `/privacy`, ...) go to the language they were written in, `/blog/<slug>` goes to the English post (when it exists), the project pages to the new project pages, and the campaign shortlinks (`/rapport2027`, `/rapport2027/<code>`, `/rapport2027/download`, `/routecheck`, `/opbrengst`) go to the Dutch pages with the same tracking parameters as the old site. Old URLs that never worked (the Terms page, the old mobile menu links) are not redirected. Add a line to the map when you add a page that the old site had.

## Contact page, report PDF and downloads

The Contact page (`content/pages/contact.php`, slugs `contact` / `contact-opnemen`) holds the contact form; the header's "Contact Us" button goes to it. The trend report page holds the trend report form (see `docs/integrations.md`). Files that are too big for git go in `content/downloads/` (ignored by git); the seeder copies them to `wp-content/uploads/chargenet-downloads/`, and the theme serves the report at `/downloads/ChargeNet-TR2027.pdf`. On a fresh server put the PDF there by hand (or copy the folder and run the seeder).

## Changing the hero background image

- **In the editor (live site):** open the page, select the Hero block, and in the block's sidebar use **Background image → Replace**. Pick or upload the new image and update the page. The image is resized and converted automatically (WebP, or AVIF when the server supports it), with all `srcset` sizes up to 2048 px, and it is preloaded in the page head. No code change is needed.
- **In the repository (seeded content):** replace the file in `content/media/` (for example `bg-truck.png`) and run `bin/seed-content.php`. The seeder skips pages edited in the editor unless you pass `force`.
- **Which image:** landscape, about 2400 × 1350 px (at least 1600 px wide), JPEG or PNG, preferably under 1.5 MB before upload. Keep the subject away from the left side, where the text sits, and leave room at the top and bottom: the photo is cropped to fill the section (and drifts slightly with the parallax animation). A dark overlay covers it; choose "Strong" for busy photos. The background is decorative, so it needs no alt text.
- After a change, `npm run perf` should keep the home page LCP under 2500 ms; the original upload stays in the media library, so delete unused originals to save disk space.
