# Sections (Gutenberg blocks)

Every page section is a dynamic block registered from the theme and rendered in PHP. No ACF, no page-builder plugin. The HTML is complete on the server; JavaScript only enhances it.

## Layout of the code

```
web/wp-content/themes/chargenet/
  blocks/                      sources (not shipped in the zip)
    _shared/section.js         shared inspector panel + helpers
    <section-name>/
      block.json               apiVersion 3, category chargenet-sections
      render.php               server render (the real output)
      index.js                 editor: edit() + save()
      style.scss               front end AND editor styles
      editor.scss              editor-only styles (optional)
      view.js                  front-end script module (optional)
  build/blocks/<name>/         built by @wordpress/scripts (git-ignored, shipped in the zip)
  inc/section.php              chargenet_section_open/close(), chargenet_heading()
  inc/section-attributes.json  shared attribute definitions (read by PHP and the editor build)
  inc/blocks.php               category, auto-registration, page restrictions, pattern category
  patterns/*.php               starter page patterns
```

`inc/blocks.php` registers every `build/blocks/*/block.json` automatically. Run `npm run build` (or `npm run dev`, which rebuilds blocks on change) after adding or editing a block. If `build/blocks` is missing, wp-admin shows a warning.

## Shared section settings

Every top-level section gets these attributes without declaring them (merged in by the `block_type_metadata` filter):

| Setting             | Attribute                                                                                                | Output                                                                                           |
| ------------------- | -------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------ |
| Background          | `sectionBackground`: light, paper, dark                                                                  | class `is-light` / `is-paper` / `is-dark` on the section                                         |
| Space above / below | `spaceTop`, `spaceBottom`: none, sm, md, lg                                                              | `data-space-top`, `data-space-bottom`                                                            |
| Visibility          | `hideOn`: none, mobile, desktop                                                                          | class `hide-mobile` / `hide-desktop` (front end only, so hidden sections stay editable)          |
| Animation           | `animation`: `fade-rise`, `stagger`, `text-lines`, `counters`, `parallax`, `draw`, `horizontal` or empty | `data-animation="…"` and the class `is-first-section` on the first section; see `docs/motion.md` |
| Anchor ID           | native `supports.anchor`                                                                                 | `id="…"`                                                                                         |

A block can override a default by declaring the attribute itself (the CTA band defaults to dark). To add a shared setting: add it to `inc/section-attributes.json`, to the inspector in `blocks/_shared/section.js`, and to `chargenet_section_open()` in `inc/section.php`.

Child blocks (blocks with a `parent`, like `chargenet/button`) are not sections and get no shared settings.

## PHP wrapper

```php
chargenet_section_open( $attributes, array( 'name' => 'faq', 'labelledby' => $title_id ) );
// ... markup ...
chargenet_section_close();
```

This prints `<section id class="section is-… section--faq" data-… aria-labelledby>` and `<div class="container">`. Options: `name`, `labelledby` (id of the heading), `label` (aria-label when there is no heading), `container` (`narrow` or `wide`), `class`. Use `chargenet_heading( $html, $level, $id )` for headings: levels are limited to h2 to h4 because the page title is the only h1. Sections with a heading let the editor pick the level (h2 by default), so the outline stays correct when a section sits under another.

## Create a section

```sh
npm run make:section faq-list          # add --view for a front-end script module
npm run dev                            # or: npm run build
```

This creates `blocks/faq-list/` from `bin/templates/section/`. Then:

1. Edit `block.json`: title, description, attributes, `example` (the inserter preview renders it live with the real theme styles, so there is no static preview image to go stale).
2. Edit `render.php` (real output) and `index.js` (editor). Keep the markup and class names identical so the editor shows what visitors see.
3. Style in `style.scss` with design tokens (`var(--space-*)`, `var(--color-*)`) and the section variables (`--bg`, `--fg`, `--accent`) so it works on light, paper and dark.
4. Add a `chargenet/<name>` block to the starter patterns if it belongs on every page of a kind.

### Repeated items (cards, steps, FAQ entries)

Use InnerBlocks with a small child block (see `blocks/button`: `parent` lists the sections it may sit in, `save: () => null`, server-rendered). Editors add, remove and reorder items directly in the editor. For simple fixed lists, an array attribute edited with `RichText` also works; prefer InnerBlocks for anything with rich content.

## Section library

