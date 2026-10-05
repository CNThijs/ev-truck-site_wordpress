<?php
/**
 * Home. English first, Dutch is the live copy (typos fixed; English leftovers in the Dutch live text are filled in
 * and listed in docs/translation-review.md).
 */
return array(
	'key'    => 'home',
	'slugs'  => array(
		'en' => 'home',
		'nl' => 'home',
	),
	'titles' => array(
		'en' => 'Home',
		'nl' => 'Home',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'hero_h'      => 'Affordable & reliable EV truck charging at your destination',
				'hero_t'      => 'ChargeNet is the platform for location owners & carrier managers to collaborate on decarbonisation of road logistics by sharing charging infrastructure at destination.<br>With ChargeNet you can charge at the right price, at the right place, at the right time.',
				'hero_b1'     => 'Explore Use Cases',
				'hero_b2'     => 'Contact Us',
				'f_h'         => 'What ChargeNet does for You',
				'f_t'         => 'We bring locations with charging infrastructure and EV fleets together on one platform.',
				'benefits'    => 'Key Benefits',
				'features'    => 'Platform Features',
				'loc_t'       => 'Location Managers',
				'loc_tag'     => 'Monetize your charging infrastructure efficiently',
				'loc_b'       => array(
					array( 'Make private charging infrastructure accessible', 'Give limited access to your private charging infrastructure to your partners' ),
					array( 'Access Control', 'Secure authentication and authorization for different user groups' ),
					array( 'Pricing proposals for frequent visitors', 'Contractual pricing agreements for even better prices per kWh against a fixed minimum capacity' ),
					array( 'Automated Billing', 'Seamless payment processing and invoice generation' ),
				),
				'loc_f'       => array( 'Location Dashboard', 'Access Management', 'Revenue Tracking', 'Pricing Proposals', 'Usage Analytics', 'Billing Automation' ),
				'loc_l'       => 'Explore Location Benefits',
				'fleet_t'     => 'Fleet Managers',
				'fleet_tag'   => 'Complete fleet oversight and optimization',
				'fleet_b'     => array(
					array( 'Access to private charging infrastructure', 'Get access to private charging infrastructure from our network' ),
					array( 'Cost Control', 'Detailed analytics on energy consumption, charging costs, and operational efficiency' ),
					array( 'Pricing proposals for frequent visitors', 'Contractual pricing agreements for even better prices per kWh against a fixed minimum capacity' ),
					array( 'Automated Billing', 'Seamless payment processing and invoice generation' ),
				),
				'fleet_f'     => array( 'Fleet Dashboard', 'Mobile Driver App', 'Cost Analytics', 'Pricing Proposals', 'Reporting Tools', 'Billing Automation' ),
				'fleet_l'     => 'Explore Carrier Benefits',
				'w_h'         => 'Why ChargeNet?',
				'w_t'         => 'We bring locations with private charging infrastructure and EV fleets together on one platform.',
				'stats'       => array(
					array( '3.8', 'Bn', 'tonne CO₂: Average truck pollution of 7.0 g/mi pollution (NOx), 100x more than a gasoline car' ),
					array( '18', '%', 'of all emissions in the EU comes from road freight transport' ),
					array( '80', '%', 'Fuel costs are rising and becoming more volatile' ),
					array( '80', '%', 'Fossil fuel trucks are cheapest to buy, but have higher maintenance and running costs during their life-cycle.' ),
				),
				'd_h'         => 'What ChargeNet Does for You',
				'd_t'         => 'We link fleets of EV trucks to nearby locations with private charging infrastructure on their destination.',
				'does'        => array(
					array( 'euro', 'Reduce charging costs', 'Reduce up to 60% of your charging costs compared to roaming or public charging.' ),
					array( 'layers', 'One Platform', 'Work with an unlimited number of partners, all on one platform for your whole supply chain.' ),
					array( 'sliders', 'Control & Flexibility', 'Fully adaptable to your wishes on location and partner level, as each relation is unique.' ),
					array( 'smartphone', 'Easy to Use', 'Our platform is uniquely developed to collaborate in the 3PL road transport relationship.' ),
					array( 'lock', 'Private access', 'Get access to privately-owned charging locations that are part of the ChargeNet network.' ),
					array( 'shield-check', 'Security by Design', 'We apply the latest cybersecurity standards in our product development to minimise risk to your operations.' ),
				),
				'platform_l'  => 'Learn More About Our Platform',
				'p_e'         => 'Projects',
				'p_h'         => 'Sharing EV charging infrastructure',
				'p_t'         => 'Explore how our ChargeNet platform enables various industries to accelerate adoption of EV trucks and electric heavy machinery, tailored to their specific needs.',
				'more'        => 'Read More',
				'projects'    => array(
					array( 'project-destination', 'project-destination-charging', 'Accessible EV infrastructure on your destination', 'ChargeNet integrates with your charge points to enable logistic partners to charge on their destination using your EV infrastructure.', 'ChargeNet, Millenaar van Schaik, Schiphol' ),
					array( 'project-chargebase', 'project-chargebase', 'Emission-free construction with heavy machinery', 'Mobile, sustainable and powerful: THE solution for emission-free construction with heavy machinery.', 'ChargeNet, Volta Energy, AVIA Volt, Construction, Arnhem Electricity Week' ),
					array( 'project-ijmond', 'project-ijmondaanzet', 'Accessible EV infrastructure for logistics', 'ChargeNet integrates with your charge points to enable logistic partners to charge on their destination using your EV infrastructure.', 'ChargeNet, Brankenhoff, JTF: Kansen voor West' ),
					array( 'project-bouwpow', 'project-bouw-pow', 'Enable accessible EV infrastructure on construction sites', 'ChargeNet empowers open electric infrastructure for emission-free construction with heavy machinery.', 'ChargeNet, Unitial, EV infrastructure, Construction' ),
				),
				'g_e'         => 'Getting Started',
				'g_h'         => 'How to Get Started',
				'g_t'         => 'Explore how our ChargeNet platform enables various industries to accelerate adoption of EV trucks and electric heavy machinery, tailored to their specific needs.',
				'g_loc'       => array(
					'Locations',
					'Monetize your existing charging infrastructure efficiently.',
					array(
						array( 'Registration', 'Sign up and register your locations.' ),
						array( 'Configuration', 'Set up charging infrastructure on our network.' ),
						array( 'Monetization', 'Start earning from your existing charging infrastructure.' ),
					),
				),
				'g_car'       => array(
					'Carriers',
					'Access charging network and optimize fleet operations.',
					array(
						array( 'Join our Network', 'Connect to the ChargeNet platform' ),
						array( 'Plan Routes', 'Optimize charging along your routes.' ),
						array( 'Start Charging', 'Access charging points across the network.' ),
					),
				),
				'n_e'         => 'News',
				'n_h'         => 'Latest News',
				'n_t'         => 'Discover the latest trends, updates and news from our network; new charging locations, ChargeNet milestones, and key developments in EV trucking and charging technology.',
				'n_l'         => 'View all posts',
			),
			'nl' => array(
				'hero_h'      => 'Betaalbaar & betrouwbaar uw EV vrachtwagen laden op bestemming',
				'hero_t'      => 'ChargeNet is het platform voor locatie-eigenaren & vervoerders om samen te werken aan decarbonisatie van wegtransport door het delen van laadinfrastructuur op bestemming.<br>Met ChargeNet kunt u laden voor de juiste prijs, op de juiste plaats, op het juiste moment.',
				'hero_b1'     => 'Meer weten',
				'hero_b2'     => 'Neem Contact Op',
				'f_h'         => 'Wat ChargeNet voor u doet',
				'f_t'         => 'Wij brengen locaties met laadinfrastructuur en EV-vloten samen op één platform.',
				'benefits'    => 'Belangrijkste voordelen',
				'features'    => 'Platformfuncties',
				'loc_t'       => 'Locaties',
				'loc_tag'     => 'Monetariseer uw laadinfrastructuur efficiënt',
				'loc_b'       => array(
					array( 'Maak private laadinfrastructuur bereikbaar', 'Geef beperkt toegang tot uw private laadinfrastructuur aan uw partners' ),
					array( 'Toegangsbeheer', 'Volledige controle over de toegang van gebruikers van verschillende partners' ),
					array( 'Prijsvoorstellen voor frequente bezoekers', 'Contractuele prijsafspraken voor nog betere prijzen per kWh tegen een vaste minimale capaciteit' ),
					array( 'Geautomatiseerde back-office processen', 'Geautomatiseerde rapportages en facturatie' ),
				),
				'loc_f'       => array( 'Locatiedashboard', 'Toegangsbeheer', 'Omzetoverzicht', 'Prijsvoorstellen', 'Gebruiksanalyses', 'Automatische facturatie' ),
				'loc_l'       => 'Lees meer over locaties',
				'fleet_t'     => 'Vervoerders',
				'fleet_tag'   => 'Volledig wagenpark overzicht en optimalisatie',
				'fleet_b'     => array(
					array( 'Toegang tot private laadinfrastructuur', 'Krijg toegang tot private laadinfrastructuur uit ons netwerk' ),
					array( 'Kostenbeheersing', 'Gedetailleerde analyses van energieverbruik, laadkosten en operationele efficiëntie' ),
					array( 'Prijsvoorstellen voor frequente bezoekers', 'Contractuele prijsafspraken voor nog betere prijzen per kWh tegen een vaste minimale capaciteit' ),
					array( 'Geautomatiseerde back-office processen', 'Geautomatiseerde rapportages en facturatie' ),
				),
				'fleet_f'     => array( 'Wagenparkdashboard', 'Mobiele chauffeursapp', 'Kostenanalyse', 'Prijsvoorstellen', 'Rapportagetools', 'Automatische facturatie' ),
				'fleet_l'     => 'Lees meer over vervoerders',
				'w_h'         => 'Waarom ChargeNet?',
				'w_t'         => 'Wij brengen locaties met private laadinfrastructuur en EV-vloten samen op één platform.',
				'stats'       => array(
					array( '3.8', 'Mrd', 'ton CO₂: Gemiddelde vrachtwagenuitstoot van 7,0 g/mi NOx – 100 keer meer dan een benzineauto' ),
					array( '18', '%', 'van alle emissies in de EU komt van wegvervoer van goederen' ),
					array( '80', '%', 'Brandstofkosten stijgen en worden steeds onvoorspelbaarder' ),
					array( '80', '%', 'Dieseltrucks zijn goedkoop in aanschaf, maar hebben hogere onderhouds- en gebruikskosten over hun levensduur' ),
				),
				'd_h'         => 'Wat ChargeNet voor u betekent',
				'd_t'         => 'Wij koppelen een vloot EV trucks aan nabijgelegen locaties met private laadinfrastructuur op bestemming.',
				'does'        => array(
					array( 'euro', 'Verlaag laadkosten', 'Bespaar tot 60% op uw laadkosten vergeleken met roaming of publiek laden.' ),
					array( 'layers', 'Eén platform', 'Werk met een onbeperkt aantal partners, allemaal op één platform voor uw volledige supply chain.' ),
					array( 'sliders', 'Controle & flexibiliteit', 'Volledig aanpasbaar aan uw wensen op locatie- en partnerniveau, elke samenwerking is uniek.' ),
					array( 'smartphone', 'Gebruiksvriendelijk', 'Ons platform is speciaal ontwikkeld voor samenwerking binnen 3PL-wegtransport.' ),
					array( 'lock', 'Privétoegang', 'Krijg toegang tot privélaadlocaties binnen het ChargeNet-netwerk.' ),
					array( 'shield-check', 'Digitaal veilig vanaf de start', 'Wij passen de nieuwste cybersecurity-standaarden toe om risico’s voor uw operatie te minimaliseren.' ),
				),
				'platform_l'  => 'Lees meer over ons platform',
				'p_e'         => 'Projecten',
				'p_h'         => 'Laadinfrastructuur voor EV delen',
				'p_t'         => 'Ontdek hoe ons ChargeNet-platform sectoren helpt bij de versnelde adoptie van elektrische trucks en zwaar elektrisch materieel – afgestemd op hun specifieke behoeften.',
				'more'        => 'Lees Meer',
				'projects'    => array(
					array( 'project-destination', 'project-destination-charging', 'Toegankelijke EV-infrastructuur op uw bestemming', 'ChargeNet integreert met uw laadpunten, zodat logistieke partners op hun bestemming kunnen opladen met behulp van uw EV-infrastructuur.', 'ChargeNet, Millenaar van Schaik, Schiphol' ),
					array( 'project-chargebase', 'project-chargebase', 'Emissievrij bouwen met zwaar materieel', 'Mobiel, duurzaam en krachtig: DÉ oplossing voor emissievrij bouwen met zwaar materieel.', 'ChargeNet, Volta Energy, AVIA Volt, Construction, Arnhem Electricity Week' ),
					array( 'project-ijmond', 'project-ijmondaanzet', 'Toegankelijke EV-infrastructuur voor logistiek', 'ChargeNet integreert met uw laadpunten, zodat logistieke partners op hun bestemming kunnen opladen met behulp van uw EV-infrastructuur.', 'ChargeNet, Brankenhoff, JTF: Kansen voor West' ),
					array( 'project-bouwpow', 'project-bouw-pow', 'Zorg voor toegankelijke EV-infrastructuur op bouwplaatsen', 'ChargeNet maakt open elektrische infrastructuur mogelijk voor emissievrije bouw met zwaar materieel.', 'ChargeNet, Unitial, EV infrastructure, Construction' ),
				),
				'g_e'         => 'Aan de slag',
				'g_h'         => 'Hoe begint u?',
				'g_t'         => 'Ontdek hoe ons ChargeNet-platform sectoren helpt bij de versnelde adoptie van elektrische trucks en zwaar elektrisch materieel – afgestemd op hun specifieke behoeften.',
				'g_loc'       => array(
					'Locaties',
					'Verdien efficiënt aan uw bestaande laadinfrastructuur.',
					array(
						array( 'Registratie', 'Meld u aan en registreer uw locaties.' ),
						array( 'Configuratie', 'Configureer uw laadinfrastructuur op ons netwerk.' ),
						array( 'Monetisatie', 'Start met geld verdienen met uw bestaande laadinfrastructuur.' ),
					),
				),
				'g_car'       => array(
					'Vervoerders',
					'Krijg toegang tot het laadnetwerk en optimaliseer uw vlootoperatie.',
					array(
						array( 'Word lid van ons netwerk', 'Maak verbinding met het ChargeNet-platform.' ),
						array( 'Plan uw Routes', 'Optimaliseer het opladen op de bestemming van uw routes.' ),
						array( 'Begin met laden', 'Krijg toegang tot laadpunten in het hele netwerk.' ),
					),
				),
				'n_e'         => 'Nieuws',
				'n_h'         => 'Laatste Nieuws',
				'n_t'         => 'Ontdek de laatste trends, updates en nieuws uit ons netwerk: nieuwe laadlocaties, ChargeNet-mijlpalen en belangrijke ontwikkelingen op het gebied van elektrische vrachtwagens en laadtechnologie.',
				'n_l'         => 'Zie alle posts',
			),
		)[ $lang ];

		// Hero: background photo plus the driver app as cover image. The first section is never faded; parallax keeps the text and image still at load.
		$out = cn_block(
			'chargenet/hero',
			array(
				'variant'   => 'campaign',
				'animation' => 'parallax',
				'heading'   => $c['hero_h'],
				'intro'     => $c['hero_t'],
				'imageId'   => $media( 'hero-bg' ),
				'coverId'   => $media( 'hero-app' ),
				'coverAlt'  => 'en' === $lang ? 'ChargeNet Driver App' : 'ChargeNet Driver App',
			),
			cn_button( $c['hero_b1'], '#features' ) . cn_button( $c['hero_b2'], '#contact', 'secondary' )
		);

		// What ChargeNet does for you: the two audiences side by side.
		$column = static fn( array $c, string $t, string $tag, array $b, array $f, string $l, string $url ): string => cn_block(
			'chargenet/feature-column',
			array(
				'title'     => $t,
				'url'       => $url,
				'linkLabel' => '' !== $url ? $l : '',
			),
			cn_p( $tag )
			. cn_h( $c['benefits'] ) . cn_list( array_map( static fn( array $i ): string => cn_lead( $i[0], $i[1] ), $b ) )
			. cn_h( $c['features'] ) . cn_list( $f )
		);
		$out .= cn_block(
			'chargenet/feature-columns',
			array(
				'sectionBackground' => 'paper',
				'animation'         => 'fade-rise',
				'anchor'            => 'features',
				'heading'           => $c['f_h'],
				'intro'             => $c['f_t'],
			),
			$column( $c, $c['loc_t'], $c['loc_tag'], $c['loc_b'], $c['loc_f'], $c['loc_l'], $link( 'locations' ) )
			. $column( $c, $c['fleet_t'], $c['fleet_tag'], $c['fleet_b'], $c['fleet_f'], $c['fleet_l'], $link( 'carriers' ) )
		);

		// Why ChargeNet: four facts that count up, then what the platform does.
		$stats = '';
		foreach ( $c['stats'] as $s ) {
			$stats .= cn_block(
				'chargenet/stat-item',
				array(
					'value'  => $s[0],
					'suffix' => $s[1],
					'label'  => $s[2],
				)
			);
		}
		$out .= cn_block(
			'chargenet/stats',
			array(
				'animation' => 'counters',
				'anchor'    => 'whyChargeNet',
				'heading'   => $c['w_h'],
				'text'      => $c['w_t'],
			),
			$stats
		);
		$does = '';
		foreach ( $c['does'] as $d ) {
			$does .= cn_block(
				'chargenet/feature-grid-item',
				array(
					'icon'  => $d[0],
					'title' => $d[1],
					'text'  => $d[2],
				)
			);
		}
		$out .= cn_block(
			'chargenet/feature-grid',
			array(
				'animation'   => 'stagger',
				'spaceBottom' => '' !== $link( 'locations' ) ? 'sm' : 'lg',
				'heading'     => $c['d_h'],
				'intro'       => $c['d_t'],
			),
			$does
		);
		if ( '' !== $link( 'locations' ) ) {
			$out .= cn_block( 'chargenet/rich-text', array( 'spaceTop' => 'none' ), cn_button( $c['platform_l'], $link( 'locations' ), 'secondary' ) );
		}

		// Projects: cards link to the project pages once those pages exist.
		$cards = '';
		foreach ( $c['projects'] as $p ) {
			$url    = $link( $p[1] );
			$cards .= cn_block(
				'chargenet/slide-card',
				array(
					'imageId'   => $media( $p[0] ),
					'title'     => $p[2],
					'text'      => $p[3],
					'tags'      => $p[4],
					'url'       => $url,
					'linkLabel' => '' !== $url ? $c['more'] : '',
				)
			);
		}
		$out .= cn_block(
			'chargenet/card-slider',
			array(
				'sectionBackground' => 'paper',
				'anchor'            => 'projects',
				'eyebrow'           => $c['p_e'],
				'heading'           => $c['p_h'],
				'intro'             => $c['p_t'],
			),
			$cards
		);

		// How to get started: one column per audience, steps as a numbered list, then the app badges.
		$start = static function ( array $g, string $url, string $label ): string {
			$steps = array_map( static fn( array $s ): string => '<strong>' . $s[0] . '</strong><br>' . $s[1], $g[2] );
			return cn_block(
				'chargenet/feature-column',
				array(
					'title'     => $g[0],
					'url'       => $url,
					'linkLabel' => '' !== $url ? $label : '',
				),
				cn_p( $g[1] ) . cn_list( $steps, true )
			);
		};
		$out .= cn_block(
			'chargenet/feature-columns',
			array(
				'animation'   => 'fade-rise',
				'anchor'      => 'getStarted',
				'spaceBottom' => 'sm',
				'eyebrow'     => $c['g_e'],
				'heading'     => $c['g_h'],
				'intro'       => $c['g_t'],
			),
			$start( $c['g_loc'], $link( 'locations' ), $c['loc_l'] ) . $start( $c['g_car'], $link( 'carriers' ), $c['fleet_l'] )
		);
		$out .= cn_app_badges( $lang, $media );

		// Latest news: real posts of this language (empty until the blog is imported).
		$out .= cn_block(
			'chargenet/post-grid',
			array(
				'animation' => 'stagger',
				'anchor'    => 'blog',
				'eyebrow'   => $c['n_e'],
				'heading'   => $c['n_h'],
				'intro'     => $c['n_t'],
				'linkLabel' => $c['n_l'],
			)
		);

		return $out . cn_contact_team( $lang, $media );
	},
);
