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
require_once CHARGENET_DIR . '/inc/redirects.php';
require_once CHARGENET_DIR . '/inc/forms.php';
require_once CHARGENET_DIR . '/inc/locations.php';
require_once CHARGENET_DIR . '/inc/style-guide.php';
require_once CHARGENET_DIR . '/inc/image.php';
require_once CHARGENET_DIR . '/inc/icons.php';
require_once CHARGENET_DIR . '/inc/post-grid.php';
require_once CHARGENET_DIR . '/inc/post-source.php';
require_once CHARGENET_DIR . '/inc/blocks.php';
require_once CHARGENET_DIR . '/inc/gallery.php';
require_once CHARGENET_DIR . '/inc/motion-lab.php';
