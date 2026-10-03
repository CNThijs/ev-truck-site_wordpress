<?php
/**
 * Navigation: menu markup tweaks, language switcher, header CTA.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give every menu link the shared nav-link class.
 *
 * @param array<string, string> $atts Link attributes.
 * @return array<string, string>
 */
function chargenet_nav_link_attributes( array $atts ): array {
	$atts['class'] = trim( ( $atts['class'] ?? '' ) . ' nav-link' );
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'chargenet_nav_link_attributes' );

/**
 * Primary menu parents get a disclosure button. A parent without its own URL becomes the button itself.
 *
 * @param string   $output Item markup.
 * @param WP_Post  $item   Menu item.
 * @param int      $depth  Depth.
 * @param stdClass $args   Menu args.
 */
function chargenet_submenu_toggle( string $output, WP_Post $item, int $depth, stdClass $args ): string {
	if ( 'primary' !== ( $args->theme_location ?? '' ) || 0 !== $depth || ! in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		return $output;
	}

	if ( in_array( $item->url, array( '', '#' ), true ) ) {
		return '<button type="button" class="nav-link submenu-toggle" aria-expanded="false">' . esc_html( $item->title ) . '</button>';
	}

	/* translators: %s: menu item title. */
	$label = sprintf( __( 'Show submenu for %s', 'chargenet' ), $item->title );
	return $output . '<button type="button" class="submenu-toggle" aria-expanded="false"><span class="visually-hidden">' . esc_html( $label ) . '</span></button>';
}
add_filter( 'walker_nav_menu_start_el', 'chargenet_submenu_toggle', 10, 4 );

/**
 * Language switcher. Uses Polylang when active; otherwise a non-functional placeholder.
 *
 * @param string $label Accessible name of the landmark; must differ per instance on a page.
 */
function chargenet_language_switcher( string $label = '' ): void {
	$label = '' !== $label ? $label : __( 'Language', 'chargenet' );
	echo '<nav class="lang-switch-wrap" aria-label="' . esc_attr( $label ) . '"><ul class="lang-switch">';

	if ( function_exists( 'pll_the_languages' ) ) {
		$languages = pll_the_languages(
			array(
				'raw'                    => 1,
				'hide_if_no_translation' => 0,
			)
		);
		foreach ( (array) $languages as $language ) {
			printf(
				'<li><a href="%1$s" lang="%2$s" hreflang="%2$s"%3$s>%4$s</a></li>',
				esc_url( $language['url'] ),
				esc_attr( $language['locale'] ? str_replace( '_', '-', $language['locale'] ) : $language['slug'] ),
				$language['current_lang'] ? ' aria-current="true"' : '',
				esc_html( strtoupper( $language['slug'] ) )
			);
		}
	} else {
		echo '<li><span class="nav-link" aria-current="true">EN</span></li><li><span class="nav-link">NL</span></li>';
	}

	echo '</ul></nav>';
}

/**
 * Header call-to-action. Scrolls to the contact block in the footer.
 */
function chargenet_header_cta(): void {
	printf(
		'<a class="btn btn--primary btn--sm" href="#contact">%s</a>',
		esc_html__( 'Contact Us', 'chargenet' )
	);
}
