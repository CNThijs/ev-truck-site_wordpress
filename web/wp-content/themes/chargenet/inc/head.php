<?php
/**
 * Head extras: JS flag and favicons.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Mark the document as JS-capable before first paint (menu falls back to open without JS).
 */
function chargenet_js_flag(): void {
	wp_print_inline_script_tag( "document.documentElement.classList.add('js');" );
}
add_action( 'wp_head', 'chargenet_js_flag', 0 );

/**
 * Brand favicons, unless an admin set a Site Icon in the Customizer.
 */
function chargenet_favicons(): void {
	if ( has_site_icon() ) {
		return;
	}
	$dir = CHARGENET_URI . '/assets/img/favicons/';
	printf( '<link rel="icon" href="%s" sizes="any">' . "\n", esc_url( $dir . 'favicon.ico' ) );
	printf( '<link rel="icon" type="image/png" sizes="32x32" href="%s">' . "\n", esc_url( $dir . 'favicon-32x32.png' ) );
	printf( '<link rel="icon" type="image/png" sizes="16x16" href="%s">' . "\n", esc_url( $dir . 'favicon-16x16.png' ) );
	printf( '<link rel="apple-touch-icon" href="%s">' . "\n", esc_url( $dir . 'apple-touch-icon.png' ) );
}
add_action( 'wp_head', 'chargenet_favicons' );
