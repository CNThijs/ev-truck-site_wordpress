<?php
/**
 * Project: Destination Charging. Text from the project card on the old site (see _project.php).
 */
require_once __DIR__ . '/_project.php';

return array(
	'key'    => 'project-destination-charging',
	'slugs'  => array(
		'en' => 'destination-charging',
		'nl' => 'laden-op-bestemming',
	),
	'titles' => array(
		'en' => 'Destination Charging',
		'nl' => 'Laden op Bestemming',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'label'  => 'Destination Charging',
				'title'  => 'Accessible EV infrastructure on your destination',
				'text'   => 'ChargeNet integrates with your charge points to enable logistic partners to charge on their destination using your EV infrastructure.',
				'tags'   => array( 'ChargeNet', 'Millenaar van Schaik', 'Schiphol' ),
				'tags_h' => 'Partners and topics',
				'cta_h'  => 'Interested in this project?',
				'cta_b'  => 'Contact us',
			),
			'nl' => array(
				'label'  => 'Laden op Bestemming',
				'title'  => 'Toegankelijke EV-infrastructuur op uw bestemming',
				'text'   => 'ChargeNet integreert met uw laadpunten, zodat logistieke partners op hun bestemming kunnen opladen met behulp van uw EV-infrastructuur.',
				'tags'   => array( 'ChargeNet', 'Millenaar van Schaik', 'Schiphol' ),
				'tags_h' => 'Partners en onderwerpen',
				'cta_h'  => 'Interesse in dit project?',
				'cta_b'  => 'Neem contact op',
			),
		)[ $lang ];

		return cn_project_page( $lang, $c, 'project-destination', $media );
	},
);
