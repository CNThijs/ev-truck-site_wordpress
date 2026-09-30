# SEO baseline (live site, rendered in Chromium)

Method: each URL rendered after client-side JS, EN and NL language state. Values are what a crawler executing JS sees; a non-JS fetch only sees the static `index.html` head (identical on every URL).

## robots.txt (verbatim)

```
User-agent: *
Allow: /

# Sitemap location
Sitemap: https://chargenet.energy/sitemap.xml

# Optimize crawling
Crawl-delay: 10
```

Note: source repo's public/robots.txt additionally has `Disallow: /downloads/`; live does not.

## sitemap.xml

Full copy: `sitemap.xml` (in this folder). Live lists 23 URLs; lastmod dates are 2025 (stale). Problems:
- Lists `/terms-and-conditions` → soft 404.
- Lists only 9 of the 18 blog posts; lists none of /rapport2027.
- Lists /locaties, /vervoerder, /over-ons as separate URLs but they are duplicates of /locations, /carriers, /about (identical content, no hreflang, canonical points to `/`).
- Lists both /privacy and /privacy-policy (duplicate content).

## Site-wide findings

- **Language is not in the URL.** EN/NL is a localStorage flag; a crawler always gets English. No hreflang tags exist anywhere.
- **Canonical is wrong**: every page (including blog posts, /about, /faq) has `<link rel=canonical href="https://chargenet.energy/">` (see table). Blog posts: see per-page row.
- **Duplicate meta tags**: on every page that sets its own SEO via react-helmet (blog index, all 18 posts, /rapport2027; 63 duplicated tags counted) `description`, `og:title`, `og:url` appear twice: the static index.html value first, the page-specific value second. Parsers that take the first tag see the generic homepage values. Canonical is NOT duplicated and is the homepage on all 39 crawled URLs.
- **Structured data**: JSON-LD Organization on 2 pages, Organization + BlogPosting on all 18 posts, a Report object on /rapport2027, none elsewhere.
- **Same title on most pages** ("ChargeNet · Keep Charging Ahead"): only blog list, blog posts and rapport2027 set their own.
- **Soft 404s**: unknown URLs return HTTP 200.
- `og:image` relative path `/uploads/ChargeNet-icon.png` in static head (not absolute).

## Per page (EN state; NL differences noted)

### /about

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 About ChargeNet
  - H2 Our Story
  - H2 Our Mission
  - H3 Our Values
  - H2 Our Team
  - H2 Our Board of Advisors
- **internal links (unique):** /, /about, /blog, /location-owners, /careers, /faq, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/in/sebastiaan-de-vries-chargenet/, https://www.linkedin.com/in/thijsverwaal/, https://www.linkedin.com/in/krzepczak/, https://www.linkedin.com/company/chargenet-eu/

### /blog

- **title:** ChargeNet - ChargeNet News & Insights
- **title (NL):** ChargeNet - ChargeNet Nieuws & Inzichten
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Discover the latest trends, updates and news from our network; new charging locations, ChargeNet milestones, and key developments in EV trucking and charging technology.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Ontdek de laatste trends, updates en nieuws uit ons netwerk: nieuwe laadlocaties, ChargeNet-mijlpalen en belangrijke ontwikkelingen op het gebied van elektrische vrachtwagens en laadtechnologie.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet - ChargeNet News & Insights | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Discover the latest trends, updates and news from our network; new charging locations, ChargeNet milestones, and key developments in EV trucking and charging technology. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Maxem.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog | og:type=website ¦ website | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet - ChargeNet News & Insights | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Maxem.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ charging network, destination charging, EV trucks, electric logistics, electric construction, network of private charge points
- **structured data:** `Organization`
- **heading outline:**
  - H1 ChargeNet News & Insights
  - H3 ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity
  - H3 ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites
  - H3 ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans
  - H3 ChargeNet and LEAP24: Affordable Access to Public E-Truck Charging
  - H3 Adding a New Equans E-Mobility E-Truck Charging Site in Nieuwegein
  - H3 ChargeNet and Equans E-Mobility: Getting More Out of Charging Sites Together
  - H3 ChargeNet Featured in Transport & Logistiek Nederland
  - H3 ChargeNet pitches at the “Schoon & Emissieloos Bouwen” Market Meeting
  - H3 ChargeNet expands their use cases with the help of the Growth Accelerator Demonstration Energy Voucher
  - H3 Electric Trucks are Gaining Momentum, ING research sees Tipping Point Ahead
  - H3 ChargeNet starts pilot testing at MvS
  - H3 Growth Accelerator Demonstration Voucher Energy awarded to ChargeNet
  - H3 ChargeNet in the spotlight during the Arnhem Electricity Week
  - H3 ChargeBase @ Arnhem Electricity Week
  - H3 ChargeNet Secures Funding to Accelerate Smart Charging for Electric Trucks
  - H3 Kick-off JTF IJmond aan zet
  - H3 Visit us @ MoveEast
  - H3 ChargeNet moves to the Connectr office in Arnhem
