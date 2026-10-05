<?php
/**
 * Redirects from the URLs of the old site (a React app without language prefixes) to the new ones, so links, bookmarks
 * and search results keep working. The old paired slugs go to the language they were written in; the Dutch campaign
 * shortlinks keep their tracking parameters and go to the Dutch pages. Post slugs go to the English post.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_CAMPAIGN_UTM = 'utm_source=trend_report&utm_medium=DM&utm_campaign=tr2027';

/**
 * Where an old path goes, or '' when it is not an old URL.
 *
 * @param string $path  Path without slashes at both ends.
 * @param string $query Query string of the request.
 * @return string New URL path (with query) or ''.
 */
function chargenet_legacy_target( string $path, string $query ): string {
	$code_path = $path; // Campaign codes keep their case.
	$path      = strtolower( $path );
	$pages     = array(
		'locations'                     => '/en/locations/',
		'carriers'                      => '/en/carriers/',
		'about'                         => '/en/about/',
		'faq'                           => '/en/faq/',
		'careers'                       => '/en/careers/',
		'security'                      => '/en/security/',
		'privacy'                       => '/en/privacy-policy/',
		'privacy-policy'                => '/en/privacy-policy/',
		'blog'                          => '/en/blog/',
		'projects/destination-charging' => '/en/destination-charging/',
		'projects/chargebase'           => '/en/chargebase-project/',
		'projects/ijmondaanzet'         => '/en/ijmond-aan-zet-project/',
		'projects/bouw-pow'             => '/en/bouw-pow-project/',
		'locaties'                      => '/nl/locaties/',
		'locatie'                       => '/nl/locaties/',
		'vervoerder'                    => '/nl/vervoerders/',
		'vervoerders'                   => '/nl/vervoerders/',
		'over-ons'                      => '/nl/over-ons/',
		'projecten/laden-op-bestemming' => '/nl/laden-op-bestemming/',
		'projecten/chargebase'          => '/nl/chargebase/',
		'projecten/ijmondaanzet'        => '/nl/ijmond-aan-zet/',
		'projecten/bouw-pow'            => '/nl/bouw-pow/',
	);
	if ( isset( $pages[ $path ] ) ) {
		return $pages[ $path ] . ( '' !== $query ? '?' . $query : '' );
	}

	// Campaign shortlinks: the old site added the tracking parameters on the client.
	$utm = CHARGENET_CAMPAIGN_UTM;
	if ( 'routecheck' === $path ) {
		return "/nl/vervoerders/?{$utm}&utm_term=laders";
	}
	if ( 'opbrengst' === $path ) {
		return "/nl/locaties/?{$utm}&utm_term=locaties";
	}
	if ( 'rapport2027' === $path ) {
		return '/nl/rapport2027/?' . ( '' !== $query ? $query : "{$utm}&utm_term=form-access" );
	}
	if ( preg_match( '#^rapport2027/([A-Za-z0-9]+)$#i', $code_path, $m ) ) {
		return "/nl/rapport2027/?{$utm}&utm_term=" . rawurlencode( $m[1] );
	}

	// Old post URLs: /blog/<slug>. Only when that post exists.
	if ( preg_match( '#^blog/([a-z0-9-]+)$#', $path, $m ) && get_page_by_path( $m[1], OBJECT, 'post' ) ) {
		return '/en/blog/' . $m[1] . '/';
	}
	return '';
}

/**
 * Redirect an old URL (301) before WordPress and Polylang look at it.
 */
function chargenet_legacy_redirect(): void {
	if ( is_admin() || wp_doing_ajax() || ! isset( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}
	$uri  = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
	if ( '' === $path || preg_match( '#^(en|nl|wp-|index\.php)#i', $path ) ) {
		return;
	}
	$target = chargenet_legacy_target( $path, (string) wp_parse_url( $uri, PHP_URL_QUERY ) );
	if ( '' !== $target ) {
		wp_safe_redirect( home_url( $target ), 301, 'ChargeNet' );
		exit;
	}
}
add_action( 'init', 'chargenet_legacy_redirect', 20 ); // After post types exist, before the main query.
