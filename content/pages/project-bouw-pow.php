<?php
/**
 * Project: Bouw-Pow. Text from the project card on the old site (see _project.php).
 */
require_once __DIR__ . '/_project.php';

return array(
	'key'    => 'project-bouw-pow',
	'slugs'  => array(
		'en' => 'bouw-pow-project',
		'nl' => 'bouw-pow',
	),
	'titles' => array(
		'en' => 'Bouw-Pow',
		'nl' => 'Bouw-Pow',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'label'  => 'Bouw-Pow',
				'title'  => 'Enable accessible EV infrastructure on construction sites',
				'text'   => 'ChargeNet empowers open electric infrastructure for emission-free construction with heavy machinery.',
				'tags'   => array( 'ChargeNet', 'Unitial', 'EV infrastructure', 'Construction' ),
				'tags_h' => 'Partners and topics',
				'cta_h'  => 'Interested in this project?',
				'cta_b'  => 'Contact us',
			),
			'nl' => array(
				'label'  => 'Bouw-Pow',
				'title'  => 'Zorg voor toegankelijke EV-infrastructuur op bouwplaatsen',
				'text'   => 'ChargeNet maakt open elektrische infrastructuur mogelijk voor emissievrije bouw met zwaar materieel.',
				'tags'   => array( 'ChargeNet', 'Unitial', 'EV infrastructure', 'Construction' ),
				'tags_h' => 'Partners en onderwerpen',
				'cta_h'  => 'Interesse in dit project?',
				'cta_b'  => 'Neem contact op',
			),
		)[ $lang ];

		return cn_project_page( $lang, $c, 'project-bouwpow', $media );
	},
);
