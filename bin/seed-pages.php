<?php
/**
 * Seeds the Home page in English and Dutch (linked translations) and makes it the static front page,
 * so /en/ and /nl/ both exist. Content is the starter landing pattern; the real pages come in later epics. WP-CLI gives the Dutch slug 'home-2'; it never shows in a URL (the front page is /nl/).
 * Run by bin/setup-wp.sh (wp eval-file). Idempotent: an existing Home page is kept, never overwritten.
 */

$homes = array(
	'en' => array( 'Home', 'home', 'chargenet/page-landing' ),
	'nl' => array( 'Home', 'home', 'chargenet/page-landing-nl' ),
);

$ids = array();
foreach ( $homes as $lang => $home ) {
	// get_posts() ignores the language filter here, so pick the page of this language by hand.
	$existing = array_filter(
		get_posts(
			array(
				'post_type'   => 'page',
				'name'        => $home[1],
				'numberposts' => -1,
				'post_status' => 'any',
				'fields'      => 'ids',
			)
		),
		static fn( $page_id ) => pll_get_post_language( $page_id ) === $lang
	);
	if ( $existing ) {
		$ids[ $lang ] = (int) reset( $existing );
		continue;
	}
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( $home[2] );
	$id      = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $home[0],
			'post_name'    => $home[1],
			'post_content' => $pattern['content'] ?? '',
		)
	);
	pll_set_post_language( $id, $lang );
	$ids[ $lang ] = (int) $id;
}

pll_save_post_translations( $ids );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $ids['en'] );
echo "Home pages seeded (en, nl).\n";
