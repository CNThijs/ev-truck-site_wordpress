<?php
/**
 * Section wrapper helpers. Every section block renders through these so markup stays consistent.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared section attribute definitions (single source: inc/section-attributes.json, also read by the editor build).
 *
 * @return array<string, array<string, mixed>>
 */
function chargenet_section_attributes(): array {
	static $attributes = null;
	if ( null === $attributes ) {
		$decoded    = wp_json_file_decode( CHARGENET_DIR . '/inc/section-attributes.json', array( 'associative' => true ) );
		$attributes = is_array( $decoded ) ? $decoded : array();
	}
	return $attributes;
}

/**
 * Return $value if it is an allowed option of a shared attribute, else that attribute's default.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $key        Shared attribute name.
 */
function chargenet_section_option( array $attributes, string $key ): string {
	$definition = chargenet_section_attributes()[ $key ] ?? array();
	$value      = (string) ( $attributes[ $key ] ?? '' );
	return in_array( $value, $definition['enum'] ?? array(), true ) ? $value : (string) ( $definition['default'] ?? '' );
}

/**
 * Open a section: <section> (with anchor id, classes and data attributes) and the inner container.
 *
 * Pair with chargenet_section_close(). Call from a block's render.php, where block supports are available.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param array<string, mixed> $args       name (string, required), labelledby (heading id), label (aria-label), container (narrow|wide|'' ), class.
 */
function chargenet_section_open( array $attributes, array $args = array() ): void {
	$hide    = chargenet_section_option( $attributes, 'hideOn' );
	$classes = array(
		'section',
		'is-' . chargenet_section_option( $attributes, 'sectionBackground' ),
		'section--' . sanitize_html_class( (string) ( $args['name'] ?? 'section' ) ),
	);
	if ( 'none' !== $hide ) {
		$classes[] = 'hide-' . $hide;
	}
	if ( ! empty( $args['class'] ) ) {
		$classes[] = $args['class'];
	}

	$wrapper = array(
		'class'             => implode( ' ', $classes ),
		'data-space-top'    => chargenet_section_option( $attributes, 'spaceTop' ),
		'data-space-bottom' => chargenet_section_option( $attributes, 'spaceBottom' ),
	);
	$anim    = sanitize_html_class( (string) ( $attributes['animation'] ?? '' ) );
	if ( '' !== $anim ) {
		$wrapper['data-animation'] = $anim;
	}
	if ( ! empty( $args['labelledby'] ) ) {
		$wrapper['aria-labelledby'] = $args['labelledby'];
	} elseif ( ! empty( $args['label'] ) ) {
		$wrapper['aria-label'] = $args['label'];
	}

	$container = in_array( $args['container'] ?? '', array( 'narrow', 'wide' ), true ) ? ' container--' . $args['container'] : '';

	// get_block_wrapper_attributes() escapes values and adds the editor's anchor id.
	printf( '<section %s><div class="container%s">', get_block_wrapper_attributes( $wrapper ), esc_attr( $container ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Close a section opened with chargenet_section_open().
 */
function chargenet_section_close(): void {
	echo '</div></section>';
}

/**
 * Heading with a validated level (h2 to h4; the page title is the only h1).
 *
 * @param string $text  Heading HTML (inline markup allowed).
 * @param int    $level 2, 3 or 4.
 * @param string $id    Optional id, for aria-labelledby.
 * @param string $css_class Optional class.
 */
function chargenet_heading( string $text, int $level = 2, string $id = '', string $css_class = '' ): void {
	if ( '' === trim( wp_strip_all_tags( $text ) ) ) {
		return;
	}
	$level = max( 2, min( 4, $level ) );
	printf(
		'<h%1$d%2$s%3$s>%4$s</h%1$d>',
		(int) $level,
		'' !== $id ? ' id="' . esc_attr( $id ) . '"' : '',
		'' !== $css_class ? ' class="' . esc_attr( $css_class ) . '"' : '',
		wp_kses_post( $text )
	);
}
