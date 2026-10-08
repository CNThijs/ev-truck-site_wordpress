# Accessibility (Epic 13)

**Target: WCAG 2.2 level AA** on every public page, English and Dutch. Until it is confirmed whether a legal requirement applies (for example the European Accessibility Act through a customer contract), this is a quality target and no public accessibility statement is published.

## What is built in

- Skip link to `#main` on every page; one `<main>`; one `h1` per page (the Hero block); heading order checked by the editor warnings and `npm run check:seo`.
- Visible focus on every interactive element (`:focus-visible`, colours from the design tokens, contrast checked by `npm run tokens`). Anchor targets keep clear of the sticky header (`scroll-padding-top`).
- Colour contrast: every foreground/background pair in `tokens.json` is checked against WCAG AA in CI.
- `prefers-reduced-motion`: animations collapse to instant states; content is never hidden without JavaScript (`html.js` gates the start states). The card slider never moves by itself: it is a scrolling row with buttons and arrow keys.
- Forms: labels tied to inputs, hints and errors linked with `aria-describedby`, errors announced (`role="alert"`) in a message above the form, focus moves to the first invalid field, nothing relies on colour alone, the same fields work without JavaScript, autocomplete attributes on name and email.
- `lang`: `<html lang="en-US">` / `lang="nl-NL"` follows the page (Polylang). A passage in the other language inside a page must be wrapped in `lang="en"` or `lang="nl"` (use the HTML view of the block: `<span lang="en">…</span>`).
- Tables from the editor are focusable regions with unique names and no empty header cells (`inc/accessibility.php`).
- Tables, sliders and the cookie banner/panel are tested by axe in both states.
- Target size: buttons and header controls are at least 44 px high (`2.75rem`); the WCAG 2.2 minimum is 24 × 24 px. Inline text links are smaller and rely on the 24 px spacing exception; verify on a phone (manual test 5).

## Automated check (CI)

`npm run check:a11y [baseUrl]` runs axe-core with the WCAG 2.0/2.1/2.2 A and AA rules plus best practices on every sitemap URL, both language homes and the 404 page, in a 390 px wide mobile viewport, with the cookie banner open and with the cookie settings panel open. It fails on any violation. Last run: 81 pages, 0 violations. `--only <text>` limits it to matching URLs.

Automated tests find roughly a third of real problems. They cannot judge whether alt text makes sense, whether focus order is logical, or whether a screen reader reads a page well. That is what the manual script is for.

## Manual test script

Run it before launch, after every new section type or template, and once a year. Record date, tester, browser, result.

**Pages to test:** Home, Carriers, Locations, a blog post, the contact page (form), the Dutch report page `/nl/rapport2027/` (form), the cookie banner, the cookie settings panel, the 404 page. In English and Dutch.

### 1. Keyboard only (no mouse)

1. Reload the page, press **Tab** once: a "Skip to content" link appears. **Enter** moves focus to the main content.
2. Tab through the whole page. Check: the order follows the visual order; every link, button, field and slider control gets a **visible** focus ring; nothing is skipped; focus never gets trapped (except inside the cookie panel, which must trap focus and close with **Esc**).
3. Open and close the mobile menu (Enter/Space, Esc). The language switcher and the footer links work.
4. Card slider: reach it with Tab, scroll with the arrow keys and with the buttons; the section's links stay reachable.
5. Accordion: Enter/Space opens and closes, state is announced.
6. Forms: fill in and send with the keyboard only. Submit once empty: focus moves to the error message, each field says what is wrong. Submit correctly: the success message is announced and focus is sensible.
7. Cookie banner: reachable immediately after load, Accept and Reject are equal in prominence, settings can be changed and saved by keyboard, **Cookie settings** in the footer reopens it.

### 2. Screen reader

Use VoiceOver on macOS (Safari: Cmd+F5) and, if available, NVDA on Windows with Firefox or Chrome.

1. Landmarks (VoiceOver rotor, `Ctrl+Option+U`; NVDA `D`): banner, navigation (each with a name), main, contentinfo. No duplicate names.
2. Headings list: one `h1`, logical order, no skipped levels.
3. Links list: every link makes sense out of context (no "click here", no repeated "Read more" without a name).
4. Images: informative images have useful alt text, decorative ones are ignored. The hero background must be silent.
5. Forms: each field announces its label, required state and hint; errors are read when they appear.
6. Language: on Dutch pages the voice stays Dutch; any English passage switches voice (needs `lang="en"`).
7. Dynamic parts: accordion state, slider region name, cookie panel opens with focus inside, closes with focus back on the trigger.
8. Tables (privacy policy): the table is announced as a region with a name; header cells are read with each cell.

### 3. Zoom, reflow and text

1. Browser zoom **200 %** and **400 %** (equal to a 320 px wide screen): no horizontal scrolling of the page (tables and code may scroll inside their own region), nothing overlaps or is cut off, the header menu still works.
2. Text spacing: apply the "text spacing" bookmarklet (line height 1.5, letter 0.12 em, word 0.16 em, paragraph 2 em): no text is clipped or overlapping.
3. Browser minimum font size set to large: the layout still holds.

### 4. Motion, colour and contrast

1. Turn on **Reduce motion** (macOS: Accessibility → Display; Windows: Animation effects off): reload; no parallax, no reveal animation, no moving counters; everything is visible at once.
2. Windows **High contrast / forced colors**: text, buttons, focus rings and form borders are still visible.
3. Greyscale: nothing depends on colour alone (errors have text and icon, links are underlined or otherwise marked).

### 5. Touch

On a phone: all buttons and links can be hit without zooming (at least 24 × 24 px, buttons 44 px), the slider can be swiped, the form keyboards match the field type (email, text).

## Fixing and recording

- Fix what fails, add an axe-visible test case if the problem is machine-detectable (extend `bin/check-a11y.mjs`), and record the date and result here.
- Known gaps are listed below; remove them when fixed.

| Date | Tester | Browser / reader | Result | Open items |
| ---- | ------ | ---------------- | ------ | ---------- |
|      |        |                  |        |            |

### Known gaps

- No human screen-reader test has been done yet.
- Accessibility statement: not published (see the top of this file).
- Any English passage inside a Dutch page (and the other way round) must be marked with `lang`; no automated check can find these. Review when content is added.
