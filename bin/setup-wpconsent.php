<?php
/**
 * WPConsent (free) settings. Run by bin/setup-wp.sh after the pages are seeded; safe to run again:
 * `wp eval-file bin/setup-wpconsent.php`.
 *
 * Choices: the banner is on for every visitor and consent is denied until the visitor chooses (no region rules in the
 * free version, so the strict rule applies everywhere); scripts of known services are blocked until consent; the
 * choice is remembered for 180 days; Accept and Reject look the same size and prominence; colours from tokens.json;
 * no floating icon (the footer has a "Cookie settings" button); Google Consent Mode v2 defaults come from the plugin
 * once a Google service is in its list. Texts: inc/consent.php (gettext, English and Dutch).
 */

if ( ! function_exists( 'wpconsent' ) ) {
	WP_CLI::error( 'WPConsent is not active.' );
}

$cn_find_page = static function ( string $key ): int {
	$ids = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'meta_key'       => '_chargenet_seed_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'lang'           => 'en',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	return $ids ? (int) $ids[0] : 0;
};

$cn_privacy = $cn_find_page( 'privacy' );
if ( $cn_privacy ) {
	update_option( 'wp_page_for_privacy_policy', $cn_privacy );
}

wpconsent()->settings->bulk_update_options(
	array(
		'enable_consent_banner'      => 1,
		'enable_script_blocking'     => 1,
		'enable_content_blocking'    => 1,
		'default_allow'              => 0,
		'consent_duration'           => 180,
		'enable_consent_floating'    => 0,
		'disable_close_button'       => 1, // Closing without a choice is not a choice: no close button.
		'hide_powered_by'            => 1,
		'banner_layout'              => 'long',
		'banner_position'            => 'bottom',
		'banner_background_color'    => '#083A0B', // forest-900.
		'banner_text_color'          => '#FFFFFF',
		'banner_font_size'           => '16px',
		'banner_button_size'         => 'regular',
		'banner_button_corner'       => 'rounded',
		'banner_button_type'         => 'filled',
		'banner_accept_bg'           => '#FAE104', // volt-400.
		'banner_accept_color'        => '#083A0B',
		'banner_cancel_bg'           => '#FFFFFF', // As prominent as Accept.
		'banner_cancel_color'        => '#083A0B',
		'banner_preferences_bg'      => '#FFFFFF',
		'banner_preferences_color'   => '#083A0B',
		'accept_button_enabled'      => 1,
		'cancel_button_enabled'      => 1,
		'preferences_button_enabled' => 1,
		'button_order'               => array( 'cancel', 'preferences', 'accept' ),
		'cookie_policy_page'         => $cn_find_page( 'cookie-policy' ),
		'onboarding_completed'       => 1,
		'analytics_enabled'          => 0, // The plugin's own banner statistics: off, nothing is collected about the banner.
		'gcm_url_passthrough'        => 0,
		'gcm_ads_data_redaction'     => 1,
	)
);

// Services and cookies in the plugin's list (the preferences panel, the cookie table and Consent Mode read this).
$cn_cookies  = wpconsent()->cookies;
$cn_stat     = (int) $cn_cookies->get_categories()['statistics']['id'];
$cn_service  = static function ( string $name, string $description, string $url ) use ( $cn_cookies, $cn_stat ): int {
	$slug = sanitize_title( $name );
	$have = $cn_cookies->get_service_by_slug( $slug );
	return $have ? (int) $have['id'] : (int) $cn_cookies->add_service( $name, $cn_stat, $description, $url );
};
$cn_gtm      = $cn_service( 'Google Tag Manager', 'Loads our analytics tags after consent.', 'https://policies.google.com/privacy' );
$cn_ga       = $cn_service( 'Google Analytics', 'Measures visits and clicks.', 'https://policies.google.com/privacy' );
$cn_register = static function ( string $id, string $name, string $description, int $category, string $duration ) use ( $cn_cookies ): void {
	foreach ( get_posts(
		array(
			'post_type'      => 'wpconsent_cookie',
			'post_status'    => 'any',
			'meta_key'       => 'wpconsent_cookie_id', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	) as $post_id ) {
		$cn_cookies->update_cookie( $post_id, $id, $name, $description, $category, $duration );
		return;
	}
	$cn_cookies->add_cookie( $id, $name, $description, $category, $duration );
};
$cn_register( '_ga', '_ga', 'Used by Google Analytics to tell visitors apart.', $cn_ga, '2 years' );
$cn_register( '_ga_*', '_ga_*', 'Used by Google Analytics to keep the state of a visit.', $cn_ga, '2 years' );
$cn_essential = (int) $cn_cookies->get_categories()['essential']['id'];
$cn_register( 'cn_consent_id', 'cn_consent_id', 'Links your choice to the record that shows you gave or refused consent.', $cn_essential, 'Until you clear your browser data' );
$cn_register( 'pll_language', 'pll_language', 'Remembers the language of the website that you chose.', $cn_essential, '1 year' );
$cn_register( 'cn_campaign', 'cn_campaign', 'Remembers the campaign link that brought you here.', $cn_stat, '30 days' );
delete_transient( 'wpconsent_needs_google_consent' );
delete_transient( 'wpconsent_preference_slugs' );
$cn_cookies->clear_cookies_cache();

echo "WPConsent configured.\n";
