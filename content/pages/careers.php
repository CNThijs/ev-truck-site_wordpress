<?php
/**
 * Careers. English with the typographic apostrophe fixed; Dutch is the live copy.
 */
return array(
	'key'    => 'careers',
	'slugs'  => array(
		'en' => 'careers',
		'nl' => 'carriere',
	),
	'titles' => array(
		'en' => 'Careers',
		'nl' => 'Carrière',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'        => 'Join our Team',
				'intro'     => 'We\'re looking for passionate innovators to help us decarbonise road logistics and construction projects.',
				'why_h'     => 'Why Join ChargeNet?',
				'why_t'     => 'We welcome both full-time professionals and interns who would like to contribute to accelerating the adoption of electric trucks and electric heavy machinery.',
				'reasons'   => array(
					array( 'lightbulb', 'Innovation', 'Work on cutting-edge technology that\'s changing multiple industries and accelerates the adoption of electric trucks and electric heavy machinery.' ),
					array( 'target', 'Impact', 'Create solutions that enhance safety, performance, and sustainability.' ),
					array( 'trending-up', 'Growth', 'Develop your skills in a rapidly expanding field with diverse challenges.' ),
				),
				'contact_h' => 'Contact Our CEO',
			),
			'nl' => array(
				'h1'        => 'Sluit u aan bij ons team',
				'intro'     => 'Wij zijn op zoek naar gepassioneerde innovators die met ons samen de decarbonisatie van de logistiek en de bouwsector willen versnellen.',
				'why_h'     => 'Waarom aansluiten bij ChargeNet?',
				'why_t'     => 'Wij nodigen zowel fulltime professionals als stagiairs uit die graag een bijdrage willen leveren aan het versnellen van de adoptie van elektrische vrachtwagens en elektrisch zwaar materieel.',
				'reasons'   => array(
					array( 'lightbulb', 'Innovatie', 'Werk aan geavanceerde technologie die meerdere sectoren verandert en de adoptie van elektrische vrachtwagens en elektrisch zwaar materieel versnelt.' ),
					array( 'target', 'Impact', 'Creëer oplossingen die de veiligheid, prestaties en duurzaamheid verbeteren.' ),
					array( 'trending-up', 'Groei', 'Ontwikkel uw vaardigheden in een snelgroeiend vakgebied met uiteenlopende uitdagingen.' ),
				),
				'contact_h' => 'Neem contact op met onze CEO',
			),
		)[ $lang ];

		$items = '';
		foreach ( $c['reasons'] as $r ) {
			$items .= cn_block(
				'chargenet/feature-grid-item',
				array(
					'icon'  => $r[0],
					'title' => $r[1],
					'text'  => $r[2],
				)
			);
		}

		return cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $c['h1'],
				'intro'   => $c['intro'],
			)
		)
		. cn_block(
			'chargenet/feature-grid',
			array(
				'animation' => 'stagger',
				'columns'   => 3,
				'heading'   => $c['why_h'],
				'intro'     => $c['why_t'],
			),
			$items
		)
		. cn_block(
			'chargenet/team',
			array(
				'variant'           => 'compact',
				'sectionBackground' => 'paper',
				'animation'         => 'fade-rise',
				'heading'           => $c['contact_h'],
			),
			cn_block(
				'chargenet/team-member',
				array(
					'name'     => 'Sebastiaan de Vries',
					'role'     => 'CEO & CoFounder',
					'email'    => 'info@chargenet.energy',
					'linkedin' => 'https://www.linkedin.com/in/sebastiaan-de-vries-chargenet/',
					'imageId'  => $media( 'person-seb' ),
				)
			)
		);
	},
);
