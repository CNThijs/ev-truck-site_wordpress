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
 * Most cards a Card Slider with the horizontal scroll preset shows (docs/motion.md).
 */
const CHARGENET_HORIZONTAL_MAX_CARDS = 10;

/**
 * Number of sections opened so far on this page (1 inside the first section). Used to tell first-viewport images apart.
 *
 * @param bool $increment Count a newly opened section.
 */
function chargenet_section_counter( bool $increment = false ): int {
	static $count = 0;
	if ( $increment ) {
		++$count;
	}
	return $count;
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
	chargenet_section_counter( true );
	$hide    = chargenet_section_option( $attributes, 'hideOn' );
	$classes = array(
		'section',
		'is-' . chargenet_section_option( $attributes, 'sectionBackground' ),
		'section--' . sanitize_html_class( (string) ( $args['name'] ?? 'section' ) ),
	);
	if ( 'none' !== $hide ) {
		$classes[] = 'hide-' . $hide;
	}
	if ( chargenet_in_first_section() ) {
		$classes[] = 'is-first-section'; // Motion never hides or moves content here (largest contentful paint).
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
	// data-split: hook for the "text-lines" animation preset (docs/motion.md).
	printf(
		'<h%1$d%2$s%3$s data-split>%4$s</h%1$d>',
		(int) $level,
		'' !== $id ? ' id="' . esc_attr( $id ) . '"' : '',
		'' !== $css_class ? ' class="' . esc_attr( $css_class ) . '"' : '',
		wp_kses_post( $text )
	);
}

/**
 * Id for a section's heading when it has one (for aria-labelledby), else an empty string.
 *
 * @param array<string, mixed> $attributes Block attributes.
 */
function chargenet_section_title_id( array $attributes ): string {
	return '' !== trim( wp_strip_all_tags( (string) ( $attributes['heading'] ?? '' ) ) ) ? wp_unique_id( 'section-title-' ) : '';
}

/**
 * Intro block above a section's content: eyebrow, heading, introduction. Prints nothing when all three are empty.
 *
 * @param array<string, mixed> $attributes Block attributes (eyebrow, heading, headingLevel, intro).
 * @param string               $title_id   Id from chargenet_section_title_id().
 */
function chargenet_section_header( array $attributes, string $title_id ): void {
	$eyebrow = trim( wp_strip_all_tags( (string) ( $attributes['eyebrow'] ?? '' ) ) );
	$intro   = (string) ( $attributes['intro'] ?? '' );
	$has_in  = '' !== trim( wp_strip_all_tags( $intro ) );
	if ( '' === $eyebrow && '' === $title_id && ! $has_in ) {
		return;
	}
	echo '<header class="section-intro stack">';
	if ( '' !== $eyebrow ) {
		echo '<p class="t-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	chargenet_heading( (string) ( $attributes['heading'] ?? '' ), (int) ( $attributes['headingLevel'] ?? 2 ), $title_id );
	if ( $has_in ) {
		echo '<p class="t-lead">' . wp_kses_post( $intro ) . '</p>';
	}
	echo '</header>';
}