- **internal links (unique):** /, /about, /blog, /location-owners, /blog/chargenet-and-maxem-announce-ev-truck-charging-partnership, /blog/chargenet-and-den-hartog-start-a-partnership, /blog/chargenet-present-at-charge-and-connect-event, /blog/chargenet-leap24-public-charging-partnership, /blog/chargenet-adding-new-nieuwegein-charging-site, /blog/chargenet-and-equans-e-mobility-partnership, /blog/chargenet-featured-by-transport-and-logistiek-nederland, /blog/chargenet-pitches-at-the-clean-emissionless-construction-market-meeting-of-utrecht, /blog/expand-use-cases-with-the-growth-accelerator-demonstration-energy-voucher, /blog/sales-of-electric-trucks-will-accelerate-ing-research-expects, /blog/chargenet-starts-pilot-testing-at-mvs, /blog/growth-accelerator-demo-voucher-energy-awarded-to-chargenet, /blog/chargenet-in-the-spotlight-during-the-arnhem-electricity-week-2025, /blog/chargebase-at-arhem-electricity-week-2025, /blog/chargenet-secures-funding-oostnl, /blog/kick-off-jtf-ijmond-aan-zet, /blog/move-east-2025, /blog/chargenet-moves-to-the-connectr-office-in-arnhem, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargebase-at-arhem-electricity-week-2025

- **title:** ChargeBase @ Arnhem Electricity Week - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet, Volta Energy & AVIA VOLT Join Forces to Showcase ChargeBase at Euro Demo Days
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet, Volta Energy en AVIA VOLT bundelen hun krachten om ChargeBase te presenteren op de Euro Demo Days
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeBase @ Arnhem Electricity Week - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet, Volta Energy & AVIA VOLT Join Forces to Showcase ChargeBase at Euro Demo Days | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ArnhemElectricityWeek2025Stand.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargebase-at-arhem-electricity-week-2025 | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeBase @ Arnhem Electricity Week - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ArnhemElectricityWeek2025Stand.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, collaboration
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeBase @ Arnhem Electricity Week
  - H2 ChargeNet, Volta Energy & AVIA VOLT Join Forces
  - H3 Clean power, anywhere
  - H2 How ChargeBase works:
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/posts/arnhem-electricity-week_eurodemodays-chargenet-voltaenergy-activity-7320423945470439424-Op2G, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-adding-new-nieuwegein-charging-site

