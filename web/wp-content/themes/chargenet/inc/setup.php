<?php
/**
 * Theme supports and menus.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and nav menus.
 */
function chargenet_setup(): void {
	// Explicit path: just-in-time loading did not find the file under Polylang.
	load_textdomain( 'chargenet', CHARGENET_DIR . '/languages/chargenet-' . determine_locale() . '.mo' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'editor-styles' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'chargenet' ),
			'utility' => __( 'Header utility menu (Login)', 'chargenet' ),
			'footer'  => __( 'Footer menu', 'chargenet' ),
			'legal'   => __( 'Footer legal menu', 'chargenet' ),
		)
	);
}
add_action( 'after_setup_theme', 'chargenet_setup' );
