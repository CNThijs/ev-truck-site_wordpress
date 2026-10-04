<?php
/**
 * Seeds starter menus in English and Dutch and assigns them per language in Polylang.
 * Run by bin/setup-wp.sh (wp eval-file). Idempotent: existing menus are kept, never overwritten.
 *
 * URLs are placeholders (custom links); replace them with real pages when those exist.
 */

$languages = array(
	'en' => array(
		'primary' => array(
			array( 'Home', '/' ),
			array( 'About', '/about/' ),
			array( 'Customers', '#', array( array( 'Locations', '/locations/' ), array( 'Carriers', '/carriers/' ) ) ),
			array( 'News', '/blog/' ),
		),
		'utility' => array( array( 'Login', 'https://portal.chargenet.energy', array(), true ) ),
		'footer'  => array(
			array( 'About Us', '/about/' ),
			array( 'FAQ', '/faq/' ),
			array( 'Careers', '/careers/' ),
			array( 'Security', '/security/' ),
		),
		'legal'   => array(
			array( 'Privacy Policy', '/privacy-policy/' ),
			array( 'Security', '/security/' ),
		),
	),
	'nl' => array(
		'primary' => array(
			array( 'Home', '/' ),
			array( 'Over Ons', '/over-ons/' ),
			array( 'Klanten', '#', array( array( 'Locaties', '/locaties/' ), array( 'Vervoerders', '/vervoerders/' ) ) ),
			array( 'Nieuws', '/blog/' ),
		),
		'utility' => array( array( 'Login', 'https://portal.chargenet.energy', array(), true ) ),
		'footer'  => array(
			array( 'Over Ons', '/over-ons/' ),
			array( 'Veelgestelde Vragen (FAQ)', '/veelgestelde-vragen/' ),
			array( 'Carrière', '/carriere/' ),
			array( 'Beveiliging', '/beveiliging/' ),
		),
		'legal'   => array(
			array( 'Privacybeleid', '/privacybeleid/' ),
			array( 'Beveiliging', '/beveiliging/' ),
		),
	),
);

$theme       = get_option( 'stylesheet' );
$default     = function_exists( 'pll_default_language' ) ? pll_default_language() : 'en';
$assignments = (array) get_theme_mod( 'nav_menu_locations', array() );
$polylang    = function_exists( 'PLL' ) && isset( PLL()->options ) ? PLL()->options : null;
$nav_menus   = $polylang ? (array) $polylang['nav_menus'] : array();

/**
 * Add one menu item, returning its id.
 *
 * @param int    $menu_id Menu id.
 * @param string $base    Language home URL, no trailing slash.
 * @param array  $item    Title, path or URL, children, new tab.
 * @param int    $parent  Parent item id.
 */
function chargenet_seed_item( int $menu_id, string $base, array $item, int $parent = 0 ): int {
	$url = 0 === strpos( $item[1], '/' ) ? $base . $item[1] : $item[1];
	$id  = wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $item[0],
			'menu-item-url'       => $url,
			'menu-item-type'      => 'custom',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
			'menu-item-target'    => ! empty( $item[3] ) ? '_blank' : '',
		)
	);
	foreach ( $item[2] ?? array() as $child ) {
		chargenet_seed_item( $menu_id, $base, $child, (int) $id );
	}
	return (int) $id;
}

foreach ( $languages as $lang => $locations ) {
	$home = function_exists( 'pll_home_url' ) ? untrailingslashit( pll_home_url( $lang ) ) : untrailingslashit( home_url() );
	foreach ( $locations as $location => $items ) {
		$name   = ucfirst( $location ) . ' (' . $lang . ')';
		$object = wp_get_nav_menu_object( $name );
		$legacy = wp_get_nav_menu_object( ucfirst( $location ) ); // English menus from an earlier setup run.
		if ( ! $object && $legacy && 'en' === $lang ) {
			wp_update_nav_menu_object( $legacy->term_id, array( 'menu-name' => $name ) );
			$object = wp_get_nav_menu_object( $name );
		}
		if ( ! $object ) {
			$menu_id = wp_create_nav_menu( $name );
			foreach ( $items as $item ) {
				chargenet_seed_item( $menu_id, $home, $item );
			}
		} else {
			$menu_id = (int) $object->term_id;
		}
		$nav_menus[ $theme ][ $location ][ $lang ] = $menu_id;
		if ( $lang === $default ) {
			$assignments[ $location ] = $menu_id;
		}
	}
}

set_theme_mod( 'nav_menu_locations', $assignments );
if ( $polylang ) {
	PLL()->options['nav_menus'] = $nav_menus;
}
echo "Menus seeded (en, nl).\n";
