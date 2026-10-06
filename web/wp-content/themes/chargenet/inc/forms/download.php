<?php
/**
 * The trend report PDF at a fixed address: /downloads/ChargeNet-TR2027.pdf. The file lives in
 * wp-content/uploads/chargenet-downloads/ (outside the theme and git: it is 10 MB) and is streamed from there, so
 * the address from the old site and from earlier emails keeps working.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rewrite rule for the report.
 */
function chargenet_download_rewrite(): void {
	add_rewrite_rule( '^downloads/(ChargeNet-TR2027\.pdf)$', 'index.php?cn_download=$matches[1]', 'top' );
}
add_action( 'init', 'chargenet_download_rewrite' );

/**
 * Allow the query variable.
 *
 * @param string[] $vars Variables.
 * @return string[]
 */
function chargenet_download_query_vars( array $vars ): array {
	$vars[] = 'cn_download';
	return $vars;
}
add_filter( 'query_vars', 'chargenet_download_query_vars' );

/**
 * Stream the PDF.
 */
function chargenet_download_serve(): void {
	$file = (string) get_query_var( 'cn_download' );
	if ( '' === $file ) {
		return;
	}
	$path = trailingslashit( wp_upload_dir()['basedir'] ) . 'chargenet-downloads/' . basename( $file );
	if ( ! is_readable( $path ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		return;
	}
	nocache_headers();
	header( 'Content-Type: application/pdf' );
	header( 'Content-Disposition: inline; filename="' . basename( $path ) . '"' );
	header( 'Content-Length: ' . filesize( $path ) );
	header( 'X-Robots-Tag: noindex' );
	readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
	exit;
}
add_action( 'template_redirect', 'chargenet_download_serve', 1 );

/**
 * Flush the rewrite rules once when this code is first deployed.
 */
function chargenet_download_flush(): void {
	if ( '1' !== get_option( 'chargenet_download_rules' ) ) {
		flush_rewrite_rules( false );
		update_option( 'chargenet_download_rules', '1', true );
	}
}
add_action( 'init', 'chargenet_download_flush', 99 );
