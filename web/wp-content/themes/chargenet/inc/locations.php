<?php
/**
 * Charging locations: an editable, translatable content type (no public page of its own) that the Locations List
 * section prints as an accessible list. Static content, edited by hand: there is no live feed from the platform.
 * Each language has its own entry (Polylang), linked as translations.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_LOCATION = 'chargenet_location';

/**
 * Register the post type.
 */
function chargenet_register_location_type(): void {
	register_post_type(
		CHARGENET_LOCATION,
		array(
			'labels'              => array(
				'name'          => __( 'Locations', 'chargenet' ),
				'singular_name' => __( 'Location', 'chargenet' ),
				'add_new_item'  => __( 'Add location', 'chargenet' ),
				'edit_item'     => __( 'Edit location', 'chargenet' ),
				'not_found'     => __( 'No locations yet.', 'chargenet' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true, // The block editor needs it; the posts are only readable by editors.
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-location',
			'menu_position'       => 21,
			'supports'            => array( 'title', 'page-attributes' ),
			'capability_type'     => 'post',
			'rewrite'             => false,
			'has_archive'         => false,
		)
	);
}
add_action( 'init', 'chargenet_register_location_type' );

/**
 * Make the post type translatable in Polylang.
 *
 * @param string[] $types Translatable post types.
 * @return string[]
 */
function chargenet_location_translatable( array $types ): array {
	$types[ CHARGENET_LOCATION ] = CHARGENET_LOCATION;
	return $types;
}
add_filter( 'pll_get_post_types', 'chargenet_location_translatable' );

/**
 * Fields of a location.
 *
 * @return array<string, string> Meta key => label.
 */
function chargenet_location_fields(): array {
	return array(
		'_cn_loc_street'  => __( 'Street and number', 'chargenet' ),
		'_cn_loc_postal'  => __( 'Postal code', 'chargenet' ),
		'_cn_loc_city'    => __( 'City', 'chargenet' ),
		'_cn_loc_access'  => __( 'Access (for example private, semi-public, public)', 'chargenet' ),
		'_cn_loc_details' => __( 'Charge points (for example 4 × 150 kW CCS2)', 'chargenet' ),
	);
}

/**
 * Box with the fields.
 */
function chargenet_location_metabox(): void {
	add_meta_box( 'chargenet-location', __( 'Location details', 'chargenet' ), 'chargenet_location_metabox_render', CHARGENET_LOCATION, 'normal', 'high' );
}
add_action( 'add_meta_boxes_' . CHARGENET_LOCATION, 'chargenet_location_metabox' );

/**
 * Print the fields.
 *
 * @param WP_Post $post Location.
 */
function chargenet_location_metabox_render( WP_Post $post ): void {
	wp_nonce_field( 'chargenet_location', 'chargenet_location_nonce' );
	echo '<table class="form-table" role="presentation">';
	foreach ( chargenet_location_fields() as $key => $label ) {
		printf(
			'<tr><th scope="row"><label for="%1$s">%2$s</label></th><td><input class="regular-text" type="text" id="%1$s" name="%1$s" value="%3$s"></td></tr>',
			esc_attr( $key ),
			esc_html( $label ),
			esc_attr( (string) get_post_meta( $post->ID, $key, true ) )
		);
	}
	echo '</table>';
}

/**
 * Save the fields.
 *
 * @param int $post_id Location id.
 */
function chargenet_location_save( int $post_id ): void {
	$nonce = isset( $_POST['chargenet_location_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['chargenet_location_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'chargenet_location' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( array_keys( chargenet_location_fields() ) as $key ) {
		update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '' );
	}
}
add_action( 'save_post_' . CHARGENET_LOCATION, 'chargenet_location_save' );

/**
 * Locations in the current language as plain arrays (Polylang filters the query by language).
 *
 * @param int $count Maximum number.
 * @return array<int, array{name: string, street: string, postal: string, city: string, access: string, details: string}>
 */
function chargenet_locations_items( int $count = 50 ): array {
	$items = array();
	foreach ( get_posts(
		array(
			'post_type'      => CHARGENET_LOCATION,
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, min( 200, $count ) ),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
		)
	) as $post ) {
		$items[] = array(
			'name'    => get_the_title( $post ),
			'street'  => (string) get_post_meta( $post->ID, '_cn_loc_street', true ),
			'postal'  => (string) get_post_meta( $post->ID, '_cn_loc_postal', true ),
			'city'    => (string) get_post_meta( $post->ID, '_cn_loc_city', true ),
			'access'  => (string) get_post_meta( $post->ID, '_cn_loc_access', true ),
			'details' => (string) get_post_meta( $post->ID, '_cn_loc_details', true ),
		);
	}
	return $items;
}
