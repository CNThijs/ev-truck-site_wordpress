<?php
/**
 * Asset enqueue helper. Reads the Vite manifest in production and the dev server when running `npm run dev`.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dev server URL if `npm run dev` is running locally (Vite writes a `hot` file), else null.
 */
function chargenet_dev_server(): ?string {
	if ( 'local' !== wp_get_environment_type() ) {
		return null;
	}
	$hot = CHARGENET_DIR . '/hot';
	return is_readable( $hot ) ? trim( (string) file_get_contents( $hot ) ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file.
}

/**
 * Parsed Vite manifest, or an empty array if the theme has not been built.
 *
 * @return array<string, array<string, mixed>>
 */
function chargenet_manifest(): array {
	static $manifest = null;
	if ( null === $manifest ) {
		$file     = CHARGENET_DIR . '/assets/dist/.vite/manifest.json';
		$decoded  = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local file.
		$manifest = is_array( $decoded ) ? $decoded : array();
	}
	return $manifest;
}

/**
 * Public URL of a built entry file (hashed filename), or null if missing.
 *
 * @param string $entry Entry key as in vite.config.js, e.g. 'assets/src/js/main.js'.
 */
function chargenet_asset_url( string $entry ): ?string {
	$manifest = chargenet_manifest();
	return isset( $manifest[ $entry ]['file'] ) ? CHARGENET_URI . '/assets/dist/' . $manifest[ $entry ]['file'] : null;
}

/**
 * Enqueue front-end assets.
 */
function chargenet_enqueue_assets(): void {
	$entry = 'assets/src/js/main.js';
	$dev   = chargenet_dev_server();

	if ( $dev ) {
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- dev server, no caching.
		wp_enqueue_script( 'chargenet-vite-client', $dev . '/@vite/client', array(), null, array( 'strategy' => 'defer' ) );
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- dev server, no caching.
		wp_enqueue_script( 'chargenet-main', $dev . '/' . $entry, array(), null, array( 'strategy' => 'defer' ) );
		return;
	}

	$manifest = chargenet_manifest();
	if ( ! isset( $manifest[ $entry ] ) ) {
		return;
	}

	foreach ( $manifest[ $entry ]['css'] ?? array() as $i => $css ) {
		wp_enqueue_style( 'chargenet-main-' . $i, CHARGENET_URI . '/assets/dist/' . $css, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- filename is hashed.
	}
	wp_enqueue_script( 'chargenet-main', (string) chargenet_asset_url( $entry ), array(), null, array( 'strategy' => 'defer' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- filename is hashed.
}
add_action( 'wp_enqueue_scripts', 'chargenet_enqueue_assets' );

/**
 * Vite dev scripts must load as ES modules.
 *
 * @param string $tag    Script tag.
 * @param string $handle Script handle.
 */
function chargenet_module_scripts( string $tag, string $handle ): string {
	if ( in_array( $handle, array( 'chargenet-main', 'chargenet-vite-client' ), true ) ) {
		return str_replace( '<script ', '<script type="module" ', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'chargenet_module_scripts', 10, 2 );

/**
 * Editor styles from the built editor entry (production build only).
 */
function chargenet_editor_styles(): void {
	$manifest = chargenet_manifest();
	if ( isset( $manifest['assets/src/scss/editor.scss']['file'] ) ) {
		add_editor_style( 'assets/dist/' . $manifest['assets/src/scss/editor.scss']['file'] );
	}
}
add_action( 'after_setup_theme', 'chargenet_editor_styles' );
