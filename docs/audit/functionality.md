# Functionality / network audit: Locations and Carriers

Method: Playwright (Chromium) loaded each page with networkidle, scrolled the full page, waited 1.2s. Recorded every response (method, URL without query string, type, status, content-type). Four runs per page: EN/NL × 1440/390. Query strings are stripped and no headers, cookies or bodies are stored, so no tokens or credentials are in this file.

## Headline finding

**Neither /locations nor /carriers has a map, an API call, a response schema, a filter or a form.** All content is static copy compiled into the JS bundle (src/translations); imagery is static files. The only network activity beyond the page assets is third-party analytics/widgets. The brief's expectation of "API endpoints, map provider, response schema" does not apply to the live site. The "map" on /locations is a static PNG (`/uploads/ChargeNet-map.png`, alt text empty), not an interactive map.

The only first-party "dynamic" piece is the language flag in localStorage and the client-side stepper (no network).

## Locations (/locations, aliases /locaties, /locatie, /opbrengst)

### EN @ 1440px: 20 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/locations` | document | 200 | 1 | text/html |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 2 | application/javascript; charset=UTF-8 |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 1 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### NL @ 1440px: 19 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/locations` | document | 200 | 1 | text/html |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 1 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### EN @ 390px: 21 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/locations` | document | 200 | 1 | text/html |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 2 | application/javascript; charset=UTF-8 |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 2 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### NL @ 390px: 20 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/locations` | document | 200 | 1 | text/html |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 2 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### Images loaded (EN 1440)

- /uploads/ChargeNet-logo.png
- /uploads/team/Thijs.jpg
- /uploads/co-funded-EU_NL.png
- /uploads/ChargeNet-map.png
- /uploads/team/Seb.jpg
- /uploads/team/Piotr.jpg
- /uploads/logo-OostNL.png
- /uploads/map-connectr.png

## Carriers (/carriers, aliases /vervoerder, /vervoerders, /routecheck)

### EN @ 1440px: 20 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/carriers` | document | 200 | 1 | text/html |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 2 | application/javascript; charset=UTF-8 |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 1 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### NL @ 1440px: 19 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/carriers` | document | 200 | 1 | text/html |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 1 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (assets.calendly.com, chargenet.energy, fonts.googleapis.com, fonts.gstatic.com)

### EN @ 390px: 21 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/carriers` | document | 200 | 1 | text/html |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 2 | application/javascript; charset=UTF-8 |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 2 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### NL @ 390px: 21 responses, 19 unique

Non-image/font/CSS requests:

| Request | Type | Status | Count | Content-Type |
|---|---|---|---|---|
| `GET https://chargenet.energy/carriers` | document | 200 | 1 | text/html |
| `GET https://assets.calendly.com/assets/external/widget.js` | script | 200 | 1 | text/javascript |
| `GET https://www.googletagmanager.com/gtm.js` | script | 200 | 1 | application/javascript; charset=UTF-8 |
| `GET https://www.googletagmanager.com/gtag/js` | script | 200 | 2 | application/javascript; charset=UTF-8 |
| `GET https://cdn.gpteng.co/gptengineer.js` | script | 200 | 1 | application/javascript |
| `POST https://region1.google-analytics.com/g/collect` | fetch | 204 | 2 | text/plain |
| `GET https://chargenet.energy/assets/index-BxIGrRbs.js` | script | 200 | 1 | text/javascript |

Images/fonts/CSS: 12 unique (chargenet.energy, assets.calendly.com, fonts.googleapis.com, fonts.gstatic.com)

### Images loaded (EN 1440)

- /uploads/team/Thijs.jpg
- /uploads/team/Seb.jpg
- /uploads/ChargeNet-map.png
- /uploads/ChargeNet-logo.png
- /uploads/co-funded-EU_NL.png
- /uploads/team/Piotr.jpg
- /uploads/map-connectr.png
- /uploads/logo-OostNL.png

## Other flows checked (not Locations/Carriers, for completeness)

- **Home hero button "Explore Use Cases"** opens a Typeform slider: extra requests to renderer-assets.typeform.com, font.typeform.com and a POST to the Typeform host (`/forms/<id>/insights/performance/view-form-open`; form id omitted). Typeform owns that form's submit endpoint.
- **App badges** (home "How to Get Started") hot-link Apple App Store and Google Play badge images.
- **Contact/lead forms** (source code, not observed submitting to avoid sending real data): EmailJS `send` from the browser → api.emailjs.com. No first-party backend exists; there is no WordPress-equivalent endpoint to replicate, a new one (CF7/WPForms/REST) must be designed.
- **Login** → https://portal.chargenet.energy (separate app, behind auth, not audited).
- **Geo-IP**: `ipapi.co/json` is coded in i18n.ts (detectDefaultLanguage) but no request was seen in any crawl → dead code.
