<?php
/**
 * Admin-only style guide at /style-guide/. Everyone else gets a 404.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the route; flush rewrite rules once per theme version.
 */
function chargenet_style_guide_route(): void {
	add_rewrite_rule( '^style-guide/?$', 'index.php?chargenet_style_guide=1', 'top' );
	if ( get_option( 'chargenet_rules_version' ) !== CHARGENET_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'chargenet_rules_version', CHARGENET_VERSION );
	}
}
add_action( 'init', 'chargenet_style_guide_route' );

/**
 * Register the query var.
 *
 * @param string[] $vars Public query vars.
 * @return string[]
 */
function chargenet_style_guide_query_var( array $vars ): array {
	$vars[] = 'chargenet_style_guide';
	return $vars;
}
add_filter( 'query_vars', 'chargenet_style_guide_query_var' );

/**
 * Serve the template to administrators; 404 for everyone else.
 *
 * @param string $template Resolved template path.
 */
function chargenet_style_guide_template( string $template ): string {
	if ( ! get_query_var( 'chargenet_style_guide' ) ) {
		return $template;
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow' );

	if ( ! current_user_can( 'manage_options' ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		return get_404_template();
	}
	return CHARGENET_DIR . '/page-templates/style-guide.php';
}
add_filter( 'template_include', 'chargenet_style_guide_template' );

/**
 * WCAG contrast ratio between two hex colours.
 *
 * @param string $a Hex colour, e.g. #FFFFFF.
 * @param string $b Hex colour.
 */
function chargenet_contrast_ratio( string $a, string $b ): float {
	$lum = static function ( string $hex ): float {
		$rgb = array_map(
			static function ( string $part ): float {
				$v = hexdec( $part ) / 255;
				return $v <= 0.03928 ? $v / 12.92 : ( ( $v + 0.055 ) / 1.055 ) ** 2.4;
			},
			str_split( ltrim( $hex, '#' ), 2 )
		);
		return 0.2126 * $rgb[0] + 0.7152 * $rgb[1] + 0.0722 * $rgb[2];
	};
	$l1  = max( $lum( $a ), $lum( $b ) );
	$l2  = min( $lum( $a ), $lum( $b ) );
	return ( $l1 + 0.05 ) / ( $l2 + 0.05 );
}
