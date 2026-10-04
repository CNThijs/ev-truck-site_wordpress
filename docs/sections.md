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

| Setting             | Attribute                                                             | Output                                                                                  |
| ------------------- | --------------------------------------------------------------------- | --------------------------------------------------------------------------------------- |
| Background          | `sectionBackground`: light, paper, dark                               | class `is-light` / `is-paper` / `is-dark` on the section                                |
| Space above / below | `spaceTop`, `spaceBottom`: none, sm, md, lg                           | `data-space-top`, `data-space-bottom`                                                   |
| Visibility          | `hideOn`: none, mobile, desktop                                       | class `hide-mobile` / `hide-desktop` (front end only, so hidden sections stay editable) |
| Animation           | `animation`: free string, provisional presets in `_shared/section.js` | `data-animation="…"` (no behaviour yet)                                                 |
| Anchor ID           | native `supports.anchor`                                              | `id="…"`                                                                                |

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
