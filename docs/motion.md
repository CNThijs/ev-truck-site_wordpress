# Motion (GSAP and ScrollTrigger)

Animation is an enhancement. Every page must read and lay out correctly with JavaScript off, with reduced motion on, and before any script has run.

## Licence

GSAP is free for commercial use under the [standard licence](https://gsap.com/standard-license/) (effective 30 April 2025; Webflow owns GSAP). It covers all of GSAP including ScrollTrigger and the former members-only plugins such as DrawSVGPlugin and SplitText. No attribution is required. The one prohibited use is building GSAP into a tool that lets others make visual animations without code and competes with Webflow, which this site does not do. We bundle GSAP from npm (`gsap`); there is no CDN. No smooth-scroll library is used.

## Presets

An editor picks one per section in Section Settings → Animation. It is printed as `data-animation` on the section.

| Preset (`data-animation`) | What it does                                                                                                                                                                                                                                                                                                       | Targets                                                   |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ | --------------------------------------------------------- |
| `fade-rise`               | Fade in and rise a few pixels when scrolled into view. Skips the first section and anything in the first viewport.                                                                                                                                                                                                 | Every `[data-reveal]` inside the section                  |
| `stagger`                 | The same, but the items of a section appear one after another in the order they enter the viewport (default step 0.08 s).                                                                                                                                                                                          | Every `[data-reveal]` inside the section                  |
| `text-lines`              | Headings reveal line by line from a clipped edge (SplitText: real HTML text, an accessible name is kept, re-split on resize and when fonts load).                                                                                                                                                                  | Every `[data-split]` heading (all `chargenet_heading()`)  |
| `counters`                | Statistics count up from 0. The final figure is in the HTML and announced as such while counting.                                                                                                                                                                                                                  | `[data-count]` (Statistics numbers)                       |
| `parallax`                | The background image drifts slower than the page (scrub, from 48rem up). A hero at the top starts at rest.                                                                                                                                                                                                         | `[data-hero-media] img` (Hero and Statistics backgrounds) |
| `horizontal`              | On a Card Slider, vertical scroll moves the cards sideways while the section is pinned. Wide screens, mouse, no reduced motion only. At most 10 cards (`CHARGENET_HORIZONTAL_MAX_CARDS`): with more, 10 are picked at random on each render, original order kept; a page cache keeps one pick until it is cleared. | `.card-slider` track                                      |
| `draw`                    | SVG lines draw themselves: scrubbed with the scroll, or once on entering (`data-draw-mode="enter"`). The Steps connector line is an SVG path.                                                                                                                                                                      | `[data-draw]` paths, scoped by `[data-draw-scope]`        |

Sections without a preset never animate. The first section of a page is never animated by any preset except parallax (a hero at the top starts at rest), so the largest contentful paint is untouched. The starter patterns set sensible presets: parallax on the hero, counters on statistics, stagger on card grids and news, line drawing on steps, fade and rise on text sections.

### Page features (not tied to a section)

| Feature                                        | What it does                                                                                                                                                                       |
| ---------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Scroll progress bar (`[data-scroll-progress]`) | A thin bar at the very top fills as you read. Printed on posts and pages (`inc/head.php`), shown only on pages clearly longer than the window, plain JavaScript (0.3 KB, no GSAP). |
| Header hide on scroll (`header.js`)            | The header slides away while scrolling down and returns on scroll up. Not while a menu or submenu is open, while focus is inside the header, near the top, or with reduced motion. | Reduced motion switches every preset off: content is simply there. |

### Settings in markup

All presets read the same optional attributes from the element or any ancestor (so one wrapper can tune a group). Defaults come from the motion tokens in `tokens.json` (`--duration-slow`, `--distance-md`).

| Attribute              | Unit    | Default                                                | Used by                                                |
| ---------------------- | ------- | ------------------------------------------------------ | ------------------------------------------------------ |
| `data-motion-delay`    | seconds | 0                                                      | `fade-rise`, `draw`                                    |
| `data-motion-duration` | seconds | `--duration-slow`                                      | `fade-rise` (`draw` plays at twice)                    |
| `data-motion-distance` | pixels  | `--distance-md` (24px)                                 | `fade-rise`; `parallax` (48 = about 8 %)               |
| `data-motion-stagger`  | seconds | 0 for `fade-rise`, 0.08 for `stagger` and `text-lines` | items of one `[data-reveal-group]`, lines of a heading |

Editors only choose the preset; these attributes are set in code (a block's `render.php` or a template).

## Architecture

```
assets/src/js/
  main.js                    imports styles, header.js and motion/core.js
  motion.js                  reads motion tokens (duration, distance), reduced-motion check
  motion/core.js             tiny, on every page: finds [data-animation], lazy-loads the matching presets
  motion/runtime.js          GSAP + ScrollTrigger + DrawSVG, layout watching, settings(); only in lazy chunks
  motion/presets/<name>.js   one lazy module per preset
assets/src/scss/base/_motion.scss   CSS start states
```

`core.js` is about 1.5 KB gzipped. Pages without `[data-animation]` never download GSAP. A preset module default-exports `(sections, { reduced, signal }) => cleanup`.

## Progressive enhancement

- The early inline script in `inc/head.php` adds `html.js` and, after 4 seconds, sets `html[data-motion="timeout"]` as a safety net.
- `_motion.scss` hides `fade-rise` targets (opacity and transform only, so no layout shift) only under `html.js`, only without `prefers-reduced-motion: reduce`, only until `html[data-motion]` is set, and never inside the first section (`.is-first-section`, set by `chargenet_section_open()`).
- `core.js` sets `data-motion="active"` once the presets own the start states (inline GSAP styles), or `"failed"` if a chunk does not load. Either releases the CSS hiding. GSAP clears its inline styles when a tween completes.
- Without JavaScript nothing is hidden: no `js` class, no rule applies.

## Accessibility

- `prefers-reduced-motion: reduce` (and the Motion Lab simulation) turns every preset off; the page is static.
- Only opacity and transform move. Nothing needed to read the page is hidden for long: reveals take about 0.5 s and run once. Scrubbed animations follow the scroll and stop with it.
- Keyboard: focusing a not yet revealed element reveals it at once (`focusin`). Focus order is the DOM order; transforms never reorder it.
- No motion plays on its own for more than 5 seconds. There is no looping or auto-play. If one is ever added it needs a visible pause control.
- The card slider has no auto-rotation. The `horizontal` preset leaves vertical scrolling in charge (keyboard Page Down works as usual) and tabbing to a card scrolls the page to it; it is off on touch and narrow screens, where the slider stays a swipeable row.

## Performance

- GSAP is bundled locally and only loaded on pages with animated sections (dynamic `import()`).
- Transforms and opacity only; no `will-change` left on idle elements.
- `runtime.js` refreshes ScrollTrigger after web fonts (`document.fonts.ready`), `window.load` and any change of the page height (a debounced `ResizeObserver` on `body`, which covers late images). ScrollTrigger itself handles resize and orientation change; presets use `gsap.context` / `gsap.matchMedia`, whose `revert()` removes every trigger and inline style when the preference changes.
- Budget (gzip, `bin/budget.json`): initial JavaScript 6 KB (every page); motion JavaScript 50 KB for the heaviest single preset including the shared GSAP runtime (about 45 KB; `text-lines` adds SplitText, about 48 KB). A page pays only for the presets it uses; the progress bar costs 0.3 KB. `npm run check:budget` checks the Vite build and runs in CI after `npm run build`. Raise a number only with a reason in the commit message.

## SEO safety

- No text exists only because of JavaScript: all copy is in the server HTML. `text-lines` only wraps lines (and reverts), `counters` leaves the final figure in the HTML until the count starts, and an `aria-label` carries the real text while it changes.
- Nothing in the first section or first viewport is hidden or moved, so the largest contentful paint is never delayed or changed. Check with Lighthouse or the Performance panel when you add a preset.
- Start states use opacity and transform, so they cannot cause layout shift (CLS stays 0).

## Motion Lab

Administrators get `/motion-lab/` (others a 404, noindex). It shows every preset with sample content, a **Simulate reduced motion** switch (sets `html[data-motion-simulate="reduce"]`, which CSS and JS both honour), a **Replay animations** button and a status line. Add a specimen to `page-templates/motion-lab.php` when you add a preset.

## Add a preset

1. Create `assets/src/js/motion/presets/<name>.js` that default-exports `(sections, { reduced, signal }) => cleanup`. Return a no-op when `reduced` is true. Import GSAP only through `../runtime.js` (`setup()` returns `{ gsap, ScrollTrigger }`, `settings(el)` reads the data attributes). Create tweens inside `gsap.context()` (or `gsap.matchMedia()`) and return `() => context.revert()`. Use transforms and opacity only; skip anything in the first section or first viewport.
2. Register it in `presets` in `motion/core.js`: `name: () => import('./presets/<name>.js')`.
3. Add the option to `animations` in `blocks/_shared/section.js` (and a Dutch translation: `npm run i18n`, edit the `.po`, `npm run translations`).
4. If it needs a CSS start state, add it to `base/_motion.scss` behind `html.js:not([data-motion], [data-motion-simulate='reduce'])` and the `no-preference` media query, and never for `.is-first-section`.
5. Add the target hooks to the blocks that should support it (a `data-…` attribute in `render.php`, with the same markup in the editor `edit()` where it affects layout).
6. Add a specimen to the Motion Lab, run `npm run build && npm run check:budget`, and test: reduced motion on, JavaScript off, keyboard tabbing through the section, a slow connection.
7. Document it in the table above and in `docs/sections.md`.
