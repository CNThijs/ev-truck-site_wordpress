# /security

- **Live URL:** https://chargenet.energy/security
- **HTTP status seen:** 200
- **Final URL after client redirects:** /security
- **Screenshots:** `screenshots/security.{en,nl}.{1440,390}.png`
- **Page height:** 1440px→1881px (EN), 1853px (NL); 390px→3917px (EN)
- **Content fingerprint EN / NL:** bb7c1326 / 1b9eb4c7

## Purpose

Trust page: ISO 27001 information-security statement.

## Sections (ordered, top to bottom)

Shared blocks (navbar, footer, contact cards) are documented in `_shared-layout.md`; they are only referenced here.

### 1. navbar

- Element: `<nav>`, height 64px
- See `_shared-layout.md` (navbar).

### 2. hero-title-band

- Element: `<div>`, height 92px
- Headings (EN): H1 "Security"
- Headings (NL): H1 "Security"
- Images/backgrounds: none
- CTAs/links: none

**Copy (EN)**

> Security
> Security is important to us at ChargeNet.

**Copy (NL)**

> Security
> Security is important to us at ChargeNet.

### 3. prose-sections

- Element: `<section>`, height 1144px
- Headings (EN): H2 "ISO27001 Compliant Information Security Management System" · H2 "Secure Product Development" · H2 "Risk Minimisation and Operational Continuity" · H2 "Continuous Improvement"
- Headings (NL): H2 "ISO 27001 Compliant Information Security Management System" · H2 "Veilige Productontwikkeling" · H2 "Risicobeperking en Continuïteit" · H2 "Continue Verbetering"
- Images/backgrounds: none
- CTAs/links: 
  - "Back to Home" → /

**Copy (EN)**

> Back to Home
> Security is a fundamental part of how we design, build, and operate our platform. We understand that our customers rely on us to protect sensitive data and to ensure the availability and integrity of their operations. For that reason, cybersecurity and information security are embedded into every stage of our product development and operational processes.
> ISO27001 Compliant Information Security Management System
> We operate an Information Security Management System that is compliant with ISO 27001 principles. This structured framework helps us identify, assess, and mitigate information security risks in a consistent and auditable way. Our ISMS covers people, processes, and technology, and is continuously reviewed to address evolving threats and regulatory requirements.
> Secure Product Development
> We apply the latest cybersecurity standards and industry best practices throughout our product development lifecycle. Security requirements are considered from the design phase onward, including secure architecture, access control, and data protection measures. Regular code reviews, automated testing, and vulnerability assessments help us reduce the risk of security issues before they reach production.
> Risk Minimisation and Operational Continuity
> Our security approach is focused on minimising risk to your operations. This includes preventive controls, monitoring, and incident response procedures designed to detect and respond to potential threats quickly. By combining technical safeguards with clear internal policies and employee awareness, we aim to maintain a high level of resilience and reliability.
> Continuous Improvement
> Cybersecurity is not a one time effort. We continuously evaluate and improve our security controls based on risk assessments, technological developments, and changes in the threat landscape. This ensures that our platform remains secure, trustworthy, and aligned with current security standards.
> Last Updated: January 16th, 2026

**Copy (NL)**

> Back to Home
> Beveiliging staat centraal in de manier waarop wij ons platform ontwerpen, ontwikkelen en beheren. Wij begrijpen dat onze klanten op ons vertrouwen voor de bescherming van gevoelige gegevens en de continuïteit van hun bedrijfsvoering. Daarom is cybersecurity integraal onderdeel van al onze ontwikkel en operationele processen.
> ISO 27001 Compliant Information Security Management System
> Wij werken met een Information Security Management System dat compliant met de ISO27001 principes. Dit gestructureerde raamwerk stelt ons in staat om informatiebeveiligingsrisico’s systematisch te identificeren, beoordelen en beheersen. Ons ISMS omvat mensen, processen en technologie, en wordt continu geëvalueerd om in te spelen op nieuwe dreigingen en regelgeving.
> Veilige Productontwikkeling
> Tijdens de volledige productontwikkelingscyclus passen wij de nieuwste cybersecurity standaarden en best practices toe. Beveiliging wordt vanaf de ontwerpfase meegenomen, waaronder veilige architectuur, toegangsbeheer en databescherming. Door middel van code reviews, geautomatiseerde tests en kwetsbaarheidsanalyses verkleinen wij het risico op beveiligingsproblemen.
> Risicobeperking en Continuïteit
> Onze beveiligingsmaatregelen zijn gericht op het minimaliseren van risico’s voor uw bedrijfsvoering. Dit omvat preventieve maatregelen, monitoring en duidelijke procedures voor incidentrespons, zodat potentiële dreigingen tijdig worden gedetecteerd en aangepakt. Door technische beveiliging te combineren met interne richtlijnen en bewustwording bij medewerkers, waarborgen wij stabiliteit en betrouwbaarheid.
> Continue Verbetering
> Cybersecurity is een continu proces. Wij verbeteren onze beveiligingsmaatregelen voortdurend op basis van risicoanalyses, technologische ontwikkelingen en veranderingen in het dreigingslandschap. Zo blijft ons platform veilig, betrouwbaar en in lijn met actuele beveiligingsstandaarden.
> Last Updated: January 16th, 2026

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
