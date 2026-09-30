# Section inventory (reusable section types)

Deduplicated from the 21 distinct page bodies (aliases, redirects and the soft-404s excluded). "Pages" lists where the type appears on live. Page docs in `pages/` hold the exact copy. Names are proposals for the WordPress build, not existing component names. Where a live block mixes several types inside one `<section>` (Locations, Carriers, About, Careers) they are split here.

## Global chrome (every page)

| Type | Pages | Variants |
|---|---|---|
| **Navbar** | all | Transparent over hero, dark once scrolled. Desktop: Home, About Us, Customers (dropdown → /locations, /carriers), News, Contact Us (scroll to #contact), Login (external portal), language select. Mobile: hamburger; two links point to non-existent routes (`/location-owners`, `/fleet-managers`). |
| **Footer** | all | Brand blurb, Arnhem address (Industrial Park Kleefsewaard), Company links (About Us, FAQ, Careers, Security, Privacy Policy), "Get in Touch", LinkedIn, EU co-funded logo and OostNL logo (each links to a blog post), © line + Privacy Policy/Security links. Anchor id `#contact`. No app-store badges in the footer. |
| **Contact team cards** | home, locations, carriers, blog, blog posts | 3 people (photo, name, role; all three use the same mailto info@chargenet.energy); heading "Contact Us Today". Id `#contact-info`. Not on about/faq/careers/security/privacy/rapport2027. |
| **Floating contact button** | all (after scrollY>500) | Scrolls to #contact. |
| **Calendly badge** | all | Fixed bottom-right pill "Schedule time with us" (not translated), popup. |

## Heroes

| Type | Pages | Variants |
|---|---|---|
| **Hero: image banner with CTAs** | home | Full-width background (bg-truck + green overlay), H1, sub-copy, 2 buttons ("Explore Use Cases" → Typeform slider, "Contact Us" → #contact). Height 456px @1440. Commented-out video variant exists in source. |
| **Hero: title band** | about, faq, careers, security, privacy-policy, locations, carriers, blog | Short green band (92–140px) with H1 and optional 1-2 line intro. Locations/Carriers/Blog have the intro paragraph (120px); the others H1 only. |
| **Hero: campaign (cover + lead CTA)** | rapport2027 | 600px, background image, H1 "De cijfers achter de e-transitie...", description, cover image, 2 CTAs (download → #download, insights → #benefits). Dutch only. |
| **Article header** | blog posts | Featured image as hero background, H1, date, author, category badge. |

## Content sections

| Type | Pages | Variants |
|---|---|---|
| **Audience split feature list** | home (#features) | Two columns (Location Managers / Fleet Managers), each "Key Benefits" + "Platform Features" lists, link CTA per column. |
| **Stats band** | home (#whyChargeNet) | 4 headline numbers (3.8Bn tonne CO₂, 18%, 80%, 80%) with count-up on scroll, plus "What ChargeNet Does for You" text. Dark background images (6 bg). |
| **Card carousel (projects)** | home (#projects) | 4 image-background cards, prev/next buttons, 4 dots, swipe, auto-rotate per source. **All 4 "Read More" links (/projects/destination-charging, /chargebase, /ijmondaanzet, /bouw-pow) resolve to the 404 page on live** (routes commented out in App.tsx). |
| **Step process (two audiences)** | home (#getStarted); locations, carriers ("How it works") | Home: per audience steps (Registration, Configuration, Monetization …), the two App Store / Google Play badges (links to the driver app) and CTAs "Explore Location Benefits" → /locations, "Explore Carrier Benefits" → /carriers. Locations/Carriers: 5 numbered steps each with "Key Activities" bullets, "View Details"/"Currently Viewing" stepper buttons. Same concept, two layouts. |
| **Benefit grid (icon + title + text)** | locations, carriers | 6 cards: Locations: Competitive pricing, One Platform, Control & Flexibility, Easy to Use, Private access, Security by Design. Carriers: Reduce charging costs replaces Competitive pricing. |
| **Intro + image split** | locations, carriers | H2 + 3 paragraphs + static network map image (`ChargeNet-map.png`). |
| **CTA band** | locations, carriers | "ChargeNet; the private charging network for you!" + "Join today..." H3; buttons/links to contact. Home ends with contact cards instead. |
| **Blog preview cards** | home (#blog) | "Latest News": 3 cards (image bg, category, date, title, excerpt) + "View all". |
| **Post grid** | blog | Featured large card + grid (3 col desktop) of all 18 posts; category badge, date, excerpt; some cards link externally. |
| **Story / mission / values / team** | about | Story text, Mission, Values, Team (3 people: photo, name, role, LinkedIn), "Our Board of Advisors" text (no photos), link to /careers. |
| **Value props (3-col) + person CTA** | careers | Innovation / Impact / Growth cards; "Contact Our CEO" card (Sebastiaan de Vries) with mailto info@chargenet.energy and LinkedIn link. No vacancy list. |
| **FAQ accordion** | faq | 12 Q&As (Radix accordion, all closed on load) + a "Still have questions?" block with "Contact Support" and "Download Mobile App" buttons and a "Back to Home" link. Source data: src/data/faqQuestions.ts. |
| **Prose / legal** | security, privacy-policy | Security: 4 H2 sections, plain paragraphs. Privacy: ~12.5k chars, many H2 (About us … Cookies), lists, contact mail. |
| **Benefit list (numbered insights)** | rapport2027 (#benefits) | H2 + 5 insight cards (H3 headline + text) about truck levy, excise, ETS 2, AanZET subsidy, ZE-zones. |
| **Lead form** | rapport2027 (#download) | Cover/holding image + form: code view vs no-code view; success/error states; privacy link. |
| **Article body** | blog posts | Sequence of: H2, H3, paragraph (inline HTML), image (centered or floated), list, quote, table, icon-list. Types used in the data: heading, subheading, paragraph, image, list, quote, table, icon-list (one post also has a malformed section type that renders nothing). |
| **404** | unknown URLs (+ /terms-and-conditions, /trend2026) | H1 "404" + link home; served with HTTP 200. |

## Not found (checked, absent)

Maps (interactive), tabs, filters/search, sliders with controls other than the projects carousel, video, data tables (except inside 2 blog posts), testimonials, pricing tables, logo/partner marquee (/uploads/partners/* files exist in the repo but no request for them occurred on any crawled page), newsletter signup.
