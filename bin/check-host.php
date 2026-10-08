<?php
/**
 * Facts about the server that decide the caching and image setup (Epic 12). On STRATO: upload this file next to
 * wp-config.php (or anywhere under the WordPress folder), run `wp eval-file check-host.php` over SSH, and send the output.
 * Prints server facts only, no secrets. Locally: `ddev wp eval-file bin/check-host.php`. Delete the uploaded copy afterwards.
 */

$cn_row = static function ( string $label, $value ): void {
	echo str_pad( $label, 34 ) . ( is_bool( $value ) ? ( $value ? 'yes' : 'no' ) : $value ) . "\n";
};

echo "PHP and limits\n";
$cn_row( 'PHP version', PHP_VERSION );
$cn_row( 'SAPI', PHP_SAPI );
$cn_row( 'memory_limit', (string) ini_get( 'memory_limit' ) );
$cn_row( 'WP_MEMORY_LIMIT / MAX', WP_MEMORY_LIMIT . ' / ' . WP_MAX_MEMORY_LIMIT );
$cn_row( 'max_execution_time (CLI may be 0)', (string) ini_get( 'max_execution_time' ) );
$cn_row( 'upload_max_filesize / post_max_size', ini_get( 'upload_max_filesize' ) . ' / ' . ini_get( 'post_max_size' ) );
$cn_row( 'OPcache enabled', function_exists( 'opcache_get_status' ) && ini_get( 'opcache.enable' ) );
$cn_row( 'APCu', function_exists( 'apcu_fetch' ) );
$cn_row( 'Persistent object cache', wp_using_ext_object_cache() );

echo "\nImages (WordPress image editor)\n";
$cn_row( 'Editor used', _wp_image_editor_choose() ?: 'none' );
$cn_row( 'GD / Imagick loaded', ( extension_loaded( 'gd' ) ? 'GD' : '-' ) . ' / ' . ( extension_loaded( 'imagick' ) ? 'Imagick' : '-' ) );
foreach ( array( 'image/webp', 'image/avif', 'image/jpeg', 'image/png' ) as $mime ) {
	$cn_row( "Can write $mime", (bool) wp_image_editor_supports( array( 'mime_type' => $mime ) ) );
}

echo "\nCron\n";
$cn_row( 'DISABLE_WP_CRON', defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON );
$cn_row( 'Next scheduled event', ( wp_next_scheduled( 'chargenet_forms_purge_daily' ) ? gmdate( 'Y-m-d H:i', (int) wp_next_scheduled( 'chargenet_forms_purge_daily' ) ) . ' UTC (forms purge)' : 'none yet' ) );

echo "\nWeb server\n";
$cn_row( 'Server software', (string) ( $_SERVER['SERVER_SOFTWARE'] ?? 'unknown (CLI)' ) );
$cn_row( '.htaccess in WordPress folder', file_exists( ABSPATH . '.htaccess' ) );
$cn_row( 'WP_ENVIRONMENT_TYPE', wp_get_environment_type() );
$cn_row( 'WP_CACHE constant', defined( 'WP_CACHE' ) && WP_CACHE );
$mu = glob( WP_CONTENT_DIR . '/{advanced-cache.php,object-cache.php}', GLOB_BRACE );
$cn_row( 'advanced/object cache drop-in', $mu ? implode( ', ', array_map( 'basename', $mu ) ) : 'none' );

echo "\nLive checks (after the new site is online; run from any computer)\n";
echo "  curl -sI -H 'Accept-Encoding: br, gzip' https://chargenet.energy/en/ | grep -i 'content-encoding\\|cache-control\\|server\\|x-\\|HTTP/'\n";
echo "  curl -sI -H 'Accept-Encoding: br, gzip' https://chargenet.energy/wp-content/themes/chargenet/assets/dist/assets/<main css file> | grep -i 'content-encoding\\|cache-control\\|expires'\n";
echo "  curl -sI https://chargenet.energy/wp-content/themes/chargenet/assets/fonts/source-sans-3-latin-wght-normal.woff2 | grep -i 'cache-control\\|expires'\n";
