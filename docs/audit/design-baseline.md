# Design baseline

Source: computed styles sampled on the live site (/, /locations, /carriers, /about, /faq, /blog, /rapport2027 @1440px; counts = elements using the value) cross-checked with tailwind.config.ts / src/index.css in the source repo. The site is Tailwind utility styled; shadcn/ui CSS variables exist but are a neutral grey palette and are mostly overridden by hard-coded Tailwind classes.

## Colours (observed)

**Brand / accent**

| Token | Value | Evidence |
|---|---|---|
| Brand dark green (hero/section backgrounds, gradients) | #083A0B | `rgb(8 58 11)` in index.css gradients; 22 bg uses |
| Green 900 | #14532D | text 10×, bg 12× |
| Green 600 / 700 / 500 | #16A34A / #15803D / #22C55E | text links, primary buttons (#22C55E button bg) |
| Green 50 | #F0FDF4 | light card bg |
| Cyan (Calendly badge) | #00FFFF with black text | set in index.html Calendly init |

**Neutrals (Tailwind gray scale)**

| Role | Value |
|---|---|
| Body text default (shadcn foreground) | #262626 (hsl 0 0% 15%) |
| Text grey 700 / 600 / 500 / 400 | #374151 / #4B5563 / #6B7280 / #9CA3AF |
| Text/headings dark | #111827, #1F2937 |
| Surface | #FFFFFF, #F9FAFB, #F3F4F6, #E5E7EB |
| Border default | #E6E6E6 (72×), #E5E7EB |
| Nav/overlay | rgba(255,255,255,.1), rgba(0,0,0,.2), rgba(0,0,0,.5) |
| Destructive | hsl(0 84.2% 60.2%) |
| Legacy tailwind "wrlds" colours (unused template leftovers) | #9F9EA1, #3F3F3F, #F6F6F7, #C8C8C9, #F1F1F1 |

Most-used computed text colours (count): #262626 (154), #374151 (125), #FFFFFF (108), #4B5563 (83), #D1D5DB (71), #F3F4F6 (70), #6B7280 (54), #111827 (42)

Most-used computed backgrounds: #FFFFFF (120), #F3F4F6 (36), #E5E7EB (31), #F9FAFB (25), #FFFFFF @0.1 (24), #000000 @0.2 (23), #083A0B (22), #374151 (15), #14532D (12), #F0FDF4 (8)

## Typography

- **Family:** Source Sans 3 (Google Fonts, weights 300/400/700 requested in index.css; 500/600 seen in use → browser synthesises or maps). Fallback `sans-serif`. 782 of 789 text nodes use it. Tailwind family key `space` = Source Sans 3.
- **Scale observed (size/line-height, weight, count):**

| Style | Count |
|---|---|
| A 16px/24px w400 lsnormal | 84 |
| SPAN 14px/20px w400 lsnormal | 79 |
| P 16px/24px w400 lsnormal | 73 |
| P 14px/20px w400 lsnormal | 59 |
| H3 20px/28px w700 lsnormal | 49 |
| SPAN 14px/20px w500 lsnormal | 38 |
| BUTTON 14px/20px w500 lsnormal | 29 |
| DIV 14px/20px w500 lsnormal | 29 |
| SPAN 16px/24px w400 lsnormal | 25 |
| A 14px/20px w500 lsnormal | 21 |
| BUTTON 16px/24px w400 lsnormal | 21 |
| STRONG 16px/24px w700 lsnormal | 21 |
| H4 16px/24px w600 lsnormal | 20 |
| H4 20px/28px w700 lsnormal | 18 |
| SPAN 12px/16px w400 lsnormal | 17 |
| H3 18px/28px w700 lsnormal | 17 |
| A 14px/20px w400 lsnormal | 14 |
| P 20px/28px w400 lsnormal | 14 |
| P 14px/22.75px w400 lsnormal | 12 |
| BUTTON 18px/28px w600 lsnormal | 12 |
| SPAN 16px/24px w700 lsnormal | 10 |
| LI 16px/24px w400 lsnormal | 8 |

- Headings seen: H1 hero (large, white on green), H2 30px/36px w600 and 36px/40px w700, H3 20/28 w700 (cards), 24/32 w700, H4 16/24 w600 and 20/28 w700.
- No letter-spacing customisation (all `normal`).

## Spacing & layout

