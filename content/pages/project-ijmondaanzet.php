<?php
/**
 * Project: IJmond aan ZET. Text from the project card on the old site (see _project.php).
 */
require_once __DIR__ . '/_project.php';

return array(
	'key'    => 'project-ijmondaanzet',
	'slugs'  => array(
		'en' => 'ijmond-aan-zet-project',
		'nl' => 'ijmond-aan-zet',
	),
	'titles' => array(
		'en' => 'IJmond aan ZET',
		'nl' => 'IJmond aan ZET',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'label'  => 'IJmond aan ZET',
				'title'  => 'Accessible EV infrastructure for logistics',
				'text'   => 'ChargeNet integrates with your charge points to enable logistic partners to charge on their destination using your EV infrastructure.',
				'tags'   => array( 'ChargeNet', 'Brankenhoff', 'JTF: Kansen voor West' ),
				'tags_h' => 'Partners and topics',
				'cta_h'  => 'Interested in this project?',
				'cta_b'  => 'Contact us',
			),
			'nl' => array(
				'label'  => 'IJmond aan ZET',
				'title'  => 'Toegankelijke EV-infrastructuur voor logistiek',
				'text'   => 'ChargeNet integreert met uw laadpunten, zodat logistieke partners op hun bestemming kunnen opladen met behulp van uw EV-infrastructuur.',
				'tags'   => array( 'ChargeNet', 'Brankenhoff', 'JTF: Kansen voor West' ),
				'tags_h' => 'Partners en onderwerpen',
				'cta_h'  => 'Interesse in dit project?',
				'cta_b'  => 'Neem contact op',
			),
		)[ $lang ];

		return cn_project_page( $lang, $c, 'project-ijmond', $media );
	},
);
