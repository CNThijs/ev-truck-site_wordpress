<?php
/**
 * Cookie consent (WPConsent free): the banner, preferences panel and cookie list in English and Dutch. The plugin keeps
 * one set of texts, so the texts live here as gettext strings and replace the plugin's values at runtime. Settings
 * (defaults denied, colours, script blocking): bin/setup-wpconsent.php. Loading Tag Manager after consent: inc/tracking.php.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Banner and panel texts of the plugin, by option name, in the language of the page.
 *
 * @return array<string,string>
 */
function chargenet_consent_texts(): array {
	return array(
		'banner_message'                    => __( 'We use cookies to measure how our website is used and to improve it. These statistics cookies are only placed if you accept. You can change your choice at any time.', 'chargenet' ),
		'accept_button_text'                => __( 'Accept all', 'chargenet' ),
		'cancel_button_text'                => __( 'Reject all', 'chargenet' ),
		'preferences_button_text'           => __( 'Settings', 'chargenet' ),
		'preferences_panel_title'           => __( 'Cookie settings', 'chargenet' ),
		'preferences_panel_description'     => __( 'Choose which cookies we may use. Essential cookies are always on, because the site needs them.', 'chargenet' ),
		'cookie_policy_title'               => __( 'Cookie policy', 'chargenet' ),
		'cookie_policy_text'                => __( 'Read more in our {cookie_policy} and {privacy_policy}.', 'chargenet' ),
		'save_preferences_button_text'      => __( 'Save my choice', 'chargenet' ),
		'close_button_text'                 => __( 'Close', 'chargenet' ),
		'cookie_table_header_name'          => __( 'Name', 'chargenet' ),
		'cookie_table_header_description'   => __( 'Description', 'chargenet' ),
		'cookie_table_header_duration'      => __( 'Duration', 'chargenet' ),
		'cookie_table_header_service_url'   => __( 'Service URL', 'chargenet' ),
		'content_blocking_placeholder_text' => __( 'Click here to accept {category} cookies and load this content', 'chargenet' ),
	);
}

/**
 * Replace the plugin's texts with the theme's.
 *
 * @param mixed  $value Value from the plugin.
 * @param string $name  Option name.
 * @return mixed
 */
function chargenet_consent_option( $value, $name ) {
	$texts = chargenet_consent_texts();
	return $texts[ $name ] ?? $value;
}
add_filter( 'wpconsent_get_option', 'chargenet_consent_option', 10, 2 );

/**
 * The cookie policy and privacy policy page of the page's language (the plugin stores the English page).
 *
 * @param int    $page_id Page ID from the plugin.
 * @param string $locale  Locale of the page.
 */
function chargenet_consent_policy_page( $page_id, $locale = '' ) {
	if ( ! function_exists( 'pll_get_post' ) || ! $page_id ) {
		return $page_id;
	}
	$translated = pll_get_post( (int) $page_id, '' !== (string) $locale ? (string) $locale : pll_current_language() );
	return $translated ? $translated : $page_id;
}
add_filter( 'wpconsent_get_cookie_policy_id', 'chargenet_consent_policy_page', 10, 2 );
add_filter( 'wpconsent_get_privacy_policy_id', 'chargenet_consent_policy_page', 10, 2 );
add_filter( 'wpconsent_get_option_cookie_policy_page', 'chargenet_consent_policy_page' );

/**
 * Category names and descriptions.
 *
 * @param array<string,mixed> $data    Category data.
 * @param int                 $term_id Term ID.
 * @return array<string,mixed>
 */
function chargenet_consent_category( $data, $term_id ) {
	$term = get_term( (int) $term_id );
	$text = array(
		'essential'  => array(
			__( 'Essential', 'chargenet' ),
			__( 'Essential cookies make the site work, for example by remembering your cookie choice. They cannot be switched off.', 'chargenet' ),
		),
		'statistics' => array(
			__( 'Statistics', 'chargenet' ),
			__( 'Statistics cookies help us understand how visitors use the site and which campaigns bring them here. We do not use them for advertising.', 'chargenet' ),
		),
		'marketing'  => array(
			__( 'Marketing', 'chargenet' ),
			__( 'Marketing cookies follow visitors across websites to show relevant advertisements. ChargeNet does not use them at the moment.', 'chargenet' ),
		),
	);
	if ( $term instanceof WP_Term && isset( $text[ $term->slug ] ) ) {
		$data['name']        = $text[ $term->slug ][0];
		$data['description'] = $text[ $term->slug ][1];
	}
	return $data;
}
add_filter( 'wpconsent_category_data', 'chargenet_consent_category', 10, 2 );

/**
 * Description and duration of the cookies and services we register (bin/setup-wpconsent.php).
 *
 * @param array<string,mixed> $data Cookie data.
 * @return array<string,mixed>
 */
function chargenet_consent_cookie( $data ) {
	$text = array(
		'_ga'                   => array( __( 'Used by Google Analytics to tell visitors apart without identifying them.', 'chargenet' ), __( '2 years', 'chargenet' ) ),
		'_ga_*'                 => array( __( 'Used by Google Analytics to keep the state of a visit.', 'chargenet' ), __( '2 years', 'chargenet' ) ),
		'cn_campaign'           => array( __( 'Remembers the campaign link (UTM parameters) that brought you here, so that a request you send can be linked to it.', 'chargenet' ), __( '30 days', 'chargenet' ) ),
		'wpconsent_preferences' => array( __( 'Remembers your cookie choice.', 'chargenet' ), __( '180 days', 'chargenet' ) ),
		'cn_consent_id'         => array( __( 'Links your choice to the record that shows you gave or refused consent (browser storage).', 'chargenet' ), __( 'Until you clear your browser data', 'chargenet' ) ),
		'pll_language'          => array( __( 'Remembers the language of the website that you chose.', 'chargenet' ), __( '1 year', 'chargenet' ) ),
	);
	$id   = (string) ( $data['cookie_id'] ?? '' );
	if ( isset( $text[ $id ] ) ) {
		$data['description'] = $text[ $id ][0];
		$data['duration']    = $text[ $id ][1];
	}
	return $data;
}
add_filter( 'wpconsent_cookie_data', 'chargenet_consent_cookie' );

/**
 * Description of the services we register.
 *
 * @param array<string,mixed> $data Service data.
 * @return array<string,mixed>
 */
function chargenet_consent_service( $data ) {
	$name = (string) ( $data['name'] ?? '' );
	if ( 'Google Tag Manager' === $name ) {
		$data['description'] = __( 'Loads our analytics tags, only after you accept statistics cookies.', 'chargenet' );
	} elseif ( 'Google Analytics' === $name ) {
		$data['description'] = __( 'Measures visits and clicks (page views, buttons, forms) without advertising features.', 'chargenet' );
	}
	return $data;
}
add_filter( 'wpconsent_service_data', 'chargenet_consent_service' );
