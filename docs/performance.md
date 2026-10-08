# Performance and Core Web Vitals (Epic 12)

## How we measure

`npm run perf` (`bin/perf.mjs`) runs Lighthouse 13 with the **mobile profile** (Moto G Power, simulated slow 4G, 4x CPU slowdown) against the local DDEV site, three runs per page, median reported. Pages: Home `/en/`, Carriers, Locations, a blog post, and the Dutch campaign page `/nl/rapport2027/`.

- `npm run perf -- --out file.json` saves numbers; `--compare docs/perf-baseline.json --check` exits 1 on a budget breach (`bin/perf-budget.json`) or a regression of more than 15% against the baseline.
- Needs `npm run build` and `ddev start`. Chrome must be installed.
- **Read the numbers as relative.** Network and CPU are simulated, but the server is local: TTFB is ~70 ms here and will be higher on shared hosting. There is no staging site on STRATO, so a real baseline comes after launch (CrUX and Search Console, see below). Lab numbers catch regressions; they do not predict field numbers.
- Not available locally: Brotli/HTTP2 behaviour of the real server and the page cache. The redirect hops in the waterfall (http/https, trailing slash) are a DDEV artefact.

## Baseline (2026-10-08, before any Epic 12 change)

| Template     | Score | LCP ms | FCP ms | TBT ms |   CLS | Weight KB | JS KB | Requests |
| ------------ | ----: | -----: | -----: | -----: | ----: | --------: | ----: | -------: |
| Home         |    95 |   2761 |   1932 |      0 | 0.000 |     201.5 |  58.4 |       17 |
| Carriers     |    99 |   1957 |    967 |      0 | 0.000 |     267.8 |  55.4 |       14 |
| Locations    |    99 |   2110 |    962 |      0 | 0.000 |     267.9 |  55.4 |       14 |
| Blog post    |   100 |   1354 |    957 |      0 | 0.000 |      88.0 |  10.0 |       11 |
| Landing (NL) |    98 |   2255 |   1119 |      0 | 0.000 |     243.3 |  56.4 |       16 |

Targets (Google "good", 75th percentile in the field): LCP ≤ 2500 ms, CLS ≤ 0.1, INP ≤ 200 ms. Raw numbers: `docs/perf-baseline.json` (includes the LCP element and render-blocking files per page).

## What the baseline shows

- **Already good:** CLS 0 and TBT 0 everywhere, 10–11 KB CSS, one 28 KB preloaded variable font, images in WebP with width/height, `srcset`, hero image `eager` + `fetchpriority=high`, everything else lazy, no jQuery, no emoji script, initial JS 2.5 KB gzip.
- **Home is the only miss:** LCP 2761 ms. The LCP element is the hero background image (57 KB WebP). It is discoverable in the HTML and has high priority, so the delay is the chain HTML → CSS (render-blocking) → image under slow-4G simulation. FCP on Home (1.9 s) is double the other pages for the same reason: the hero image competes with the CSS and font.
- **JavaScript weight is the motion runtime** (GSAP + ScrollTrigger, 46 KB gzip, lazy, no main-thread cost measured: TBT 0). Blog posts load none of it. It is within the approved 50 KB budget; shrinking it means changing the approved animations, so it is not touched without your decision.
- **Render-blocking:** `main.css` (6 KB gzip) and the WPConsent `placeholders.css` (0.9 KB) on every page.
- **Weight:** pages are 90–270 KB. Page weight is not the problem; the request chain is.

## Changes and measured effect (median of 5 runs, same method)

