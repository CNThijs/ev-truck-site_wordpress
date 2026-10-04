<?php
/**
 * Hreflang output: Polylang prints the alternates (self link included, only for languages that have a translation).
 * It adds x-default only on the front page and points it at the bare site root, which redirects. Here x-default
 * is the default-language version of the same page, on every page that has one.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set x-default to the default-language URL of the current page.
 *
 * @param array<string, string> $hreflangs URLs keyed by hreflang value.
 * @return array<string, string>
 */
function chargenet_hreflang_x_default( array $hreflangs ): array {
	unset( $hreflangs['x-default'] );
	$default = function_exists( 'pll_default_language' ) ? pll_default_language( 'slug' ) : '';
	if ( $default && isset( $hreflangs[ $default ] ) ) {
		$hreflangs['x-default'] = $hreflangs[ $default ];
	}
	return $hreflangs;
}
add_filter( 'pll_rel_hreflang_attributes', 'chargenet_hreflang_x_default' );
