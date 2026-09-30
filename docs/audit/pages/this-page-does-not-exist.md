# /this-page-does-not-exist

- **Live URL:** https://chargenet.energy/this-page-does-not-exist
- **HTTP status seen:** 200 (soft 404: page body is the 404 component)
- **Final URL after client redirects:** /this-page-does-not-exist
- **Screenshots:** `screenshots/this-page-does-not-exist.{en,nl}.{1440,390}.png`
- **Page height:** 1440px→1345px (EN), 1373px (NL); 390px→2637px (EN)
- **Content fingerprint EN / NL:** 0baf41f9 / dc2ca6d6

## Purpose

NotFound component. Served with HTTP 200 (SPA fallback), i.e. soft 404.

## Sections (ordered, top to bottom)

Shared blocks (navbar, footer, contact cards) are documented in `_shared-layout.md`; they are only referenced here.

### 1. navbar

- Element: `<nav>`, height 64px
- See `_shared-layout.md` (navbar).

### 2. hero-title-band

- Element: `<div>`, height 132px
- Headings (EN): H1 "404"
- Headings (NL): H1 "404"
- Images/backgrounds: none
- CTAs/links: 
  - "Return to Home" → /

**Copy (EN)**

> 404
> Oops! Page not found
> Return to Home

**Copy (NL)**

> 404
> Oops! Page not found
> Return to Home

### 3. contact-team-cards (#contact-info)

- Element: `<section>`, height 592px
- See `_shared-layout.md` (contact-team-cards).

### 4. footer (#contact)

- Element: `<footer>`, height 477px
- See `_shared-layout.md` (footer).

## Interactive behaviour

- No page-specific interaction beyond shared layout (below).
- Shared layout behaviour: see `_shared-layout.md`.

## Forms

- No form rendered on this page. (Contact form component exists in source: ContactForm.tsx: fields name (min 2), email (valid), message (min 10), honeypot, ≥3s timing check; submits via EmailJS from the browser; success/failure toast. It is not rendered on any crawled page.)

## Third-party scripts

- www.googletagmanager.com
- chargenet.energy
- cdn.gpteng.co
- assets.calendly.com