| Change                                                                                                                                                     | Result                                                                                         | Kept                                                                    |
| ---------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------- |
| WebP/AVIF quality 70/60 instead of 82 (`inc/image.php`; the hero background went from 56 KB to 29 KB)                                                      | Home LCP 2761 → 2291 ms, landing 2255 → 1654 ms, page weight −34 KB (Home) to −58 KB (landing) | yes                                                                     |
| Preload of the hero background image in `<head>` (`chargenet_preload_hero_image()`)                                                                        | No change (the image was already discoverable and high priority)                               | yes, harmless, and it keeps the image ahead of late CSS on slow servers |
| Inline `main.css` (25 KB, 6 KB gzip) instead of a blocking link                                                                                            | Home LCP 2291 → 2465, landing 1654 → 1953: **worse**                                           | no, reverted                                                            |
| `font-display: optional` instead of `swap`                                                                                                                 | No change in LCP or FCP                                                                        | no, reverted                                                            |
| Page forms: campaign fields and a fresh nonce/time token come from JavaScript (`/wp-json/chargenet/v1/form-token`), the HTML is the same for every visitor | Needed for page caching, no effect on the numbers                                              | yes                                                                     |

After these changes (`docs/perf-after-step1.json`): Home 2286 ms, Carriers 1959, Locations 1958, Blog post 1353, Landing 1954. **All five templates are under the 2500 ms LCP target.** CLS 0 and TBT 0 everywhere.

### Not done, with reason

- **WPConsent `placeholders.css` (0.9 KB, blocking):** it loads in parallel with the theme CSS, so deferring it changes nothing measurable.
- **Hero background source is 800 × 800 px** and is stretched over the full width on large screens. That is an image-quality question for the content owner, not a speed one. A wider source (about 1600 px) would cost roughly 50 KB more on desktop only.
- **Motion runtime (46 KB gzip):** unchanged, see above.

### Forms and the page cache

Cached HTML is shared, so nothing visitor-specific may be rendered into it. The forms now render empty campaign fields and the return URL without query string. JavaScript fills the campaign fields (URL, else the `cn_campaign` cookie) and replaces the nonce and time token with fresh ones. Without JavaScript the form still works with the nonce from the page, which WordPress accepts for 12 to 24 hours, so **set the page cache lifetime to at most 10 hours**. Campaign attribution is lost for visitors without JavaScript.

## Database review

Measured with `SAVEQUERIES` (temporary, not in the repo) on a warm local site, one request per template:

| Template  | Queries | DB time | PHP time | Peak memory |
| --------- | ------: | ------: | -------: | ----------: |
| Home      |      98 |   12 ms |    55 ms |      8.5 MB |
| Carriers  |      79 |   19 ms |    66 ms |      8.0 MB |
| Locations |      81 |   10 ms |    47 ms |      8.0 MB |
| Blog list |     100 |   12 ms |    48 ms |      7.9 MB |
| Blog post |      96 |   13 ms |    52 ms |      8.0 MB |
| NL report |      82 |   12 ms |    49 ms |      7.8 MB |

No query is slower than 2 ms and nothing repeats in a way that needs fixing; the counts come from WordPress core, Polylang and Rank Math. Peak memory of 8 MB is far below any shared-hosting `memory_limit`. A page cache removes all of this for repeat requests; without one, expect roughly 50 ms of PHP on this site plus whatever the shared server adds.

External API calls: the site makes none at render time (forms send mail on submit only; Tag Manager loads in the browser after consent), so there is nothing to cache in transients.

## Guardrails

- `npm run check:budget` (CI, lint-build job): initial JS 6 KB, motion JS 50 KB gzip.
- `npm run perf -- --check` (CI, seo job, after DDEV starts): median of 3 Lighthouse mobile runs per template against `bin/perf-budget.json`: LCP 2500 ms, CLS 0.1, TBT 200 ms, page weight 400 KB, JS 70 KB. The result is uploaded as artifact `perf-result`. The failing lines name the template and the metric.
- `npm run perf -- --compare docs/perf-baseline.json --check` also flags a template that got more than 15% worse than the baseline. Use it locally on the same machine; the CI runner is not comparable with the baseline.
- Home sits at 2290 ms against the 2500 ms limit, and one noisy run reached 2900 ms. If CI flags only Home LCP once, re-run before changing code; if it repeats, look at what was added to the first viewport.
- After an intended change, save a new baseline: `npm run perf -- --out docs/perf-baseline.json`.

