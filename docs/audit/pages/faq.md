# /faq

- **Live URL:** https://chargenet.energy/faq
- **HTTP status seen:** 200
- **Final URL after client redirects:** /faq
- **Screenshots:** `screenshots/faq.{en,nl}.{1440,390}.png`
- **Page height:** 1440px→2373px (EN), 2429px (NL); 390px→3745px (EN)
- **Content fingerprint EN / NL:** d604988d / 01a1b4e4

## Purpose

FAQ for e-truck drivers (driver app, access, finding/reserving chargers, invoices, troubleshooting). Accordion.

## Sections (ordered, top to bottom)

Shared blocks (navbar, footer, contact cards) are documented in `_shared-layout.md`; they are only referenced here.

### 1. navbar

- Element: `<nav>`, height 64px
- See `_shared-layout.md` (navbar).

### 2. hero-title-band

- Element: `<div>`, height 140px
- Headings (EN): H1 "Frequently Asked Questions for Electric Truck Drivers"
- Headings (NL): H1 "Veelgestelde vragen voor elektrische vrachtwagenchauffeurs"
- Images/backgrounds: none
- CTAs/links: none

**Copy (EN)**

> Frequently Asked Questions for Electric Truck Drivers
> Find answers to common questions about using our mobile app for EV truck charging.

**Copy (NL)**

> Veelgestelde vragen voor elektrische vrachtwagenchauffeurs
> Vind uw antwoord op veelgestelde vragen over het gebruik van onze mobiele app voor het opladen van elektrische vrachtwagens.

### 3. faq-accordion

- Element: `<section>`, height 1588px
- Headings (EN): H3 "Where can I download the ChargeNet driver app?" · H3 "How can I get access to the ChargeNet driver app?" · H3 "How do I find charging stations compatible with my electric truck?" · H3 "Can I reserve a charging station in advance for my delivery route?" · H3 "How do I track my charging costs and create expense reports?" · H3 "What should I do if a charging station seems available in the app but turns out to be out of order?" · H3 "How long does it typically take to charge an electric truck?" · H3 "Can I monitor my truck's charging progress remotely?" · H3 "How do I set up fleet billing for multiple trucks?" · H3 "What payment methods are accepted through the app?" · H3 "What support is available if I need help during charging?" · H3 "How can I request to delete my account and associated data?" · H3 "Still have questions?"
- Headings (NL): H3 "Waar kan ik de ChargeNet driver app downloaden?" · H3 "Hoe krijg ik toegang tot de ChargeNet driver app?" · H3 "Hoe vind ik laadstations die compatibel zijn met mijn elektrische vrachtwagen?" · H3 "Kan ik een laadstation vooraf reserveren voor mijn bezorgroute?" · H3 "Hoe houd ik mijn laadkosten bij en maak ik onkostendeclaraties?" · H3 "Wat moet ik doen als een laadstation als beschikbaar wordt weergegeven in de app, maar defect blijkt te zijn?" · H3 "Hoe lang duurt het meestal om een elektrische vrachtwagen op te laden?" · H3 "Kan ik op afstand de voortgang van het laden van mijn vrachtwagen volgen?" · H3 "Hoe stel ik wagenparkfacturatie in voor meerdere vrachtwagens?" · H3 "Welke betaalmethoden worden via de app geaccepteerd?" · H3 "Welke ondersteuning is beschikbaar als ik hulp nodig heb tijdens het laden?" · H3 "Hoe kan ik verzoeken mijn account en bijbehorende gegevens te verwijderen?" · H3 "Heeft u nog vragen?"
- Images/backgrounds: none
- CTAs/links: 
  - "Back to Home" → /
  - "Where can I download the ChargeNet driver app?" → (button, no href)
  - "How can I get access to the ChargeNet driver app?" → (button, no href)
  - "How do I find charging stations compatible with my electric truck?" → (button, no href)
  - "Can I reserve a charging station in advance for my delivery route?" → (button, no href)
  - "How do I track my charging costs and create expense reports?" → (button, no href)
  - "What should I do if a charging station seems available in the app but turns out to be out of order?" → (button, no href)
  - "How long does it typically take to charge an electric truck?" → (button, no href)
  - "Can I monitor my truck's charging progress remotely?" → (button, no href)
  - "How do I set up fleet billing for multiple trucks?" → (button, no href)
  - "What payment methods are accepted through the app?" → (button, no href)
  - "What support is available if I need help during charging?" → (button, no href)
  - "How can I request to delete my account and associated data?" → (button, no href)
  - "Contact Support" → (button, no href)
  - "Download Mobile App" → (button, no href)
- Interactive elements detected in DOM: accordions=12

**Copy (EN)**

> Back to Home
> Where can I download the ChargeNet driver app?
> How can I get access to the ChargeNet driver app?
> How do I find charging stations compatible with my electric truck?
> Can I reserve a charging station in advance for my delivery route?
> How do I track my charging costs and create expense reports?
> What should I do if a charging station seems available in the app but turns out to be out of order?
> How long does it typically take to charge an electric truck?
> Can I monitor my truck's charging progress remotely?
> How do I set up fleet billing for multiple trucks?
> What payment methods are accepted through the app?
> What support is available if I need help during charging?
> How can I request to delete my account and associated data?
> Still have questions?
> Our support team is here to help with any issues or questions when connecting to ChargeNet.
> Contact Support
> Download Mobile App

**Copy (NL)**

> Terug naar Home
> Waar kan ik de ChargeNet driver app downloaden?
> Hoe krijg ik toegang tot de ChargeNet driver app?
> Hoe vind ik laadstations die compatibel zijn met mijn elektrische vrachtwagen?
> Kan ik een laadstation vooraf reserveren voor mijn bezorgroute?
> Hoe houd ik mijn laadkosten bij en maak ik onkostendeclaraties?
> Wat moet ik doen als een laadstation als beschikbaar wordt weergegeven in de app, maar defect blijkt te zijn?
> Hoe lang duurt het meestal om een elektrische vrachtwagen op te laden?
> Kan ik op afstand de voortgang van het laden van mijn vrachtwagen volgen?
> Hoe stel ik wagenparkfacturatie in voor meerdere vrachtwagens?
> Welke betaalmethoden worden via de app geaccepteerd?
> Welke ondersteuning is beschikbaar als ik hulp nodig heb tijdens het laden?
> Hoe kan ik verzoeken mijn account en bijbehorende gegevens te verwijderen?
> Heeft u nog vragen?
> Ons support team staat voor u klaar om u te helpen met eventuele problemen of vragen bij het verbinden met ChargeNet.
> Contact Support
> Download Mobiele App

### 4. footer (#contact)

- Element: `<footer>`, height 477px
- See `_shared-layout.md` (footer).

## Interactive behaviour

- Radix accordion, single item open at a time (aria-expanded toggles; 12 question triggers + 3 navbar triggers counted, 0 open on load; verified live).
- Shared layout behaviour: see `_shared-layout.md`.

## Forms

- No form rendered on this page. (Contact form component exists in source: ContactForm.tsx: fields name (min 2), email (valid), message (min 10), honeypot, ≥3s timing check; submits via EmailJS from the browser; success/failure toast. It is not rendered on any crawled page.)

## Third-party scripts

- www.googletagmanager.com
- chargenet.energy
- cdn.gpteng.co
- assets.calendly.com
