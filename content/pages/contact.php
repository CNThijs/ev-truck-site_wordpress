<?php
/**
 * Contact: the contact form and the founders' contact cards. New page (the old site had no contact page; its
 * contact form component was never shown), so its Dutch texts are listed in docs/translation-review.md.
 */
return array(
	'key'    => 'contact',
	'slugs'  => array(
		'en' => 'contact',
		'nl' => 'contact-opnemen',
	),
	'titles' => array(
		'en' => 'Contact',
		'nl' => 'Contact',
	),
	'build'  => static function ( string $lang, callable $media, callable $link ): string {
		$c = array(
			'en' => array(
				'h1'    => 'Contact us',
				'intro' => 'Have a question about ChargeNet? Send us a message and we will get back to you. You receive a copy by email.',
			),
			'nl' => array(
				'h1'    => 'Neem contact met ons op',
				'intro' => 'Heeft u een vraag over ChargeNet? Stuur ons een bericht, dan nemen wij contact met u op. U ontvangt een kopie per e-mail.',
			),
		)[ $lang ];

		return cn_block(
			'chargenet/hero',
			array(
				'variant' => 'title-band',
				'heading' => $c['h1'],
				'intro'   => $c['intro'],
			)
		)
		. cn_block( 'chargenet/contact-form', array( 'animation' => 'fade-rise' ) )
		. cn_contact_team( $lang, $media );
	},
);
