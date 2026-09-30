# Shared layout blocks

Present on (nearly) every page. Captured from `/`.

## Behaviour

- Navbar: transparent, turns dark on scroll; desktop dropdown "Customers" (→ /locations, /carriers); "Contact Us" scrolls to #contact (footer) and does nothing visible on pages without that anchor target; "Login" opens https://portal.chargenet.energy in a new tab (external app, out of scope).
- Mobile menu (hamburger) links: Home, About Us, /location-owners, /fleet-managers, News: the last two are 404 on live (bug).
- Language switcher (EN/NL select). Sets localStorage key preferred-language; URL does not change; whole UI re-renders in the chosen language. Default EN unless stored.
- Floating "contact" button appears after scrollY > 500 and scrolls to #contact.
- Calendly badge widget bottom-right ("Schedule time with us"; text is not translated) opens Calendly popup for calendly.com/chargenet/introductie.

## Third-party scripts (site-wide)

- Google Tag Manager container + GA4 (gtag; consent defaults read from localStorage; IDs omitted here on purpose)
- Calendly badge widget (assets.calendly.com widget.js/css)
- Google Fonts: Source Sans 3 (fonts.googleapis.com / fonts.gstatic.com)
- cdn.gpteng.co/gptengineer.js (Lovable/GPT-Engineer editor script: should NOT be carried to the WordPress build)
- Silktide consent manager files exist in repo public/cookie-banner but no banner element was rendered on live in tests (see open questions)
- Typeform embed (home hero CTA; loads on click; renderer-assets.typeform.com, font.typeform.com, typeform form host)
- Apple App Store and Google Play badge images hot-linked in home "How to Get Started" (developer.apple.com, play.google.com); badges link to the driver app listings
- EmailJS (browser SDK → api.emailjs.com; contact form + campaign lead form, keys omitted)
- ipapi.co geolocation code exists in i18n.ts (detectDefaultLanguage) but was never called in live network logs

## navbar

- Element `<nav>`, height 64px
- Images: /uploads/ChargeNet-logo.png (alt "ChargeNet Logo")
- Links/CTAs:
  - "" → /
  - "Home" → /
  - "Home" → (button)
  - "About Us" → /about
  - "About Us" → (button)
  - "Customers" → (button)
  - "News" → /blog
  - "News" → (button)
  - "Contact Us" → (button)
  - "🇬🇧
EN" → (button)
  - "" → https://portal.chargenet.energy [_blank]
  - "" → (button)
  - "Customers" → /location-owners
  - "Login" → (button)
  - "🇬🇧EN" → (button)

**Copy (EN)**

> Home
> About Us
> Customers
> News
> Contact Us
> 🇬🇧
> EN

**Copy (NL)**

> Home
> Over Ons
> Klanten
> Nieuws
> Contact
> 🇳🇱
> NL

## contact-team-cards

- Element `<section>`, height 592px
- Images: /uploads/team/Seb.jpg (alt "Sebastiaan de Vries"); /uploads/team/Thijs.jpg (alt "Thijs Verwaal"); /uploads/team/Piotr.jpg (alt "Piotr Krzepczak")
- Links/CTAs:
  - "info@chargenet.energy" → mailto:info@chargenet.energy

**Copy (EN)**

> Get In Touch
> Contact Us Today
> Have questions about our ChargeNet network? Reach out to our team and let us discuss how we can help you to electrify your logistics.
> Sebastiaan de Vries
> CEO & CoFounder
> info@chargenet.energy
> Thijs Verwaal
> CISO & CoFounder
> info@chargenet.energy
> Piotr Krzepczak
> CTO & CoFounder
> info@chargenet.energy

**Copy (NL)**

> Neem Contact Op
> Neem vandaag contact op
> Of u nu een locatiemanager bent die zijn laadinfrastructuur wil geldelijker maken of een wagenpark manager die op zoek is naar betrouwbare laadoplossingen: wij maken het u gemakkelijk om aan de slag te gaan.
> Sebastiaan de Vries
> CEO & CoFounder
> info@chargenet.energy
> Thijs Verwaal
> CISO & CoFounder
> info@chargenet.energy
> Piotr Krzepczak
> CTO & CoFounder
> info@chargenet.energy

## footer

- Element `<footer>`, height 477px
- Images: /uploads/ChargeNet-logo.png (alt "ChargeNet Logo"); /uploads/co-funded-EU_NL.png (alt "EU Logo"); /uploads/logo-OostNL.png (alt "EU Logo")
- Links/CTAs:
  - "" → https://www.linkedin.com/company/chargenet-eu/ [_blank]
  - "About Us" → /about
  - "FAQ" → /faq
  - "Careers" → /careers
  - "Security" → /security
  - "Privacy Policy" → /privacy-policy
  - "Connect with Us" → (button)
  - "" → /blog/chargenet-starts-pilot-testing-at-mvs/ [_blank]
  - "" → /blog/chargenet-secures-funding-oostnl/ [_blank]

**Copy (EN)**

> ChargeNet is the platform for shippers & transport companies to collaborate on decarbonisation of road logistics by sharing charging infrastructure at destination. With ChargeNet you can charge at the right price, at the right place, at the right time.
> Industrial Park Kleefsewaard
> Westervoortsedijk 73
> 6827 AV, Arnhem, The Netherlands
> Company
> About Us
> FAQ
> Careers
> Security
> Privacy Policy
> Get in Touch
> Connect with Us
> © 2026 ChargeNet. All rights reserved.
> Privacy Policy
> Security

**Copy (NL)**

> ChargeNet is het platform voor verladers & transportbedrijven om samen te werken aan decarbonisatie van weglogistiek door het delen van laadinfrastructuur op bestemmingen. Met ChargeNet kan u laden voor de juiste prijs, op de juiste plaats, op het juiste moment.
> Industrieterrein Kleefsewaard
> Westervoortsedijk 73
> 6827 AV, Arnhem, Nederland
> Bedrijf
> Over Ons
> Veelgestelde Vragen (FAQ)
> Carriere
> Beveiliging
> Privacybeleid
> Neem Contact Op
> Connect with Us
> © 2026 ChargeNet. Alle rechten voorbehouden.
> Privacybeleid
> Beveiliging