- **title:** Adding a New Equans E-Mobility E-Truck Charging Site in Nieuwegein - ChargeNet
- **title (NL):** Nieuw laadplein voor e-trucks in Nieuwegein - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ New Equans E-Mobility fast chargers in Nieuwegein are now part of the ChargeNet network. Transport companies can find and book the site directly through the ChargeNet app.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Nieuwe snelladers van Equans E-Mobility in Nieuwegein maken nu deel uit van het ChargeNet-netwerk. Transporteurs vinden en boeken de locatie direct via de ChargeNet app.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ Adding a New Equans E-Mobility E-Truck Charging Site in Nieuwegein - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ New Equans E-Mobility fast chargers in Nieuwegein are now part of the ChargeNet network. Transport companies can find and book the site directly through the ChargeNet app. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/Location-Nieuwegein-Arsenaaldok.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-adding-new-nieuwegein-charging-site | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ Adding a New Equans E-Mobility E-Truck Charging Site in Nieuwegein - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/Location-Nieuwegein-Arsenaaldok.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ semi-public charging network, Equans E-Mobility, Velian, Velian charging site, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 Adding a New Equans E-Mobility E-Truck Charging Site in Nieuwegein
  - H3 From Own Fleet to Full Charging Site
  - H3 Now Live: Equans E-mobility`s Nieuwegein Location
  - H3 Ready to Charge There?
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/feed/update/urn:li:activity:7424757223597301760/?rcm=ACoAAALZZuEBCxwRl9dFw21jKpid-4dKbw3fEdU, https://velian.com, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-and-den-hartog-start-a-partnership

- **title:** ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites - ChargeNet
- **title (NL):** ChargeNet en Den Hartog Zero-Emission bundelen krachten voor meer waarde uit logistieke laadpleinen - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet and Den Hartog Zero-Emission partner up, adding Den Hartog`s e-truck charging locations to the ChargeNet network for higher utilization and extra revenue.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet en Den Hartog Zero-Emission werken samen: al hun e-truck laadinfrastructuur maakt nu deel uit van het ChargeNet-netwerk. Hogere bezetting, extra inkomsten.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet and Den Hartog Zero-Emission partner up, adding Den Hartog`s e-truck charging locations to the ChargeNet network for higher utilization and extra revenue. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Den-Hartog.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-and-den-hartog-start-a-partnership | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Den-Hartog.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ shared charging site logistics, e-truck charging network, charging site utilization, Den Hartog Zero-Emission, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites
  - H3 From Charging Facility to Network Asset
  - H3 Full Control Stays With the Site Owner
  - H3 Available Now for Den Hartog Customers
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/posts/chargenet-x-den-hartog-meer-waarde-uit-share-7465380828584263680-QT2m/, https://www.denhartogbv.com/zero-emission/, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-and-equans-e-mobility-partnership

- **title:** ChargeNet and Equans E-Mobility: Getting More Out of Charging Sites Together - ChargeNet
- **title (NL):** ChargeNet en Equans E-Mobility: samen meer halen uit laadpleinen - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet partners with Equans E-Mobility to help owners of Equans-built charging sites open them up selectively, boost utilization, and earn extra revenue.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet werkt samen met Equans E-Mobility om eigenaren van Equans-laadpleinen deze selectief open te laten stellen, met hogere bezetting en extra inkomsten.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and Equans E-Mobility: Getting More Out of Charging Sites Together - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet partners with Equans E-Mobility to help owners of Equans-built charging sites open them up selectively, boost utilization, and earn extra revenue. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Equans-E-Mobility.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-and-equans-e-mobility-partnership | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and Equans E-Mobility: Getting More Out of Charging Sites Together - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Equans-E-Mobility.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ charging site business case, public e-truck charging locations, semi-public charging network, Equans E-Mobility, charging site revenue, partnership
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet and Equans E-Mobility: Getting More Out of Charging Sites Together
  - H3 What This Partnership Delivers
  - H3 Full Control, Selective Access
  - H3 Want to Know More?
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/posts/laadinfrastructuur-logistiek-chargenet-share-7425099332753932288-Foxv/, https://equans.nl/oplossingen/e-mobility/, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-and-maxem-announce-ev-truck-charging-partnership

- **title:** ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity - ChargeNet
- **title (NL):** ChargeNet en Maxem bundelen krachten voor meer laadcapaciteit voor e-trucks - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet and Maxem partner to help transport companies share EV truck charging capacity, cut costs, and speed up fleet electrification.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet en Maxem werken samen om transportbedrijven te helpen laadcapaciteit voor e-trucks te delen, kosten te verlagen en sneller te elektrificeren.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet and Maxem partner to help transport companies share EV truck charging capacity, cut costs, and speed up fleet electrification. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Maxem.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-and-maxem-announce-ev-truck-charging-partnership | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-Maxem.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ charging capacity, shared charging infrastructure, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity
  - H3 What the ChargeNet and Maxem Partnership Means for You
  - H3 Solving the Real Bottleneck: Charging Capacity, Not Charging Points
  - H3 A Combined Approach to the Energy Transition
  - H3 Getting Started
  - H3 Frequently Asked Questions
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/posts/energietransitie-evcharging-laadinfrastructuur-share-7477989899967655938-kz5b/, https://www.maxem.io, https://www.maxem.io/oplossingen/per-sector/vervoer, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-featured-by-transport-and-logistiek-nederland

- **title:** ChargeNet Featured in Transport & Logistiek Nederland - ChargeNet
- **title (NL):** ChargeNet geïnterviewd door Transport & Logistiek Nederlan - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet co-founder Sebastiaan spoke to Transport & Logistiek Nederland about the model behind ChargeNet`s network of private e-truck charging sites.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet Featured in Transport & Logistiek Nederland - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet co-founder Sebastiaan spoke to Transport & Logistiek Nederland about the model behind ChargeNet`s network of private e-truck charging sites. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/TLN-interview-seb.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-featured-by-transport-and-logistiek-nederland | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet Featured in Transport & Logistiek Nederland - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/TLN-interview-seb.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ ChargeNet founder interview, private truck charging network Netherlands, semi-public charging network, shared charging sites logistics, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet Featured in Transport & Logistiek Nederland
  - H3 Close to Thirty Charging Sites in Two Years
  - H3 A Better Business Case for Site Owners
  - H3 Solving the Real Challenge Behind Electrification
  - H3 Looking Beyond the Dutch Border
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.nt.nl/voorpagina/2025/12/27/chargenet-op-elkaars-laadplein-je-e-truck-laden/?utm_source=chargenet&utm_medium=social&utm_campaign=transport-logistiek, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-in-the-spotlight-during-the-arnhem-electricity-week-2025

- **title:** ChargeNet in the spotlight during the Arnhem Electricity Week - ChargeNet
- **title (NL):** ChargeNet in de spotlight tijdens de Arnhem Electricity Week - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet works together with Volta Energy, and AVIA VOLT NL to present ChargeBase at the Arnhem Electricity Week 2025 in Arnhem.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet werkt samen met Volta Energy en AVIA VOLT NL om ChargeBase te presenteren tijdens de Arnhem Electricity Week 2025 in Arnhem.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet in the spotlight during the Arnhem Electricity Week - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet works together with Volta Energy, and AVIA VOLT NL to present ChargeBase at the Arnhem Electricity Week 2025 in Arnhem. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ArnhemElectricityWeek2025Interview.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-in-the-spotlight-during-the-arnhem-electricity-week-2025 | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet in the spotlight during the Arnhem Electricity Week - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ArnhemElectricityWeek2025Interview.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, event
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet in the spotlight during the Arnhem Electricity Week
  - H2 ChargeNet at Arnhem Electricity Week 2025: Showcasing ChargeBase together with Volta Energy & AVIA VOLT
- **internal links (unique):** /, /about, /blog, /location-owners, /blog/chargenet-in-the-spotlight-during-the-arnhem-electricity-week-2025, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-leap24-public-charging-partnership

- **title:** ChargeNet and LEAP24: Affordable Access to Public E-Truck Charging - ChargeNet
- **title (NL):** ChargeNet en LEAP24: toegang tot publieke laadpleinen met voordeel - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet`s network now includes affordable access to LEAP24`s public e-truck charging sites, giving transport companies more flexibility on the road.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Het ChargeNet-netwerk breidt uit met voordelige toegang tot de publieke e-truck laadpleinen van LEAP24. Meer flexibiliteit en lagere laadkosten onderweg.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and LEAP24: Affordable Access to Public E-Truck Charging - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet`s network now includes affordable access to LEAP24`s public e-truck charging sites, giving transport companies more flexibility on the road. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-LEAP24.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-leap24-public-charging-partnership | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet and LEAP24: Affordable Access to Public E-Truck Charging - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNet-and-LEAP24.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ public e-truck charging locations, semi-public charging network, LEAP24, one charging card logistics, partnership
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet and LEAP24: Affordable Access to Public E-Truck Charging
  - H3 One Network, More Places to Charge
  - H3 Why This Matters for Route Planning
  - H3 Strengthening the ChargeNet Network
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/posts/emobility-laadinfrastructuur-logistiek-share-7446931875333349376-T71_/, https://leap24.eu, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-moves-to-the-connectr-office-in-arnhem

- **title:** ChargeNet moves to the Connectr office in Arnhem - ChargeNet
- **title (NL):** ChargeNet verhuisd naar het Connectr office in Arnhem - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet ontvangt JTFIJM-financiering om emissievrije bouwplaatsen te ontwikkelen, wat duurzame bouwpraktijken bevordert.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet moves to the Connectr office in Arnhem - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/Connectr-Arnhem.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-moves-to-the-connectr-office-in-arnhem | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet moves to the Connectr office in Arnhem - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/Connectr-Arnhem.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, update
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet moves to the Connectr office in Arnhem
  - H2 THE Hub for Collaboration and Clean Energy Innovation
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.connectr.nu/innovation-lab-shared-office/, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-pitches-at-the-clean-emissionless-construction-market-meeting-of-utrecht

- **title:** ChargeNet pitches at the “Schoon & Emissieloos Bouwen” Market Meeting - ChargeNet
- **title (NL):** ChargeNet pitcht tijdens de “Schoon & Emissieloos Bouwen” Marktbijeenkomst - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet co-founder pitches at the “Schoon & Emissieloos Bouwen” Market Meeting of Gemeente Utrecht
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet co-founder pitcht tijdens de “Schoon & Emissieloos Bouwen” Marktbijeenkomst van de Gemeente Utrecht
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet pitches at the “Schoon & Emissieloos Bouwen” Market Meeting - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet co-founder pitches at the “Schoon & Emissieloos Bouwen” Market Meeting of Gemeente Utrecht | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/SchoonEnEmissieloosBouwenMarktbijeenkomstUtrecht102025.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-pitches-at-the-clean-emissionless-construction-market-meeting-of-utrecht | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet pitches at the “Schoon & Emissieloos Bouwen” Market Meeting - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/SchoonEnEmissieloosBouwenMarktbijeenkomstUtrecht102025.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ electric heavy machinery, clean & emission-free construction, construction, sustainability, collaboration, event
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet pitches at the “Schoon & Emissieloos Bouwen” Market Meeting
  - H3 Empowering zero-emission construction through smart charging
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/posts/radboudvanderlinden_spukseb-sseb-emissieloosbouwen030-activity-7383950457053851648-WFf8, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-present-at-charge-and-connect-event

- **title:** ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans - ChargeNet
- **title (NL):** ChargeNet op Charge & Connect: van route naar concreet laadplan - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet joined the Charge & Connect event by Connectr, offering transport companies a live analysis of their e-routes and access to the ChargeNet charging network.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet aanwezig op Charge & Connect van Connectr en bood transporteurs een live analyse van hun e-routes en toegang tot het ChargeNet-laadnetwerk.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet joined the Charge & Connect event by Connectr, offering transport companies a live analysis of their e-routes and access to the ChargeNet charging network. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeAndConnect2026.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-present-at-charge-and-connect-event | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeAndConnect2026.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ charge & connect, connectr, e-route analysis, event
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans
  - H3 From Fuel Planning to Charging Strategy
  - H3 A Day Full of Real Experience
  - H3 Missed the Event? You Can Still Get Your Route Analysis
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.connectr.nu/events/charge-connect/, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-secures-funding-oostnl

- **title:** ChargeNet Secures Funding to Accelerate Smart Charging for Electric Trucks - ChargeNet
- **title (NL):** ChargeNet krijgt financiering om slim opladen voor elektrische vrachtwagens te versnellen - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet ontvangt JTFIJM-financiering om emissievrije bouwplaatsen te ontwikkelen, wat duurzame bouwpraktijken bevordert.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet Secures Funding to Accelerate Smart Charging for Electric Trucks - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/OostNLFinancing.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-secures-funding-oostnl | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet Secures Funding to Accelerate Smart Charging for Electric Trucks - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/OostNLFinancing.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet Secures Funding to Accelerate Smart Charging for Electric Trucks
  - H2 Vroegefasefonds Gelderland
  - H3 What the Funding Enables
  - H2 Strategic re-location to Cleantech Park Arnhem
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://oostnl.nl/nieuws-overzicht/financiering-voor-chargenet, https://www.linkedin.com/company/chargenet-eu/

### /blog/chargenet-starts-pilot-testing-at-mvs

- **title:** ChargeNet starts pilot testing at MvS - ChargeNet
- **title (NL):** ChargeNet start met pilot testen bij MvS - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ After thorough preparation, ChargeNet and Millenaar van Schaik has carried out the first charging sessions on shared charging infrastructure with electric trucks from MvS.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet heeft na grondige voorbereiding samen met Millenaar van Schaik de eerste laadsessies op gedeelde private laadinfrastructuur met elektrische trucks van MvS uitgevoerd.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet starts pilot testing at MvS - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ After thorough preparation, ChargeNet and Millenaar van Schaik has carried out the first charging sessions on shared charging infrastructure with electric trucks from MvS. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/MvStesting02.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/chargenet-starts-pilot-testing-at-mvs | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet starts pilot testing at MvS - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/MvStesting02.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ charging, EV trucks, emission-free, sustainability, product
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet starts pilot testing at MvS
  - H2 Connecting the Future of Zero Emission Logistics
  - H2 First test results
  - H2 Project goal
  - H2 Expected project outcomes
- **internal links (unique):** /, /about, /blog, /location-owners, /blog/chargenet-starts-pilot-testing-at-mvs, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /blog/expand-use-cases-with-the-growth-accelerator-demonstration-energy-voucher

- **title:** ChargeNet expands their use cases with the help of the Growth Accelerator Demonstration Energy Voucher - ChargeNet
- **title (NL):** ChargeNet breidt use cases uit met behulp van de Groeiversneller Demonstratie Energie Voucher - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ The Energy Demonstration Growth Accelerator was awarded to ChargeNet in May. A look back at the opportunities this grant has offered us.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ De Groeiversneller Demonstratie Energie werd in mei uitgereikt aan ChargeNet. Een terugblik wat deze subsidie ons voor een mogelijkheden heeft geboden.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet expands their use cases with the help of the Growth Accelerator Demonstration Energy Voucher - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ The Energy Demonstration Growth Accelerator was awarded to ChargeNet in May. A look back at the opportunities this grant has offered us. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/GroeiversnellerDemonstratieEnergie2025.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/expand-use-cases-with-the-growth-accelerator-demonstration-energy-voucher | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet expands their use cases with the help of the Growth Accelerator Demonstration Energy Voucher - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/GroeiversnellerDemonstratieEnergie2025.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ electric trucks, logistics, sustainability, ING research, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 ChargeNet expands their use cases with the help of the Growth Accelerator Demonstration Energy Voucher
  - H3 From Idea to Demonstration
  - H3 An Extension of the ChargeNet Platform
  - H3 Powered by Collaboration
  - H3 Looking Ahead
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.connectr.nu/actueel/chargenet-maakt-een-uitstap-met-behulp-van-de-groeiversneller-demonstratie-energie/, https://www.linkedin.com/company/chargenet-eu/

### /blog/growth-accelerator-demo-voucher-energy-awarded-to-chargenet

- **title:** Growth Accelerator Demonstration Voucher Energy awarded to ChargeNet - ChargeNet
- **title (NL):** Groei Acceleratie Demonstratie Voucher Energie uitgereikt aan ChargeNet - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTF funding to develop emission-free construction sites, advancing sustainable building practices.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet ontvangt JTF-financiering om emissievrije bouwplaatsen te ontwikkelen, wat duurzame bouwpraktijken bevordert.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ Growth Accelerator Demonstration Voucher Energy awarded to ChargeNet - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTF funding to develop emission-free construction sites, advancing sustainable building practices. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/OostNLGroeiversneller.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/growth-accelerator-demo-voucher-energy-awarded-to-chargenet | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ Growth Accelerator Demonstration Voucher Energy awarded to ChargeNet - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/OostNLGroeiversneller.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, accelerate
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 Growth Accelerator Demonstration Voucher Energy awarded to ChargeNet
  - H2 Demonstration Energy Innovation Voucher by Connectr and OostNL
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.connectr.nu/actueel/de-groeiversneller-demonstratie-energie-uitgereikt-aan-chargenet/, https://www.linkedin.com/company/chargenet-eu/

### /blog/kick-off-jtf-ijmond-aan-zet

- **title:** Kick-off JTF IJmond aan zet - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTF funding to develop emission-free construction sites, advancing sustainable building practices.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet ontvangt JTF-financiering om emissievrije bouwplaatsen te ontwikkelen, wat duurzame bouwpraktijken bevordert.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ Kick-off JTF IJmond aan zet - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTF funding to develop emission-free construction sites, advancing sustainable building practices. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNetJTFIJmondAanZet.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/kick-off-jtf-ijmond-aan-zet | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ Kick-off JTF IJmond aan zet - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/ChargeNetJTFIJmondAanZet.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, collaboration
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 Kick-off JTF IJmond aan zet
  - H2 The challenge in IJmond: sustainability under pressure
  - H2 A holistic approach: education, infrastructure & smart technology
  - H2 ChargeNet’s role within IJmond aan Zet
  - H2 Sustainable mobility is a shared responsibility
- **internal links (unique):** /, /about, /blog, /location-owners, /blog/kick-off-jtf-ijmond-aan-zet, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /blog/move-east-2025

- **title:** Visit us @ MoveEast - ChargeNet
- **title (NL):** Loop bij ons langs @ MoveEast - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet ontvangt JTFIJM-financiering om emissievrije bouwplaatsen te ontwikkelen, wat duurzame bouwpraktijken bevordert.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ Visit us @ MoveEast - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/MoveEast.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/move-east-2025 | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ Visit us @ MoveEast - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/MoveEast.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ funding, construction, emission-free, sustainability, event
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 Visit us @ MoveEast
  - H2 ChargeNet @ Move East: Shaping the Future of Sustainable Mobility
  - H2 The Netherlands in 2030
  - H2 E-Mobility for the Future
  - H2 ChargeNet’s Contribution
  - H2 A Step Forward for Clean Mobility
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.gelderland.nl/nieuws/move-east-focus-op-duurzame-zakelijke-mobiliteit, https://www.linkedin.com/company/chargenet-eu/

### /blog/sales-of-electric-trucks-will-accelerate-ing-research-expects

- **title:** Electric Trucks are Gaining Momentum, ING research sees Tipping Point Ahead - ChargeNet
- **title (NL):** Elektrische trucks versnellen de toekomst van transport, ING research ziet omslagpunt naderen - ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ING research shows electric truck adoption will accelerate significantly by 2030, with transport costs for diesel rising up to 12%.
- **meta description (NL):** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ING-onderzoek toont aan dat de adoptie van elektrische vrachtwagens tegen 2030 aanzienlijk zal versnellen, met transportkosten voor diesel die tot 12% stijgen.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ Electric Trucks are Gaining Momentum, ING research sees Tipping Point Ahead - ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ING research shows electric truck adoption will accelerate significantly by 2030, with transport costs for diesel rising up to 12%. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/INGresearchEVTrucks.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/blog/sales-of-electric-trucks-will-accelerate-ing-research-expects | og:type=website ¦ article | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ Electric Trucks are Gaining Momentum, ING research sees Tipping Point Ahead - ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/blog/INGresearchEVTrucks.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ electric trucks, logistics, sustainability, ING research, business
- **structured data:** `Organization`, `BlogPosting`
- **heading outline:**
  - H1 Electric Trucks are Gaining Momentum, ING research sees Tipping Point Ahead
  - H3 Diesel is getting more expensive, electric trucks are becoming more attractive
  - H3 Electric truck fleet set to grow, from 1,500 to 25,000 by 2030
  - H4 Aantal elektrische trucks op de Nederlandse wegen gaat fors groeien met de dieselheffingen en terugsluis voor subsidie
  - H3 Charging infrastructure remains a challenge, but depot charging offers a solution
  - H3 New tech means new ways of operating
  - H3 A wave of fleet replacements is coming, and investments are ramping up
  - H2 The acceleration has begun
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.ing.nl/zakelijk/sector/transport-logistics-mobility/assetvisie-trucks-2025, https://www.linkedin.com/company/chargenet-eu/

### /careers

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Join our Team
  - H2 Why Join ChargeNet?
  - H3 Innovation
  - H3 Impact
  - H3 Growth
  - H3 Contact Our CEO
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/in/sebastiaan-de-vries-chargenet/, https://www.linkedin.com/company/chargenet-eu/

### /carriers

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Expand your charging opportunities today
  - H2 Reduce your down-time: Start charging on you destination!
  - H2 Our Benefits
  - H4 Reduce charging costs
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add your Drivers & Plan their Routes
  - H4 Key Activities:
  - H3 Download the app & Start Charging
  - H4 Key Activities:
  - H3 Optimize costs with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today & start charging on your destination!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /faq

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Frequently Asked Questions for Electric Truck Drivers
  - H3 Where can I download the ChargeNet driver app?
  - H3 How can I get access to the ChargeNet driver app?
  - H3 How do I find charging stations compatible with my electric truck?
  - H3 Can I reserve a charging station in advance for my delivery route?
  - H3 How do I track my charging costs and create expense reports?
  - H3 What should I do if a charging station seems available in the app but turns out to be out of order?
  - H3 How long does it typically take to charge an electric truck?
  - H3 Can I monitor my truck's charging progress remotely?
  - H3 How do I set up fleet billing for multiple trucks?
  - H3 What payment methods are accepted through the app?
  - H3 What support is available if I need help during charging?
  - H3 How can I request to delete my account and associated data?
  - H3 Still have questions?
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy ¦ https://chargenet.energy/ | og:type=website ¦ website | og:locale=en_US ¦ en_US
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP ¦ EV trucks, EV truck charging, charging network, eMSP
- **structured data:** `Organization`
- **heading outline:**
  - H1 Affordable & reliable EV truck charging at your destination
  - H2 What ChargeNet does for You
  - H3 Location Managers
  - H4 Key Benefits
  - H4 Platform Features
  - H3 Fleet Managers
  - H4 Key Benefits
  - H4 Platform Features
  - H2 Why ChargeNet?
  - H3 3.8Bn tonne CO₂
  - H3 18%
  - H3 80%
  - H3 80%
  - H3 What ChargeNet Does for You
  - H4 Reduce charging costs
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 Sharing EV charging infrastructure
  - H3 Accessible EV infrastructure on your destination
  - H3 Accessible EV infrastructure on your destination
  - H3 Emission-free construction with heavy machinery
  - H3 Emission-free construction with heavy machinery
  - H3 Accessible EV infrastructure for logistics
  - H3 Accessible EV infrastructure for logistics
  - H3 Enable accessible EV infrastructure on constrution sites
  - H3 Enable accessible EV infrastructure on constrution sites
  - H2 How to Get Started
  - H3 Locations
  - H4 Registration
  - H4 Configuration
  - H4 Monitization
  - H3 Carriers
  - H4 Join our Network
  - H4 Plan Routes
  - H4 Start Charging
  - H2 Latest News
  - H3 ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity
  - H3 ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites
  - H3 ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans
  - H3 ChargeNet and Maxem Partner to Unlock More EV Truck Charging Capacity
  - H3 ChargeNet and Den Hartog Zero-Emission Team Up for More Valuable Logistics Charging Sites
  - H3 ChargeNet at Charge & Connect: Turning Routes Into Real Charging Plans
- **internal links (unique):** /, /about, /blog, /location-owners, /locations, /carriers, /projects/destination-charging, /projects/chargebase, /projects/ijmondaanzet, /projects/bouw-pow, /blog/chargenet-and-maxem-announce-ev-truck-charging-partnership, /blog/chargenet-and-den-hartog-start-a-partnership, /blog/chargenet-present-at-charge-and-connect-event, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://apps.apple.com/us/app/chargenet-driver/id6745788861, https://play.google.com/store/apps/details?id=energy.chargenet.app, https://www.linkedin.com/company/chargenet-eu/

### /locatie

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Solve netcongestion & start earning!
  - H2 Sell unused charging capacity to your partners & neighbours
  - H2 Our Benefits
  - H4 Competitive pricing
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add locations & connect your charge points
  - H4 Key Activities:
  - H3 Configure location settings
  - H4 Key Activities:
  - H3 Optimize revenue with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today to help solving netcongestion & start earning!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /locaties

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Solve netcongestion & start earning!
  - H2 Sell unused charging capacity to your partners & neighbours
  - H2 Our Benefits
  - H4 Competitive pricing
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add locations & connect your charge points
  - H4 Key Activities:
  - H3 Configure location settings
  - H4 Key Activities:
  - H3 Optimize revenue with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today to help solving netcongestion & start earning!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /locations

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Solve netcongestion & start earning!
  - H2 Sell unused charging capacity to your partners & neighbours
  - H2 Our Benefits
  - H4 Competitive pricing
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add locations & connect your charge points
  - H4 Key Activities:
  - H3 Configure location settings
  - H4 Key Activities:
  - H3 Optimize revenue with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today to help solving netcongestion & start earning!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /opbrengst

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Solve netcongestion & start earning!
  - H2 Sell unused charging capacity to your partners & neighbours
  - H2 Our Benefits
  - H4 Competitive pricing
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add locations & connect your charge points
  - H4 Key Activities:
  - H3 Configure location settings
  - H4 Key Activities:
  - H3 Optimize revenue with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today to help solving netcongestion & start earning!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /over-ons

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 About ChargeNet
  - H2 Our Story
  - H2 Our Mission
  - H3 Our Values
  - H2 Our Team
  - H2 Our Board of Advisors
- **internal links (unique):** /, /about, /blog, /location-owners, /careers, /faq, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/in/sebastiaan-de-vries-chargenet/, https://www.linkedin.com/in/thijsverwaal/, https://www.linkedin.com/in/krzepczak/, https://www.linkedin.com/company/chargenet-eu/

### /privacy-policy

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Privacy Policy
  - H2 About us
  - H2 Scope us
  - H2 Responsibility
  - H2 How we obtain your personal data
  - H2 Details of processing
  - H2 Cookies
  - H2 Sharing with third parties
  - H2 Transfer to countries outside the EEA
  - H2 Security
  - H2 Retention periods
  - H2 Your rights (incl. the right to object)
  - H2 Contact details
  - H2 Miscellaneous
  - H2 Definitions
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /privacy

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Privacy Policy
  - H2 About us
  - H2 Scope us
  - H2 Responsibility
  - H2 How we obtain your personal data
  - H2 Details of processing
  - H2 Cookies
  - H2 Sharing with third parties
  - H2 Transfer to countries outside the EEA
  - H2 Security
  - H2 Retention periods
  - H2 Your rights (incl. the right to object)
  - H2 Contact details
  - H2 Miscellaneous
  - H2 Definitions
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /rapport2027

- **title:** Trendrapport 2027 | ChargeNet
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Ontdek welke ontwikkelingen jouw organisatie in 2027 écht gaan veranderen. Download het gratis Trend 2026 rapport van ChargeNet.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1 ¦ index, follow
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead ¦ Trendrapport 2027 | ChargeNet | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. ¦ Ontdek welke ontwikkelingen jouw organisatie in 2027 écht gaan veranderen. Download het gratis Trendrapport 2027 van ChargeNet. | og:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/trend2026-cover.jpg | og:url=https://chargenet.energy ¦ https://chargenet.energy/rapport2027/ | og:type=website ¦ website | og:locale=en_US ¦ nl_NL
- **Twitter:** twitter:card=summary_large_image ¦ summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead ¦ Trendrapport 2027 | ChargeNet | twitter:image=/uploads/ChargeNet-icon.png ¦ https://chargenet.energy/uploads/trend2026-cover.jpg
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** `Report`
- **heading outline:**
  - H1 De cijfers achter de e-transitie in het Nederlandse wegtransport
  - H2 Wat levert het Trendrapport 2027 u op?
  - H3 Vrachtwagenheffing tot €0,195/km, vanaf medio 2026
  - H3 Accijnskorting diesel vervalt eind 2026
  - H3 ETS 2 vanaf 2027: CO2-prijs aan de pomp
  - H3 AanZET-subsidie verdriedubbelt voor e-trucks
  - H3 Bijna 30 steden met ZE-zones tot 2030
  - H2 Ontvang het Trendrapport 2027
- **internal links (unique):** /, /about, /blog, /location-owners, /privacy-policy, /faq, /careers, /security, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /routecheck

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Expand your charging opportunities today
  - H2 Reduce your down-time: Start charging on you destination!
  - H2 Our Benefits
  - H4 Reduce charging costs
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add your Drivers & Plan their Routes
  - H4 Key Activities:
  - H3 Download the app & Start Charging
  - H4 Key Activities:
  - H3 Optimize costs with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today & start charging on your destination!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /security

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Security
  - H2 ISO27001 Compliant Information Security Management System
  - H2 Secure Product Development
  - H2 Risk Minimisation and Operational Continuity
  - H2 Continuous Improvement
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /terms-and-conditions

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 404
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /this-page-does-not-exist

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 404
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /trend2026

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 404
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /vervoerder

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Expand your charging opportunities today
  - H2 Reduce your down-time: Start charging on you destination!
  - H2 Our Benefits
  - H4 Reduce charging costs
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add your Drivers & Plan their Routes
  - H4 Key Activities:
  - H3 Download the app & Start Charging
  - H4 Key Activities:
  - H3 Optimize costs with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today & start charging on your destination!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

### /vervoerders

- **title:** ChargeNet · Keep Charging Ahead
- **meta description:** ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time.
- **canonical:** https://chargenet.energy/
- **hreflang:** none
- **html lang:** en
- **robots:** index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1
- **Open Graph:** og:title=ChargeNet · Keep Charging Ahead | og:description=ChargeNet is revolutionizing the electrification of the trucking industry, one charge at a time. | og:image=/uploads/ChargeNet-icon.png | og:url=https://chargenet.energy | og:type=website | og:locale=en_US
- **Twitter:** twitter:card=summary_large_image | twitter:title=ChargeNet · Keep Charging Ahead | twitter:image=/uploads/ChargeNet-icon.png
- **keywords:** EV trucks, EV truck charging, charging network, eMSP
- **structured data:** none
- **heading outline:**
  - H1 Expand your charging opportunities today
  - H2 Reduce your down-time: Start charging on you destination!
  - H2 Our Benefits
  - H4 Reduce charging costs
  - H4 One Platform
  - H4 Control & Flexibility
  - H4 Easy to Use
  - H4 Private access
  - H4 Security by Design
  - H2 How it works
  - H3 Sign Up to Join our Network
  - H4 Key Activities:
  - H3 Add your Drivers & Plan their Routes
  - H4 Key Activities:
  - H3 Download the app & Start Charging
  - H4 Key Activities:
  - H3 Optimize costs with pricing proposals
  - H4 Key Activities:
  - H3 Automated administration & billing process
  - H4 Key Activities:
  - H2 ChargeNet; the private charging network for you!
  - H3 Join today & start charging on your destination!
- **internal links (unique):** /, /about, /blog, /location-owners, /faq, /careers, /security, /privacy-policy, /blog/chargenet-starts-pilot-testing-at-mvs/, /blog/chargenet-secures-funding-oostnl/
- **external links (unique):** https://portal.chargenet.energy, https://www.linkedin.com/company/chargenet-eu/

## Structured data (full JSON-LD as rendered: one BlogPosting post + the Report page)

```json
{"@context":"https://schema.org","@type":"Organization","name":"ChargeNet","url":"https://chargenet.energy","logo":"https://chargenet.energy/img/ChargeNet-icon.png","description":"ChargeNet is the platform for location owners & carrier managers to collaborate on decarbonisation of road logistics by sharing charging infrastructure at destination.","contactPoint":{"@type":"ContactPoint","contactType":"customer service","email":"info@chargenet.energy"},"sameAs":["https://www.linkedin.com/company/chargenet-eu/"]}
{"@context":"https://schema.org","@type":"BlogPosting","mainEntityOfPage":{"@type":"WebPage","@id":"https://chargenet.energy/blog/move-east-2025"},"headline":"Visit us @ MoveEast - ChargeNet","image":{"@type":"ImageObject","url":"https://chargenet.energy/uploads/blog/MoveEast.jpg","width":1200,"height":630},"datePublished":"2024-10-10T22:00:00.000Z","dateModified":"2024-10-10T22:00:00.000Z","author":{"@type":"Organization","name":"ChargeNet","url":"https://chargenet.energy"},"publisher":{"@type":"Organization","name":"ChargeNet","logo":{"@type":"ImageObject","url":"https://chargenet.energy/img/ChargeNet-icon.png","width":512,"height":512},"url":"https://chargenet.energy"},"description":"ChargeNet receives JTFIJM funding to develop emission-free construction sites, advancing sustainable building practices.","keywords":"funding, construction, emission-free, sustainability","articleSection":"Event","inLanguage":"en-US","isAccessibleForFree":true}
{"@context":"https://schema.org","@type":"Report","name":"Trend 2026","description":"Welke ontwikkelingen gaan jouw organisatie in 2027 écht veranderen? Het Trendrapport 2027 van ChargeNet.","url":"https://chargenet.energy/rapport2027/","publisher":{"@type":"Organization","name":"ChargeNet","url":"https://chargenet.energy"},"inLanguage":"nl","isAccessibleForFree":true}
```
