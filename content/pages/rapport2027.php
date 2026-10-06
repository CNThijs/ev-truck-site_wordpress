<?php
/**
 * Campaign landing page for the trend report. The live page is Dutch only; the English version is a translation
 * (listed in docs/translation-review.md). The request form is the Trend Report Form section (code and email, or name,
 * company and email): submissions are stored in WordPress and the report link is emailed (docs/integrations.md).
 */
return array(
	'key'    => 'rapport2027',
	'slugs'  => array(
		'en' => 'trend-report-2027',
		'nl' => 'rapport2027',
	),
	'titles' => array(
		'en' => 'Trend report 2027',
		'nl' => 'Trendrapport 2027',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'nl' => array(
				'h1'       => 'De cijfers achter de e-transitie in het Nederlandse wegtransport',
				'intro'    => 'Vrachtwagenheffing, ETS 2, ZE-zones, netcongestie: hoe de rekensom van uw kilometerkostprijs de komende 24 maanden kantelt. Opgebouwd uit ING, ElaadNL, Milence en RVO. Zonder wensdenken.',
				'b1'       => 'Vraag het rapport aan',
				'b2'       => 'Bekijk de belangrijkste inzichten',
				'eyebrow'  => 'Wat u krijgt',
				'heading'  => 'Wat levert het Trendrapport 2027 u op?',
				'lead'     => 'In de komende 24 maanden verandert de kostenstructuur van het Nederlandse wegtransport fundamenteel. Wie op cijfers rijdt, moet nu opnieuw rekenen.',
				'items'    => array(
					array( 'euro', 'Vrachtwagenheffing tot €0,195/km, vanaf medio 2026', 'De heffing raakt direct uw kilometerkostprijs. Het rapport rekent voor wat dit per voertuig en per jaar betekent voor uw wagenpark.' ),
					array( 'battery-charging', 'Accijnskorting diesel vervalt eind 2026', 'Zonder de huidige korting stijgen uw brandstofkosten fors. Wij laten zien hoe dit de businesscase voor elektrificatie verschuift.' ),
					array( 'leaf', 'ETS 2 vanaf 2027: CO2-prijs aan de pomp', 'Europese CO2-beprijzing maakt fossiele kilometers duurder. Het rapport duidt wat dit betekent voor uw kostprijs per rit.' ),
					array( 'trending-up', 'AanZET-subsidie verdriedubbelt voor e-trucks', 'Meer subsidiebudget verlaagt de instapdrempel voor e-trucks aanzienlijk. Wij zetten op een rij hoe u hier optimaal gebruik van maakt.' ),
					array( 'map-pin', 'Bijna 30 steden met ZE-zones tot 2030', 'Steeds meer binnensteden sluiten fossiele vrachtwagens uit. Het rapport toont welke zones eraan komen en wat dit vraagt van uw planning.' ),
				),
				'dl_e'     => 'Gratis rapport',
				'dl_h'     => 'Ontvang het Trendrapport 2027',
				'dl_t'     => 'Vul uw gegevens in, dan sturen wij het Trendrapport 2027 direct naar uw mailbox. Eén rapport, geen nieuwsbrief-abonnement, geen vervolgmailings zonder uw instemming. Wij gebruiken uw gegevens alleen voor de toezending van dit rapport en één opvolgend contactmoment.',
							),
			'en' => array(
				'h1'       => 'The numbers behind the e-transition in Dutch road transport',
				'intro'    => 'Truck toll, ETS 2, zero-emission zones, grid congestion: how the calculation of your cost per kilometre tips over the next 24 months. Built from ING, ElaadNL, Milence and RVO. No wishful thinking.',
				'b1'       => 'Request the report',
				'b2'       => 'See the key insights',
				'eyebrow'  => 'What you get',
				'heading'  => 'What does the Trend Report 2027 offer you?',
				'lead'     => 'Over the next 24 months the cost structure of Dutch road transport changes fundamentally. Anyone who drives by the numbers needs to recalculate now.',
				'items'    => array(
					array( 'euro', 'Truck toll up to €0.195/km, from mid-2026', 'The toll directly affects your cost per kilometre. The report calculates what this means per vehicle and per year for your fleet.' ),
					array( 'battery-charging', 'Diesel excise discount ends in late 2026', 'Without the current discount your fuel costs rise sharply. We show how this shifts the business case for electrification.' ),
					array( 'leaf', 'ETS 2 from 2027: CO2 price at the pump', 'European CO2 pricing makes fossil kilometres more expensive. The report explains what this means for your cost per trip.' ),
					array( 'trending-up', 'AanZET subsidy triples for e-trucks', 'A larger subsidy budget lowers the barrier to entry for e-trucks considerably. We lay out how to make the best use of it.' ),
					array( 'map-pin', 'Almost 30 cities with zero-emission zones until 2030', 'More and more city centres exclude fossil trucks. The report shows which zones are coming and what this requires of your planning.' ),
				),
				'dl_e'     => 'Free report',
				'dl_h'     => 'Get the Trend Report 2027',
				'dl_t'     => 'Fill in your details and we will send the Trend Report 2027 straight to your inbox. One report, no newsletter subscription, no follow-up mailings without your consent. We only use your details to send this report and for one follow-up contact.',
							),
		)[ $lang ];

		$out = cn_block(
			'chargenet/hero',
			array(
				'variant'   => 'campaign',
				'animation' => 'parallax',
				'heading'   => $c['h1'],
				'intro'     => $c['intro'],
				'imageId'   => $media( 'hero-bg' ),
				'coverId'   => $media( 'report-cover' ),
				'coverAlt'  => 'en' === $lang ? 'Trend report 2027 cover' : 'Trendrapport 2027',
			),
			cn_button( $c['b1'], '#download' ) . cn_button( $c['b2'], '#benefits', 'secondary' )
		);

		$items = '';
		foreach ( $c['items'] as $item ) {
			$items .= cn_block(
				'chargenet/feature-grid-item',
				array(
					'icon'  => $item[0],
					'title' => $item[1],
					'text'  => $item[2],
				)
			);
		}
		$out .= cn_block(
			'chargenet/feature-grid',
			array(
				'anchor'    => 'benefits',
				'animation' => 'stagger',
				'columns'   => 3,
				'eyebrow'   => $c['eyebrow'],
				'heading'   => $c['heading'],
				'intro'     => $c['lead'],
			),
			$items
		);

		$out .= cn_block(
			'chargenet/trend-report-form',
			array(
				'sectionBackground' => 'paper',
				'animation'         => 'fade-rise',
				'anchor'            => 'download',
				'eyebrow'           => $c['dl_e'],
				'heading'           => $c['dl_h'],
				'intro'             => $c['dl_t'],
			)
		);

		return $out;
	},
);