Built in batches; each section is in the [Section Gallery](#section-gallery) with every variant. Fields are edited in the block (text) and the inspector (settings). Items are child blocks you add, remove and reorder in the editor.

| Section (`chargenet/…`) | Fields                                                                                                                            | Variants                                                                               | Empty state                            |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------- | -------------------------------------- |
| `hero`                  | eyebrow, heading (the page's h1), intro, background image, overlay (standard/strong), cover image + alt (campaign), buttons       | `banner` (background image), `title-band` (text only), `campaign` (text + cover image) | No heading: nothing is printed         |
| `feature-grid`          | eyebrow, heading, intro, columns (2 to 4); items (`feature-grid-item`): icon, title, text, optional link URL and label            | `cards`, `plain`, `numbered` (CSS counter)                                             | No items: nothing is printed           |
| `feature-columns`       | eyebrow, heading, intro; columns (`feature-column`): title, sub-headings and lists, optional link                                 | 2 or 3 columns (by number of columns, wraps on small screens)                          | No columns: nothing is printed         |
| `stats`                 | eyebrow, heading, text, optional background image; items (`stat-item`): number, text before and after, label                      | `row` (heading above), `with-text` (text beside)                                       | No items: nothing is printed           |
| `steps`                 | eyebrow, heading, intro; steps (`step-item`): title, text and lists                                                               | `horizontal` (side by side, dashed connector), `vertical` (timeline)                   | No steps: nothing is printed           |
| `accordion`             | eyebrow, heading, intro, FAQ structured data toggle, help box (heading, text, button); items (`accordion-item`): question, answer | `single`, `with-aside` (help box beside the list)                                      | No items: nothing is printed           |
| `team`                  | eyebrow, heading, intro; people (`team-member`): photo + alt, name, role, short bio (one paragraph per line), email, LinkedIn     | `cards` (portrait photos), `compact` (small round photos, contact cards)               | No people: nothing; no photo: initials |
| `logo-strip`            | eyebrow, heading, intro; logos (`logo-item`): image, name (alt text), optional link                                               | `grayscale` (colour on hover, white on dark sections), `colour`                        | No logos: nothing is printed           |
| `card-slider`           | eyebrow, heading, intro; cards (`slide-card`): image + alt, title, text, optional link URL and "read more" label                  | `image-bg` (text over a dark overlay), `image-top`                                     | No cards: nothing is printed           |
| `post-grid`             | eyebrow, heading, intro, number of posts (1 to 12), category, optional "view all" label and URL (default: the posts page)         | `latest` (equal cards), `featured-grid` (first post across the row)                    | No posts: nothing is printed           |
| `rich-text`             | heading, text blocks (paragraph, heading, list, quote, table)                                                                     |                                                                                        |                                        |
| `rich-text-image`       | eyebrow, heading, text blocks, image + alt override, image position                                                               | image left or right                                                                    |                                        |
| `cta-band`              | heading, text, buttons                                                                                                            | background dark (default), light, paper                                                |                                        |
| `contact-form`          | eyebrow, heading, intro; fields name, email, message, consent (fixed)                                                             |                                                                                        | The form always prints                 |
| `trend-report-form`     | eyebrow, heading, intro; code + email, or (no code) name + company + email; consent (fixed)                                       |                                                                                        | The form always prints                 |
| `locations-list`        | eyebrow, heading, intro, maximum number; the entries come from Locations in the admin menu                                        |                                                                                        | No locations: nothing is printed       |
| `newsletter-signup`     | eyebrow, heading, intro; email + consent (fixed); subscriptions stored in WordPress                                               |                                                                                        | The form always prints                 |
| `post-filter`           | category links (of the page's language), search box (whole site); toggles for each                                                |                                                                                        | No categories: only the search box     |

Every section also has the shared settings above. Items with `data-reveal`, lists with `data-reveal-group` and numbers with `data-count` are hooks for the animation epic: nothing depends on them.

### Notes per section

- **Accordion:** built on native `<details>`/`<summary>`: keyboard operable, no JavaScript, all closed on load. The FAQ structured data toggle prints `FAQPage` JSON-LD from the items; leave it off when an SEO plugin already does it.
- **Steps:** the number is a decorative CSS counter on a real ordered list, so assistive technology announces the numbering once. Buttons are not part of the section; put a `cta-band` after it.
- **Team:** photos are treated as decoration (the name is printed beside them) unless the editor fills in the alt text. Email and LinkedIn are icon links with visually hidden text. Without a photo the initials are shown.
- **Logo strip:** the organisation name is the alt text of the logo (translate it per language page); a linked logo opens in a new tab and says so for screen readers. Add logos by adding Logo items; with none, the section prints nothing.
- **Card slider:** a scroll-snap row that works by scrolling, swiping and keyboard (the row is a focusable region). `view.js` shows previous and next buttons only when the cards overflow; there is no auto-rotation. The title is the card's one link and covers the whole card; the "read more" label is a visual cue. With the `horizontal` animation preset a slider shows at most 10 cards (10 random ones when there are more). Project detail pages arrive in Epic 15, until then the link field takes any URL.
- **Post grid:** dynamic. `chargenet_post_grid_items()` (`inc/post-grid.php`) turns the newest posts of the current language into a plain array, and `render.php` only prints that array; the Section Gallery swaps in sample posts through the `chargenet_post_grid_items` filter. Categories belong to one language, so pick the Dutch category on the Dutch page. The editor preview is the real server output. The cards always link to the post itself; the original source is shown on the post (see below).
- **Post grid:** attribute `exclude` (a post id) leaves one post out (related news); card markup is `template-parts/post-card.php`.
- **Post grid paging:** with the `paginate` attribute (set on the News page, no editor control) the grid follows the page being viewed (`/blog/page/2/`) and prints page links.
- **Section intro:** new sections print their eyebrow, heading and introduction through `chargenet_section_header()` (PHP) and `SectionHeaderFields` (`blocks/_shared/header.js`, editor), so the two stay identical.

- **Forms:** the two form sections are dynamic and print real forms (the editor preview is not clickable). How they work, the spam protection, emails, storage and privacy: `docs/integrations.md`.
- **Locations list:** dynamic; `chargenet_locations_items()` (`inc/locations.php`) turns the Locations of the current language into a plain array.

### Heading levels

Only `hero` prints an h1 (its own heading field; use one per page). Other sections let the editor pick h2 to h4; item titles inside a section are one level below the section heading, capped at h4.

### Images

Always use `chargenet_image( $attachment_id, $args )` (`inc/image.php`): it prints `srcset`, `sizes`, `width` and `height` from the media library and sets `loading`. Images in the first section on the page load eagerly with `fetchpriority="high"`, all others lazy; the hero background always counts as first-viewport. Uploads are resized to WebP (PNG and JPEG) or AVIF (JPEG, when the server's image library can write it; STRATO has to be checked) by the `image_editor_output_format` filter. Background images are decorative (`alt=""`); content images take the media item's alt text, or the section's alt override.

### Icons

`chargenet_the_icon( 'zap' )` prints an inline, `aria-hidden` SVG from the built-in set in `inc/icons.json` (outline icons from Lucide, ISC licence; the same file feeds the editor icon picker). Add an icon by adding its inner SVG markup to that file. Icons are decoration: the item always has visible text.

## Original source of a post

News posts can link to the article, LinkedIn post or report they are based on. Editors fill in **Source URL** in the "Original source" box on the post screen (`inc/post-source.php`, meta key `_chargenet_source_url`, also readable and writable through the REST API for the blog import). The single post prints "Read the original on <site>" under the text, opening in a new tab and saying so for screen readers. Polylang copies the URL when a translation is created, it is not kept in sync afterwards. The audit's `externalUrl` of the 15 imported posts maps to this field.

## Section Gallery

Administrators get every section and variant with sample content at `/section-gallery/` (everyone else a 404, noindex). It renders through the same block code as a real page, so it is the place to check a new variant. Add a section's variants to `inc/gallery-samples.php` when you add the section. The first visit generates three sample images with PHP GD and stores them in the media library as "ChargeNet gallery sample" (delete them whenever; they are recreated on the next visit).

## Which blocks editors can use

On **pages** (landing pages are ordinary pages) the inserter offers only `chargenet/*` sections. Paragraph, heading, list and quote exist only inside sections: `chargenet_allowed_blocks()` (`inc/blocks.php`) gives them a `parent` rule listing the section blocks, so they are never offered at the root of a page. Child blocks such as `chargenet/button` are placed from inside their section. Posts keep the normal editor.

Starter patterns are in the pattern inserter under "ChargeNet pages": Standard page and Landing page, each in English and in Dutch (`patterns/page-*.php` and `patterns/page-*-nl.php`). Add a Dutch twin whenever you add an English pattern. Pattern text is literal, not translated by the editor's UI language, so an English page never gets Dutch placeholder text.

## Assets

Each block's `style` and optional `viewScriptModule` are declared in `block.json`, so WordPress loads them only on pages that contain the block. `style.scss` is also loaded in the editor; `editor.scss` only there. Global styles (tokens, base, components) come from the main bundle and the editor stylesheet.

Critical CSS: hook `chargenet_critical_css` (filter, returns CSS) prints an inline `<style id="chargenet-critical">` in the head. Nothing uses it yet.

## Polylang

Block content is stored in the post, so **each translation is its own page**:

1. Edit the English page, or create it in the page list.
2. In the language column of the page list click the "+" for Dutch (or use the Languages box in the page sidebar). Polylang creates a linked draft.
3. To start from the English layout, use "Copy content to translation" in the Languages box, then translate the text of each section.
4. Translate section settings only if they should differ (usually not), and the **anchor IDs** if you link to them (anchors can stay the same).

Media: "Media translation" is on (`bin/setup-polylang.php`), so each language has its own copy of a media item with its own title, caption and alt text. Use "Create translation" in the media library, or the Languages box when editing an item. A section stores the attachment ID, so on the Dutch page pick the Dutch copy of the image; its alt text is then Dutch. The **Alt text override** field in a section still wins over the media item.
