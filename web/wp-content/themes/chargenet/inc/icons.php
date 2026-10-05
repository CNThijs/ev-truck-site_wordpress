<?php
/**
 * Built-in icon set: 24px outline icons drawn from Lucide (ISC licence), stored as inline SVG in icons.json
 * (also read by the block editor). No icon font, no extra request.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon name => inner SVG markup.
 *
 * @return array<string, string>
 */
function chargenet_icons(): array {
	static $icons = null;
	if ( null === $icons ) {
		$decoded = wp_json_file_decode( CHARGENET_DIR . '/inc/icons.json', array( 'associative' => true ) );
		$icons   = is_array( $decoded ) ? $decoded : array();
	}
	return $icons;
}

/**
 * Print a decorative icon (aria-hidden: always paired with visible text). Unknown names print nothing.
 *
 * @param string $name  Icon name from icons.json.
 * @param string $extra_class Extra class.
 */
function chargenet_the_icon( string $name, string $extra_class = '' ): void {
	$icons = chargenet_icons();
	if ( ! isset( $icons[ $name ] ) ) {
		return;
	}
	printf(
		'<svg class="icon%s" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		'' !== $extra_class ? ' ' . esc_attr( $extra_class ) : '',
		$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme file.
	);
}
