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
 * Early inline script: marks the document as JS-capable before first paint (the menu falls back to open without JS,
 * and CSS may hide animation start states only when this class is set). If the motion code has not taken over
 * after 4 seconds (blocked or failed script), data-motion="timeout" releases every hidden start state.
 */
function chargenet_js_flag(): void {
	wp_print_inline_script_tag( "document.documentElement.classList.add('js');setTimeout(function(){var d=document.documentElement;if(!d.dataset.motion)d.dataset.motion='timeout'},4000);" );
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

/**
 * Reading-progress bar for posts and pages. Hidden until motion/features/progress.js shows it on long pages.
 */
function chargenet_scroll_progress(): void {
	if ( is_singular() && ! is_front_page() ) {
		echo '<div class="scroll-progress" data-scroll-progress aria-hidden="true" hidden></div>' . "\n";
	}
}
add_action( 'wp_body_open', 'chargenet_scroll_progress' );
