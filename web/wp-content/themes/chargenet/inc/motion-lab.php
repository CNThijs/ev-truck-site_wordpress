<?php
/**
 * Admin-only Motion Lab at /motion-lab/: every animation preset with sample content, a switch that simulates
 * reduced motion and a replay button. Everyone else gets a 404.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the route; flush rewrite rules once when it is first added.
 */
function chargenet_motion_lab_route(): void {
	add_rewrite_rule( '^motion-lab/?$', 'index.php?chargenet_motion_lab=1', 'top' );
	if ( '1' !== get_option( 'chargenet_motion_lab_route' ) ) {
		flush_rewrite_rules( false );
		update_option( 'chargenet_motion_lab_route', '1' );
	}
}
add_action( 'init', 'chargenet_motion_lab_route' );

/**
 * Register the query var.
 *
 * @param string[] $vars Public query vars.
 * @return string[]
 */
function chargenet_motion_lab_query_var( array $vars ): array {
	$vars[] = 'chargenet_motion_lab';
	return $vars;
}
add_filter( 'query_vars', 'chargenet_motion_lab_query_var' );

/**
 * Serve the template to administrators; 404 for everyone else.
 *
 * @param string $template Resolved template path.
 */
function chargenet_motion_lab_template( string $template ): string {
	if ( ! get_query_var( 'chargenet_motion_lab' ) ) {
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
	return CHARGENET_DIR . '/page-templates/motion-lab.php';
}
add_filter( 'template_include', 'chargenet_motion_lab_template' );

/**
 * Keep Polylang from redirecting the lab to a language prefix: it is a tool page, not content.
 *
 * @param string|false $redirect Redirect URL.
 * @return string|false
 */
function chargenet_motion_lab_no_canonical_redirect( $redirect ) {
	return get_query_var( 'chargenet_motion_lab' ) ? false : $redirect;
}
add_filter( 'redirect_canonical', 'chargenet_motion_lab_no_canonical_redirect' );
add_filter( 'pll_check_canonical_url', 'chargenet_motion_lab_no_canonical_redirect' );
