<?php
/**
 * Self-hosted Source Sans 3 (SIL OFL, see assets/fonts/OFL-LICENSE.txt). Variable font, Latin and
 * Latin Extended subsets (covers English and Dutch). No third-party font requests.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preload the Latin subset and declare the font faces inline so text renders without waiting for the CSS bundle.
 */
function chargenet_fonts(): void {
	$base  = CHARGENET_URI . '/assets/fonts/source-sans-3-';
	$latin = $base . 'latin-wght-normal.woff2';
	$ext   = $base . 'latin-ext-wght-normal.woff2';

	printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( $latin ) );

	// Fallback face: Arial scaled to Source Sans 3 metrics, so the swap causes no layout shift.
	$css = '@font-face{font-family:"Source Sans 3 Fallback";src:local("Arial");size-adjust:93.76%;ascent-override:109.21%;descent-override:42.66%;line-gap-override:0%}'
		. '@font-face{font-family:"Source Sans 3";font-style:normal;font-weight:200 900;font-display:swap;src:url("' . esc_url( $latin ) . '") format("woff2");unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}'
		. '@font-face{font-family:"Source Sans 3";font-style:normal;font-weight:200 900;font-display:swap;src:url("' . esc_url( $ext ) . '") format("woff2");unicode-range:U+0100-02BA,U+02BD-02C5,U+02C7-02CC,U+02CE-02D7,U+02DD-02FF,U+0304,U+0308,U+0329,U+1D00-1DBF,U+1E00-1E9F,U+1EF2-1EFF,U+2020,U+20A0-20AB,U+20AD-20C0,U+2113,U+2C60-2C7F,U+A720-A7FF}';

	wp_register_style( 'chargenet-fonts', false, array(), CHARGENET_VERSION );
	wp_enqueue_style( 'chargenet-fonts' );
	wp_add_inline_style( 'chargenet-fonts', $css );
}
add_action( 'wp_head', 'chargenet_fonts', 1 );
