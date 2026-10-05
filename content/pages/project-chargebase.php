<?php
/**
 * Project: ChargeBase. Text from the project card on the old site (see _project.php).
 */
require_once __DIR__ . '/_project.php';

return array(
	'key'    => 'project-chargebase',
	'slugs'  => array(
		'en' => 'chargebase-project',
		'nl' => 'chargebase',
	),
	'titles' => array(
		'en' => 'ChargeBase',
		'nl' => 'ChargeBase',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'label'  => 'ChargeBase',
				'title'  => 'Emission-free construction with heavy machinery',
				'text'   => 'Mobile, sustainable and powerful: THE solution for emission-free construction with heavy machinery.',
				'tags'   => array( 'ChargeNet', 'Volta Energy', 'AVIA Volt', 'Construction', 'Arnhem Electricity Week' ),
				'tags_h' => 'Partners and topics',
				'cta_h'  => 'Interested in this project?',
				'cta_b'  => 'Contact us',
			),
			'nl' => array(
				'label'  => 'ChargeBase',
				'title'  => 'Emissievrij bouwen met zwaar materieel',
				'text'   => 'Mobiel, duurzaam en krachtig: DÉ oplossing voor emissievrij bouwen met zwaar materieel.',
				'tags'   => array( 'ChargeNet', 'Volta Energy', 'AVIA Volt', 'Construction', 'Arnhem Electricity Week' ),
				'tags_h' => 'Partners en onderwerpen',
				'cta_h'  => 'Interesse in dit project?',
				'cta_b'  => 'Neem contact op',
			),
		)[ $lang ];

		return cn_project_page( $lang, $c, 'project-chargebase', $media );
	},
);
