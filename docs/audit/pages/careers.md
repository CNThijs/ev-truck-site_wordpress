# /careers

- **Live URL:** https://chargenet.energy/careers
- **HTTP status seen:** 200
- **Final URL after client redirects:** /careers
- **Screenshots:** `screenshots/careers.{en,nl}.{1440,390}.png`
- **Page height:** 1440px→1853px (EN), 1881px (NL); 390px→3037px (EN)
- **Content fingerprint EN / NL:** 3acb9ea7 / 42abe3f8

## Purpose

Employer-brand page: why join, and a contact route to the CEO. No open vacancies listed.

## Sections (ordered, top to bottom)

Shared blocks (navbar, footer, contact cards) are documented in `_shared-layout.md`; they are only referenced here.

### 1. navbar

- Element: `<nav>`, height 64px
- See `_shared-layout.md` (navbar).

### 2. hero-title-band

- Element: `<div>`, height 92px
- Headings (EN): H1 "Join our Team"
- Headings (NL): H1 "Sluit u aan bij ons team"
- Images/backgrounds: none
- CTAs/links: none

**Copy (EN)**

> Join our Team
> We`re looking for passionate innovators to help us decarbonise road logistics and construction projects.

**Copy (NL)**

> Sluit u aan bij ons team
> Wij zijn op zoek naar gepassioneerde innovators die met ons samen de decarbonisatie van de logistiek en de bouwsector willen versnellen.

### 3. value-props-3col

- Element: `<section>`, height 1116px
- Headings (EN): H2 "Why Join ChargeNet?" · H3 "Innovation" · H3 "Impact" · H3 "Growth" · H3 "Contact Our CEO" · H3 "Sebastiaan de Vries"
- Headings (NL): H2 "Waarom aansluiten bij ChargeNet?" · H3 "Innovatie" · H3 "Impact" · H3 "Groei" · H3 "Contact onze CEO" · H3 "Sebastiaan de Vries"
- Images/backgrounds: 
  - /uploads/team/Seb.jpg (alt "Sebastiaan de Vries")
- CTAs/links: 
  - "info@chargenet.energy" → mailto:info@chargenet.energy
  - "LinkedIn Profile" → https://www.linkedin.com/in/sebastiaan-de-vries-chargenet/ [_blank]

**Copy (EN)**

> We welcome both full-time professionals and interns who would like to contribute to accelerating the adoption of electric trucks and electric heavy machinery.
> Why Join ChargeNet?
> Innovation
> Work on cutting-edge technology that`s changing multiple industries and accelerates the adoption of electric trucks and electric heavy machinery.
> Impact
> Create solutions that enhance safety, performance, and sustainability.
> Growth
> Develop your skills in a rapidly expanding field with diverse challenges.
> Contact Our CEO
> Sebastiaan de Vries
> CEO & CoFounder
> info@chargenet.energy
> LinkedIn Profile

**Copy (NL)**

> Wij nodigen zowel fulltime professionals als stagiairs uit die graag een bijdrage willen leveren aan het versnellen van de adoptie van elektrische vrachtwagens en elektrisch zwaar materieel.
> Waarom aansluiten bij ChargeNet?
> Innovatie
> Werk aan geavanceerde technologie die meerdere sectoren verandert en de adoptie van elektrische vrachtwagens en elektrisch zwaar materieel versnelt.
> Impact
> Creëer oplossingen die de veiligheid, prestaties en duurzaamheid verbeteren.
> Groei
> Ontwikkel uw vaardigheden in een snelgroeiend vakgebied met uiteenlopende uitdagingen.
> Contact onze CEO
> Sebastiaan de Vries
> CEO & CoFounder
> info@chargenet.energy
> LinkedIn Profile

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
