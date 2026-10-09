# QA checklist for the owner (Epic 14)

What the machine already checks, and what only you can check. Do the first run **locally** before the maintenance window and the server-only parts **inside the window**, behind the gate (`docs/launch-runbook.md`, phase C), and tick off every line. Record date, who, and the browser or phone.

## 1. What the automated tests cover (run them, read the result)

| Command (against the site under test)                                                         | Covers                                                                                                                                                                                                                                                                                                                                                                                                                                                                             |
| --------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `E2E_BASE_URL=<url> npm run test:e2e`                                                         | Every page of both languages in Chromium (Chrome, Edge), Firefox and WebKit (Safari) at 390, 820 and 1440 px: loads, one h1, no console errors, no failed requests, images load, no sideways scrolling, language, canonical and hreflang links; cookie consent behaviour (nothing from Google before a choice, accept/reject, campaign cookie, consent record); contact form with test data and its dataLayer events; broken links and images; structured data and hreflang pairs. |
| `npm run test:e2e:visual`                                                                     | Full-page screenshots of 15 pages at 3 widths against the saved reference images (Chromium on the Mac).                                                                                                                                                                                                                                                                                                                                                                            |
| `npm run check:a11y -- <url>`                                                                 | axe-core on every page, with the cookie banner and panel open.                                                                                                                                                                                                                                                                                                                                                                                                                     |
| `npm run check:seo -- <url> --production`, `check:hreflang`, `check:redirects -- <url>`       | Titles, descriptions, canonicals, redirects (one hop each), hreflang.                                                                                                                                                                                                                                                                                                                                                                                                              |
| `npm run check:security -- <url> --production`, `wp eval-file check-hardening.php production` | Headers, closed endpoints, hardening baseline.                                                                                                                                                                                                                                                                                                                                                                                                                                     |
| `npm run perf -- <url>`                                                                       | Lighthouse mobile numbers per template.                                                                                                                                                                                                                                                                                                                                                                                                                                            |

Notes: Edge uses the same engine as Chrome; the suite runs that engine ("Chromium"), a separate Edge run is not needed. The forms test only submits on a local site (a test on any other site would send real email). The first empty submit of a form within three seconds of loading the page gets "that was very fast" instead of field errors; that is the spam check, not a bug.

## 2. Content proofreading (you; read every page in both languages)

For each page below: spelling, names (ChargeNet, ChargeBase, Maxem...), numbers and dates, links go where the link text says, buttons say what happens, Dutch uses u/uw, no English left on a Dutch page (and the other way round, unless intended), no lorem ipsum or placeholder text, no "#" links. `docs/translation-review.md` lists every Dutch text I wrote or changed; read those first.

- [ ] Home (EN, NL) — hero, both audience blocks, statistics (the captions do not match the figures; kept on purpose), news block
- [ ] Carriers / Vervoerders
- [ ] Locations / Locaties
- [ ] About / Over ons — team names, titles, LinkedIn links, board of advisors
- [ ] Careers / Carrière, FAQ / Veelgestelde vragen (all 12 questions), Security / Beveiliging
- [ ] The four project pages (EN, NL)
- [ ] Contact / Contact opnemen — addresses, phone numbers, map or address block
- [ ] Trend report 2027 / Rapport 2027 (NL) — form, text, download
- [ ] Privacy Policy / Privacybeleid and Cookie Policy / Cookiebeleid (legal read: `docs/privacy.md`)
- [ ] The 18 news posts in both languages: title, date, category, image and alt text, links in the text, "original source" link
- [ ] Footer and menus in both languages: every link, legal links, "Cookie settings" button, language switch lands on the same page in the other language
- [ ] 404 page in both languages

## 3. Forms and email (you; use real mailboxes)

Use your own address, never a customer's. Delete the test submissions afterwards (WordPress admin → Form submissions).

- [ ] Contact form EN: send. The team mailbox gets the message; the sender gets nothing unexpected; Reply goes to the sender.
- [ ] Contact form NL: same, Dutch texts.
- [ ] Trend report form (NL) **without a code**: the visitor mail arrives with the download link; the link opens the PDF; BCC copy arrives at `info@`; the visitor mail shows **no** Bcc header (View source / Show original).
- [ ] Trend report form **with a valid code** and with an **invalid code**: right result and message.
- [ ] Newsletter sign-up: confirmation mail arrives; signing up twice does not send twice.
- [ ] Mail authentication: Show original in Gmail: SPF, DKIM and DMARC all PASS.
- [ ] Spam: submit with JavaScript off (the form still works, errors appear on the page).
- [ ] A mail failure shows in the admin as "retry/failed" (only test if you can break the mail setting safely).
- [ ] Campaign link `/rapport2027`, `/routecheck`, `/opbrengst`, a personal code link `/rapport2027/<code>`: each lands on the right Dutch page, the form stores the campaign values (admin → submission).

## 4. Consent and tracking (you; Tag Assistant and GA4 DebugView)

The container only loads where `CHARGENET_GTM_ID` is set. On the live domain during the window, set it for this session only (`docs/launch-runbook.md`, C5) and keep test hits out of the reports (GA4 DebugView, or delete the test day). Use a normal browser window with the extension "Tag Assistant" (tagassistant.google.com → connect the site), and GA4 → Admin → DebugView.

- [ ] First visit: the banner shows; **no** Google request before a choice (browser DevTools → Network, filter "google").
- [ ] **Reject all**: still no Google request; no `_ga` cookie; the choice is remembered on reload.
- [ ] **Accept all**: the Tag Manager container loads; GA4 `page_view` arrives in DebugView; Consent Mode shows analytics granted.
- [ ] Footer "Cookie settings" reopens the panel; withdrawing consent stops new GA hits.
- [ ] Form events in DebugView: `form_start`, `form_submit`, `form_error` with `form_id` and `form_language`, and **no** name, email or message text in any event.
- [ ] Campaign: open a page with `?utm_source=test&utm_medium=test&utm_campaign=test`, accept, check GA4 shows the source.
- [ ] Your own GA4 filters/internal traffic settings are in place so that your tests do not count.

## 5. Animation and layout on real phones (you)

Use your own iPhone and an Android phone, in Safari and Chrome, on mobile data as well as Wi-Fi.

- [ ] Home page hero loads quickly; text is readable over the photo.
- [ ] Scroll the whole home page: reveals happen once, smoothly, nothing jumps or stays invisible; the parallax photo moves a little, not more.
- [ ] Counters count up when they scroll into view and end on the right number.
- [ ] Card slider: swipe works, the buttons work, the page does not scroll sideways.
- [ ] Menu: opens, closes, language switch is reachable, Login button works.
- [ ] Forms: the right keyboard appears (email), fields are not covered by the keyboard, the cookie banner does not hide the Send button.
- [ ] Rotate the phone: nothing breaks. Zoom the text to the largest size: nothing is cut off.
- [ ] Turn on **Reduce motion** in the phone settings: the site shows everything at once, no animation.
- [ ] A slow check: set the phone to "Low Power Mode" and scroll the home page: still smooth enough.

## 6. Accessibility (you or a helper)

Follow `docs/accessibility.md` (keyboard, screen reader, zoom).

## 7. Sign-off

| Area                     | Date | By  | Result   |
| ------------------------ | ---- | --- | -------- |
| Automated tests          |      |     |          |
| Content EN               |      |     |          |
| Content NL               |      |     |          |
| Forms and email          |      |     |          |
| Consent and tracking     |      |     |          |
| Phones and animation     |      |     |          |
| Accessibility (manual)   |      |     |          |
| **Gate 1: go to launch** |      |     | yes / no |