## Third parties

Only Google Tag Manager, and only after the visitor accepts statistics (`docs/tracking.md`); Cloudflare Turnstile loads only when both keys are set. Their cost is **not measured yet**: loading the real container locally would send test hits into the live Analytics property. Measure after launch with a GTM preview/debug session or a test container, with consent granted: compare `npm run perf` with the consent cookie set against the baseline.

## Hosting facts (needed before the cache setup)

Run `bin/check-host.php` on STRATO over SSH (`wp eval-file check-host.php`, see the file header): it prints memory limit, OPcache, whether WordPress can write WebP and AVIF, cron, drop-ins, and the three `curl` commands that show compression and cache headers once the site is online. The panel questions it cannot answer: whether `.htaccess` overrides are allowed, whether STRATO has its own page cache or CDN switch, and whether real cron jobs exist. Locally (DDEV): memory unlimited, OPcache on, WebP and AVIF writable.

## Page cache and server rules (decided)

- **Cache Enabler (free), production only.** It stores finished HTML on disk and serves it without WordPress (measured locally: 67 ms → 1.2 ms per page, cleared on every save, `utm_*`/`gclid` links served from the same cached page, logged-in users and query-string URLs never cached). Chosen over WP Super Cache (more settings, more moving parts), W3 Total Cache (large, fiddly) and LiteSpeed Cache (needs a LiteSpeed server; STRATO runs Apache). Polylang works because every language has its own URL. Settings are code: `bin/setup-cache.php`.
- **Rules:** `docs/htaccess.md` (compression, browser caching, AVIF type, direct serving of cached pages). Not yet tested on STRATO.
- **Images:** STRATO serves `.avif` when the file type is known; the rules add `AddType image/avif .avif`. Whether WordPress can also _create_ AVIF there depends on the server's image library: `bin/check-host.php` shows it. If not, the existing filter in `inc/image.php` falls back to WebP by itself, no change needed.

## Still to do

Page cache, compression and cache headers (waiting for the hosting answers: memory limit, cron, `.htaccess`, Brotli, WebP/AVIF in `phpinfo()`). Facades for video and maps: the site has none yet; add them with the first embed.

## Real-user monitoring after launch

Lab tests (this document) catch regressions; only visitors' real devices show what Google ranks on.

1. **Search Console → Experience → Core Web Vitals.** Add and verify the property for chargenet.energy first. The report groups URLs by status (Good, Needs improvement, Poor) for mobile and desktop and by metric (LCP, INP, CLS). It uses Chrome users' data from the last 28 days, so it stays empty for the first weeks and for URL groups with too little traffic. Click a status, then an issue, to see example URLs. After a fix, use **Validate fix**; Google watches 28 days.
2. **PageSpeed Insights** (pagespeed.web.dev) for any URL: the top block is field data (CrUX, 28 days, 75th percentile), the lower block is a lab run. If the URL has too little traffic it falls back to the whole origin.
3. **CrUX directly:** the Chrome UX Report has a free dashboard (CrUX Dashboard in Looker Studio) and an API with a free key; it shows the monthly trend per metric and device. It needs enough traffic; a new low-traffic site may show nothing for a while.
4. **Targets** (75th percentile of real visits): LCP ≤ 2.5 s, INP ≤ 200 ms, CLS ≤ 0.1.
5. **Field numbers will differ from our lab numbers**: real servers add TTFB, real phones are slower or faster than the simulated one. Re-run `npm run perf` against the live URL (`npm run perf -- https://chargenet.energy`) once after launch and save it as the new baseline.
6. **Own measurements (optional):** GA4 via Tag Manager can record web-vitals events, but only for visitors who accepted statistics, which skews the sample toward engaged users. Search Console and CrUX are not skewed that way, so they are the main source.