- **Section vertical padding:** 96px/96px (4×), 64px (3×), 40px, 25px, 50px (inconsistent: candidate to normalise to a scale).
- **Container widths seen (max-width, horizontal padding):** 1400px pad 16px (8); 896px pad 0px (8); 420px pad 16px (7); 1440px pad 0px (7); max-content pad 0px (7); none pad 0px (6); 672px pad 0px (6); 1152px pad 0px (5); 1400px pad 32px (5); 768px pad 0px (4). Tailwind container: centered, padding 2rem, 2xl 1400px.
- **Breakpoints:** Tailwind defaults; mobile switch for some components at <768px (useIsMobile).

## Shape & elevation

- **Radii (px, count):** 9999px (176), 6px (137), 8px (53), 12px (47), 4px (23), 25px (7), 16px (4), 48px (1), 40px (1) (9999 = pills/avatars; base --radius 0.5rem).
- **Shadows:** mostly Tailwind shadow-sm (`0 1px 2px rgba(0,0,0,.05)`), shadow-md/lg/xl/2xl on cards.

## Buttons (computed, count)

| bg | text | radius | padding | font |
|---|---|---|---|---|
| #000000 @0 | #F3F4F6 | 6px | 6px 12px | 16px/400 |
| #000000 @0 | #F3F4F6 | 6px | 8px 16px | 14px/500 |
| #000000 @0 | #FFFFFF | 6px | 8px 16px | 14px/500 |
| #000000 @0 | #262626 | 0px | 16px 0px | 18px/600 |
| #000000 @0 | #6B7280 | 0px | 0px | 14px/500 |
| #000000 @0 | #FFFFFF | 6px | 8px 12px | 14px/400 |
| #000000 @0 | #FFFFFF | 0px | 0px | 16px/400 |
| #FFFFFF | #262626 | 6px | 8px 12px | 14px/400 |
| #1F2937 | #D1D5DB | 9999px | 0px | 16px/400 |
| #374151 | #FFFFFF | 6px | 8px 16px | 16px/400 |

Patterns: transparent/white-outline on dark hero; solid #374151 (gray-700) with white text; white with dark text; green #22C55E for lead-form submit; pill language/toggle buttons.

## Motion

framer-motion entrance (whileInView) on most sections; Tailwind keyframes defined: accordion-down/up 0.2s, slide-in 0.4s, float 6s, pulse-slow 4s, scale-in-out 3s, rotate-slow 20s, bounce-subtle 2s, shimmer, scroll (marquee). Count-up on stats (2s). Home loading animation component (LoadingAnimation) exists in source.

## Logo & favicon files

Downloaded from live to `design/`:

| File | Source URL | Notes |
|---|---|---|
| ChargeNet-logo.svg | /uploads/ChargeNet-logo.svg | primary logo (vector) 8.4 KB |
| ChargeNet-logo.png | /uploads/ChargeNet-logo.png | raster logo 33.6 KB |
| ChargeNet-icon.png | /uploads/ChargeNet-icon.png | icon; used as og:image / twitter:image / JSON-LD logo (note JSON-LD and og:image:secure_url point at /img/ChargeNet-icon.png which is not where the file lives: verify) |
| favicons__favicon.ico | /uploads/favicons/favicon.ico | 15 KB |
| favicons__favicon-16x16.png, favicon-32x32.png | /uploads/favicons/ | |
| favicons__apple-touch-icon.png | /uploads/favicons/ | 180×180 |
| favicons__android-chrome-192x192.png, 512x512.png | /uploads/favicons/ | referenced by site.webmanifest |
| favicons__safari-pinned-tab.svg | /uploads/favicons/ | mask-icon colour #7952b3 (a Bootstrap purple, off-brand) |
| favicons__site.webmanifest | /uploads/favicons/ | |

The static head also declares a first `<link rel=icon>` to a hash-named PNG in /uploads/ (not downloaded; redundant with the favicons set).

Other brand imagery in /uploads/ (repo public/uploads): partners/ (amazon, avia-volt, dhl, mvs, oost-nl, postnl, sligro, volta-energy), team/ (Piotr, Seb, Stefano, Thijs, Zlatan), co-funded-EU_EN/NL.png, logo-OostNL.png, bg-truck.png, CCS2-cable.jpg, ChargeNet-Driver-App.png, ChargeNet-map.png, map-connectr.png, trend2026-*.jpg. Only assets actually shown on live are captured in the screenshots and per-page docs.
