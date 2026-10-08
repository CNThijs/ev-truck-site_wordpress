<?php
/**
 * SEO title and description per seeded page and language (Rank Math fields). Titles get " - ChargeNet" added by the
 * Rank Math template, so keep them short. Descriptions: 70-155 characters, taken from the page's own text.
 * The seeder (bin/seed-content.php) writes them unless an editor changed the field in Rank Math.
 * Dutch texts are listed in docs/translation-review.md.
 */
return array(
	'home'                         => array(
		'en' => array(
			'title'       => 'ChargeNet - Share charging infrastructure for EV trucks',
			'description' => 'ChargeNet is the platform for location owners and carrier managers to share charging infrastructure at destination, at the right price, place and time.',
		),
		'nl' => array(
			'title'       => 'ChargeNet - Laadinfrastructuur delen voor elektrische vrachtwagens',
			'description' => 'ChargeNet is het platform voor locatie-eigenaren en vervoerders om laadinfrastructuur op bestemming te delen, voor de juiste prijs, plaats en tijd.',
		),
	),
	'carriers'                     => array(
		'en' => array(
			'title'       => 'Charging for carriers',
			'description' => 'Get access to our network of privately-owned charging locations and charge your EV trucks at your destination against a lower price.',
		),
		'nl' => array(
			'title'       => 'Laden voor vervoerders',
			'description' => 'Krijg toegang tot ons netwerk van private oplaadlocaties en laad uw elektrische vrachtwagens op uw bestemming tegen een lagere prijs.',
		),
	),
	'locations'                    => array(
		'en' => array(
			'title'       => 'Charging for location owners',
			'description' => 'Join our network of privately-owned charging locations and earn by selling unused charging capacity to your partners and neighbours.',
		),
		'nl' => array(
			'title'       => 'Laden voor locatie-eigenaren',
			'description' => 'Sluit u aan bij ons netwerk van private laadlocaties en verdien door ongebruikte laadcapaciteit te verkopen aan uw partners en buren.',
		),
	),
	'about'                        => array(
		'en' => array(
			'title'       => 'About ChargeNet',
			'description' => 'We are a team focused on increasing EV truck adoption and electric heavy machinery. Meet the team and the board of advisors.',
		),
		'nl' => array(
			'title'       => 'Over ChargeNet',
			'description' => 'Wij zijn een team dat zich richt op de adoptie van elektrische vrachtwagens en elektrisch zwaar materieel. Maak kennis met het team en de adviesraad.',
		),
	),
	'careers'                      => array(
		'en' => array(
			'title'       => 'Careers',
			'description' => 'We are looking for passionate innovators to help us decarbonise road logistics and construction projects. Join our team.',
		),
		'nl' => array(
			'title'       => 'Werken bij ChargeNet',
			'description' => 'Wij zoeken gepassioneerde innovators die met ons de decarbonisatie van de logistiek en de bouwsector willen versnellen. Sluit u aan bij ons team.',
		),
	),
	'faq'                          => array(
		'en' => array(
			'title'       => 'FAQ for electric truck drivers',
			'description' => 'Answers to common questions about using the ChargeNet mobile app to charge electric trucks.',
		),
		'nl' => array(
			'title'       => 'Veelgestelde vragen voor chauffeurs',
			'description' => 'Antwoorden op veelgestelde vragen over het gebruik van de ChargeNet-app voor het opladen van elektrische vrachtwagens.',
		),
	),
	'security'                     => array(
		'en' => array(
			'title'       => 'Security',
			'description' => 'How ChargeNet designs, builds and operates its platform to protect sensitive data and keep customer operations available.',
		),
		'nl' => array(
			'title'       => 'Beveiliging',
			'description' => 'Hoe ChargeNet het platform ontwerpt, ontwikkelt en beheert om gevoelige gegevens te beschermen en de continuïteit van uw bedrijfsvoering te waarborgen.',
		),
	),
	'privacy'                      => array(
		'en' => array(
			'title'       => 'Privacy policy',
			'description' => 'How ChargeNet B.V. handles personal data on this website, in the app and in the platform, and what rights you have.',
		),
		'nl' => array(
			'title'       => 'Privacybeleid',
			'description' => 'Hoe ChargeNet B.V. omgaat met persoonsgegevens op deze website, in de app en in het platform, en welke rechten u heeft.',
		),
	),
	'cookie-policy'                => array(
		'en' => array(
			'title'       => 'Cookie policy',
			'description' => 'Which cookies chargenet.energy uses, why, and how you change or withdraw your choice at any time.',
		),
		'nl' => array(
			'title'       => 'Cookiebeleid',
			'description' => 'Welke cookies chargenet.energy gebruikt, waarom, en hoe u uw keuze op elk moment wijzigt of intrekt.',
		),
	),
	'blog'                         => array(
		'en' => array(
			'title'       => 'News',
			'description' => 'The latest trends, updates and news from our network: new charging locations, ChargeNet milestones and developments in EV trucking.',
		),
		'nl' => array(
			'title'       => 'Nieuws',
			'description' => 'De nieuwste trends, updates en nieuwtjes uit ons netwerk: nieuwe laadlocaties, ChargeNet-mijlpalen en ontwikkelingen in elektrisch vrachtvervoer.',
		),
	),
	'project-destination-charging' => array(
		'en' => array(
			'title'       => 'Destination Charging project',
			'description' => 'ChargeNet integrates with your charge points so logistic partners can charge at their destination using your EV infrastructure.',
		),
		'nl' => array(
			'title'       => 'Project Laden op Bestemming',
			'description' => 'ChargeNet integreert met uw laadpunten, zodat logistieke partners op hun bestemming kunnen opladen met uw EV-infrastructuur.',
		),
	),
	'project-chargebase'           => array(
		'en' => array(
			'title'       => 'ChargeBase project',
			'description' => 'Mobile, sustainable and powerful: the solution for emission-free construction with heavy machinery.',
		),
		'nl' => array(
			'title'       => 'Project ChargeBase',
			'description' => 'Mobiel, duurzaam en krachtig: dé oplossing voor emissievrij bouwen met zwaar materieel.',
		),
	),
	'project-ijmondaanzet'         => array(
		'en' => array(
			'title'       => 'IJmond aan ZET project',
			'description' => 'ChargeNet integrates with charge points so logistic partners can charge at their destination using shared EV infrastructure.',
		),
		'nl' => array(
			'title'       => 'Project IJmond aan ZET',
			'description' => 'ChargeNet integreert met laadpunten, zodat logistieke partners op hun bestemming kunnen opladen met gedeelde EV-infrastructuur.',
		),
	),
	'project-bouw-pow'             => array(
		'en' => array(
			'title'       => 'Bouw-Pow project',
			'description' => 'ChargeNet empowers open electric infrastructure for emission-free construction with heavy machinery.',
		),
		'nl' => array(
			'title'       => 'Project Bouw-Pow',
			'description' => 'ChargeNet maakt open elektrische infrastructuur mogelijk voor emissievrije bouw met zwaar materieel.',
		),
	),
	'rapport2027'                  => array(
		'en' => array(
			'title'       => 'Trend report 2027: the e-transition in road transport',
			'description' => 'Truck toll, ETS 2, zero-emission zones, grid congestion: how your cost per kilometre changes in the next 24 months. Built from ING, ElaadNL, Milence and RVO.',
		),
		'nl' => array(
			'title'       => 'Trendrapport 2027: de e-transitie in wegtransport',
			'description' => 'Vrachtwagenheffing, ETS 2, ZE-zones, netcongestie: hoe uw kilometerkostprijs de komende 24 maanden verandert. Opgebouwd uit ING, ElaadNL, Milence en RVO.',
		),
	),
	'contact'                      => array(
		'en' => array(
			'title'       => 'Contact us',
			'description' => 'Have a question about ChargeNet? Send us a message and we will get back to you.',
		),
		'nl' => array(
			'title'       => 'Contact',
			'description' => 'Heeft u een vraag over ChargeNet? Stuur ons een bericht, dan nemen wij contact met u op.',
		),
	),
);
