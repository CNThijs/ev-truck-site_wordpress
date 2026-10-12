<?php
/**
 * Shared layout of the two audience pages (Carriers, Locations): title band, pitch with the network image, benefit
 * cards, five steps with key activities, claims, closing call to action and the contact cards.
 *
 * @param string   $lang  Language.
 * @param array    $c     Copy of this language (see carriers.php for the keys).
 * @param callable $media Media resolver.
 */
function cn_audience_page( string $lang, array $c, callable $media ): string {
	$out = cn_block(
		'chargenet/hero',
		array(
			'variant' => 'title-band',
			'heading' => $c['h1'],
			'intro'   => $c['intro'],
		)
	);

	$out .= cn_block(
		'chargenet/rich-text-image',
		array(
			'sectionBackground' => 'paper',
			'animation'         => 'fade-rise',
			'heading'           => $c['pitch_h'],
			'imageId'           => $media( 'network-map' ),
		),
		implode( '', array_map( 'cn_p', $c['pitch'] ) )
	);

	// Benefits: six cards that tile the grid, the large one on a photo.
	$tones    = array( 'photo', 'light', 'accent', 'dark', 'light', 'light' );
	$benefits = '';
	foreach ( $c['benefits'] as $i => $b ) {
		$benefits .= cn_block(
			'chargenet/bento-item',
			array(
				'title'   => $b[1],
				'tone'    => $tones[ $i ] ?? 'light',
				'icon'    => $b[0],
				'imageId' => 0 === $i ? $media( 'ccs2' ) : 0,
			),
			cn_p( $b[2] )
		);
	}
	$out .= cn_block(
		'chargenet/bento',
		array(
			'animation' => 'stagger',
			'heading'   => $c['benefits_h'],
		),
		$benefits
	);

	$steps = '';
	foreach ( $c['steps'] as $s ) {
		$steps .= cn_block(
			'chargenet/step-item',
			array( 'title' => $s[0] ),
			cn_p( $s[1] ) . cn_p( '<strong>' . $c['activities'] . '</strong>' ) . cn_list( $s[2] )
		);
	}
	$out .= cn_block(
		'chargenet/steps',
		array(
			'sectionBackground' => 'paper',
			'variant'           => 'vertical',
			'animation'         => 'draw',
			'heading'           => $c['steps_h'],
		),
		$steps
	);

	$claims = '';
	foreach ( $c['claims'] as $cl ) {
		$claims .= cn_block(
			'chargenet/feature-grid-item',
			array(
				'icon'  => $cl[0],
				'title' => $cl[1],
				'text'  => $cl[2],
			)
		);
	}
	$out .= cn_block(
		'chargenet/feature-grid',
		array(
			'variant'           => 'split',
			'sectionBackground' => 'paper',
			'animation'         => 'stagger',
			'heading'           => $c['cta_h'],
			'intro'       => $c['cta_t'],
		),
		$claims
	);
	$out .= cn_block(
		'chargenet/cta-band',
		array(
			'animation' => 'fade-rise',
			'heading'   => $c['join_h'],
		),
		cn_button( $c['join_b'], '#contact' )
	);

	return $out . cn_contact_team( $lang, $media );
}
