<?php
/**
 * Polylang configuration, the single source of truth for languages and settings.
 * Run: `wp eval-file bin/setup-polylang.php` (bin/setup-wp.sh does it locally; see docs/install.md for a fresh install).
 * Idempotent: creates missing languages, updates existing ones, sets the options below. Never touches content.
 *
 * URL structure: /en/ and /nl/ (language code as directory, both prefixed). The bare root is
 * redirected to the default language by Polylang. No browser-language redirect (SEO, crawlers).
 */

if ( ! function_exists( 'PLL' ) ) {
	WP_CLI::error( 'Polylang is not active.' );
}

$languages = array(
	array(
		'name'       => 'English',
		'slug'       => 'en',
		'locale'     => 'en_US',
		'rtl'        => 0,
		'term_group' => 0,
		'flag'       => 'gb',
	),
	array(
		'name'       => 'Nederlands',
		'slug'       => 'nl',
		'locale'     => 'nl_NL',
		'rtl'        => 0,
		'term_group' => 1,
		'flag'       => 'nl',
	),
);

$default = 'en';

$options = array(
	'force_lang'    => 1, // Language set from the directory name in pretty permalinks.
	'rewrite'       => 1, // Remove /language/ from the URL.
	'hide_default'  => 0, // Prefix the default language too: /en/.
	'redirect_lang' => 1, // Front page lives at /en/ and /nl/, not /en/home/.
	'browser'       => 0, // No automatic redirect by browser language.
	'media_support' => 1, // Media items (title, caption, alt text) are translatable.
	'sync'          => array( '_thumbnail_id', 'post_date' ), // Same featured image and date in every translation.
);

foreach ( $languages as $args ) {
	$existing = PLL()->model->get_language( $args['slug'] );
	if ( $existing ) {
		$result = PLL()->model->update_language( array_merge( $args, array( 'lang_id' => $existing->term_id ) ) );
	} else {
		$result = PLL()->model->add_language( $args );
	}
	if ( is_wp_error( $result ) ) {
		WP_CLI::error( $result->get_error_message() );
	}
}

PLL()->model->update_default_lang( $default );

foreach ( $options as $key => $value ) {
	PLL()->options[ $key ] = $value;
}

PLL()->model->clean_languages_cache();
echo 'Polylang configured (' . implode( ', ', wp_list_pluck( $languages, 'slug' ) ) . ", default {$default}).\n";
