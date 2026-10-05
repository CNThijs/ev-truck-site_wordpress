<?php
/**
 * About. English with typos fixed; Dutch is the live copy. The team bios are English on the live Dutch page, so they
 * were translated (listed in docs/translation-review.md).
 */
return array(
	'key'    => 'about',
	'slugs'  => array(
		'en' => 'about',
		'nl' => 'over-ons',
	),
	'titles' => array(
		'en' => 'About Us',
		'nl' => 'Over Ons',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'        => 'About ChargeNet',
				'intro'     => 'We are a team focused on increasing EV truck adoption and electric heavy machinery.',
				'story_h'   => 'Our Story',
				'story'     => array(
					'It all began in 2021, when Sebastiaan, working at a sustainability-focused ice cream factory, found himself stuck in traffic, wondering: Where are all the electric trucks? Despite major strides in sustainable sourcing and production, the factory\'s supply chain still ran on diesel. His attempt to electrify logistics hit a wall; charging infrastructure was expensive and installing it on-site seemed out of reach.',
					'That moment sparked a bigger idea. We started to build a team that shared the observation of the slow pace of electrification in logistics and wanting to make a true impact. Together, we envisioned a smarter, faster way to decarbonize road transport: by unlocking underused, privately-owned locations for EV truck charging. With this idea in mind, we set out to build a platform that connects logistic companies to a growing network of private charge locations; enabling destination charging that is affordable, reliable, and accessible.',
					'Today, we are building the backbone for the future of zero-emission logistics; where EV trucks and heavy machinery charge at the right price, in the right place, at the right time. Keep charging ahead!',
				),
				'mission_h' => 'Our Mission',
				'mission'   => array(
					'At ChargeNet, we are on a mission to accelerate the transition to zero-emission logistics by making destination charging for electric trucks and heavy machinery: simple, accessible, and scalable.',
					'By empowering private location owners to open up their charging infrastructure to logistic fleets, we are removing one of the biggest barriers to electrification; access to affordable and convenient charging where it\'s needed most, while circumventing the current netcongestion issues.',
				),
				'values_h'  => 'Our Values',
				'values'    => array(
					array( 'target', 'Practical Impact', 'We build solutions that work in the real world; fast, scalable, and easy to adopt.' ),
					array( 'users', 'Collaboration', 'We believe the only way to decarbonize road logistics is together—with location owners, fleet operators, and industry partners.' ),
					array( 'leaf', 'Sustainability', 'Every decision we make is rooted in our commitment to a cleaner, greener future for transport.' ),
					array( 'shield-check', 'Integrity', 'We are transparent, responsible, and mission-driven. Trust is the foundation of every partnership we form.' ),
					array( 'lightbulb', 'Innovation', 'We challenge the status quo to create smart infrastructure that works harder for people and planet.' ),
				),
				'team_h'    => 'Our Team',
				'team_t'    => 'Our team combines expertise in supply chain, logistics, EV electronics, software development, cybersecurity, artificial intelligence, and industry-specific knowledge to accelerate the adoption of EV trucks and electric heavy machinery.',
				'bios'      => array(
					"Sebastiaan worked his entire career with and for the ChargeNet target audience in various roles at Unilever.\nHis wide network and knowledge of B2B logistics expedite our road to success.\nSector experience: Supply Chain, Innovation, Consultancy\nSDA Bocconi MBA 2023",
					"Thijs started his career developing IT platforms & solutions for 20 years as independent contractor.\nHe further shaped his knowledge and experience on IT & Cybersecurity as Rabobank YPP IT trainee and as part of the HEINEKEN Global Security team.\nSector experience: Banking, FMCG & Blockchain\nISC2 CISSP-ISSMP certified cybersecurity expert",
					"Piotr leads ChargeNet’s lean & flexible platform development, including overseeing third-party product development.\nCTO/CPTO & Strategic Tech Leader\nDriving Business Growth through Technology & Innovation\nFormer EVBox",
				),
				'board_h'   => 'Our Board of Advisors',
				'board'     => 'Our Board of Advisors brings together seasoned experts from the fields of logistics, energy, mobility, and technology. With decades of combined experience, they provide strategic guidance, industry insights, and a strong network to help us scale our impact and navigate the complex transition to zero-emission road transport.',
				'join_h'    => 'Join our Team',
				'join_b'    => 'Join our Team',
			),
			'nl' => array(
				'h1'        => 'Over ChargeNet',
				'intro'     => 'Wij zijn een team dat zich richt op het verhogen van de adoptie van EV trucks en elektrisch zwaar materieel.',
				'story_h'   => 'Ons Verhaal',
				'story'     => array(
					'Het begon allemaal in 2021, toen Sebastiaan werkte bij een duurzaamheidsgerichte ijsfabriek, in de file stond en zich afvroeg: Waar zijn alle elektrische vrachtwagens? Ondanks grote nadruk op duurzaamheid bij inkoop en productie, draaide de toeleveringsketen van de fabriek nog steeds op diesel trucks. Zijn poging om de logistieke keten van de fabriek te elektrificeren stuitte op een muur; laadinfrastructuur was duur, te complex in de uitvoering en het installeren op locatie leek buiten bereik.',
					'Dat moment leidde tot een groter idee. We begonnen een team op te bouwen dat de observatie deelde van het trage tempo van elektrificatie in de logistiek en de wens om een echte impact te maken. Samen stelden we ons een slimmere, snellere manier voor om wegtransport te decarboniseren: door onderbenutte, privaat eigendom locaties te ontsluiten voor EV-vrachtwagenopladen. Met dit idee in gedachten gingen we aan de slag om een platform te bouwen dat logistieke bedrijven verbindt met een groeiend netwerk van privé-laadlocaties; waardoor bestemmingsladen mogelijk wordt dat betaalbaar, betrouwbaar en toegankelijk is.',
					'Vandaag bouwen we de ruggengraat voor de toekomst van emissievrije logistiek; waar EV-vrachtwagens en zware machines laden voor de juiste prijs, op de juiste plaats, op het juiste moment. Keep charging ahead!',
				),
				'mission_h' => 'Onze Missie',
				'mission'   => array(
					'Bij ChargeNet zijn we op een missie om de overgang naar emissievrije logistiek te versnellen door bestemmingsladen voor elektrische vrachtwagens en zware machines eenvoudig, toegankelijk en schaalbaar te maken.',
					'Door privé-locatie-eigenaren in staat te stellen hun laadinfrastructuur open te stellen voor logistieke vloten, verwijderen we een van de grootste barrières voor elektrificatie; toegang tot betaalbaar en handig laden waar het het meest nodig is, terwijl we de huidige netcongestie problemen omzeilen.',
				),
				'values_h'  => 'Onze Waarden',
				'values'    => array(
					array( 'target', 'Praktische Impact', 'We bouwen oplossingen die werken in de echte wereld; snel, schaalbaar en eenvoudig te adopteren.' ),
					array( 'users', 'Samenwerking', 'We geloven dat de enige manier om weglogistiek te decarboniseren samen is—met locatie-eigenaren, vlootoperators en industriële partners.' ),
					array( 'leaf', 'Duurzaamheid', 'Elke beslissing die we nemen is geworteld in onze toewijding aan een schonere, groenere toekomst voor transport.' ),
					array( 'shield-check', 'Integriteit', 'We zijn transparant, verantwoordelijk en missiegedreven. Vertrouwen is de basis van elk partnerschap dat we vormen.' ),
					array( 'lightbulb', 'Innovatie', 'We dagen de status quo uit om slimme infrastructuur te creëren die harder werkt voor mensen en planeet.' ),
				),
				'team_h'    => 'Ons Team',
				'team_t'    => 'Ons team combineert expertise op het gebied van supply chain, logistiek, elektrische voertuigen, software ontwikkeling, cybersecurity, kunstmatige intelligentie en branchespecifieke kennis om de acceptatie van elektrische vrachtwagens en elektrisch zwaar materieel te versnellen.',
				'bios'      => array(
					"Sebastiaan werkte zijn hele loopbaan met en voor de doelgroep van ChargeNet, in verschillende functies bij Unilever.\nZijn brede netwerk en kennis van B2B-logistiek versnellen onze weg naar succes.\nSectorervaring: Supply Chain, Innovatie, Consultancy\nSDA Bocconi MBA 2023",
					"Thijs begon zijn loopbaan met het ontwikkelen van IT-platforms en -oplossingen, 20 jaar lang als zelfstandige.\nHij verdiepte zijn kennis en ervaring op het gebied van IT en cybersecurity als Rabobank YPP IT-trainee en als onderdeel van het HEINEKEN Global Security-team.\nSectorervaring: Bankieren, FMCG & Blockchain\nISC2 CISSP-ISSMP gecertificeerd cybersecurity-expert",
					"Piotr leidt de lean en flexibele platformontwikkeling van ChargeNet, inclusief het aansturen van productontwikkeling door derden.\nCTO/CPTO & strategisch technologieleider\nZorgt voor bedrijfsgroei door technologie en innovatie\nVoorheen EVBox",
				),
				'board_h'   => 'Onze Raad van Advies',
				'board'     => 'Onze Raad van Adviseurs bestaat uit ervaren experts uit de logistiek, energie, mobiliteit en technologie. Met tientallen jaren gezamenlijke ervaring bieden zij strategische begeleiding, branche-inzichten en een sterk netwerk om ons te helpen onze impact te vergroten en de complexe transitie naar emissievrij wegtransport te begeleiden.',
				'join_h'    => 'Sluit u aan bij ons team',
				'join_b'    => 'Sluit u aan bij ons team',
			),
		)[ $lang ];

		$out = cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $c['h1'],
				'intro'   => $c['intro'],
			)
		);

		$out .= cn_block( 'chargenet/rich-text', array( 'animation' => 'fade-rise', 'heading' => $c['story_h'] ), implode( '', array_map( 'cn_p', $c['story'] ) ) );
		$out .= cn_block( 'chargenet/rich-text', array( 'sectionBackground' => 'paper', 'animation' => 'fade-rise', 'heading' => $c['mission_h'] ), implode( '', array_map( 'cn_p', $c['mission'] ) ) );

		$values = '';
		foreach ( $c['values'] as $v ) {
			$values .= cn_block(
				'chargenet/feature-grid-item',
				array(
					'icon'  => $v[0],
					'title' => $v[1],
					'text'  => $v[2],
				)
			);
		}
		$out .= cn_block(
			'chargenet/feature-grid',
			array(
				'animation' => 'stagger',
				'columns'   => 3,
				'heading'   => $c['values_h'],
			),
			$values
		);

		$people = array(
			array( 'Sebastiaan de Vries', 'CEO & CoFounder', 'person-seb', 'https://www.linkedin.com/in/sebastiaan-de-vries-chargenet/' ),
			array( 'Thijs Verwaal', 'CISO & CoFounder', 'person-thijs', 'https://www.linkedin.com/in/thijsverwaal/' ),
			array( 'Piotr Krzepczak', 'CTO & CoFounder', 'person-piotr', 'https://www.linkedin.com/in/krzepczak/' ),
		);
		$cards  = '';
		foreach ( $people as $i => $p ) {
			$cards .= cn_block(
				'chargenet/team-member',
				array(
					'name'     => $p[0],
					'role'     => $p[1],
					'bio'      => $c['bios'][ $i ],
					'linkedin' => $p[3],
					'imageId'  => $media( $p[2] ),
				)
			);
		}
		$out .= cn_block(
			'chargenet/team',
			array(
				'sectionBackground' => 'paper',
				'animation'         => 'stagger',
				'heading'           => $c['team_h'],
				'intro'             => $c['team_t'],
			),
			$cards
		);

		$out .= cn_block( 'chargenet/rich-text', array( 'animation' => 'fade-rise', 'heading' => $c['board_h'] ), cn_p( $c['board'] ) );
		$out .= cn_block(
			'chargenet/cta-band',
			array(
				'animation' => 'fade-rise',
				'heading'   => $c['join_h'],
			),
			cn_button( $c['join_b'], $link( 'careers' ) )
		);

		return $out . cn_contact_team( $lang, $media );
	},
);
