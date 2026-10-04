<?php
/**
 * ChargeNet theme bootstrap. Keep this file to requires only; logic lives in /inc.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CHARGENET_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'CHARGENET_DIR', get_template_directory() );
define( 'CHARGENET_URI', get_template_directory_uri() );

require_once CHARGENET_DIR . '/inc/setup.php';
require_once CHARGENET_DIR . '/inc/assets.php';
require_once CHARGENET_DIR . '/inc/fonts.php';
require_once CHARGENET_DIR . '/inc/head.php';
require_once CHARGENET_DIR . '/inc/nav.php';
require_once CHARGENET_DIR . '/inc/hreflang.php';
require_once CHARGENET_DIR . '/inc/style-guide.php';
require_once CHARGENET_DIR . '/inc/blocks.php';
