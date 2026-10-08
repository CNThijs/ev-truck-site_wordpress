<?php
/**
 * Cache Enabler (free) settings for production. Not run locally (the plugin stays inactive in DDEV so development is
 * never served from a cache). On the server, after activating the plugin: `wp eval-file bin/setup-cache.php`; safe to
 * run again. Decisions and the .htaccess rules: docs/performance.md and docs/htaccess.md.
 *
 * - Cache lifetime 10 hours: the forms on a cached page carry a WordPress nonce, valid for 12 to 24 hours.
 * - The cache is cleared when a post or page is saved or a term changes (the home page shows the latest news and the
 *   menus), and when a plugin changes.
 * - Logged-in visitors, search results, feeds, previews and URLs with a query string are never cached (utm_*, gclid,
 *   fbclid and similar are ignored by the plugin: the page is the same, the form script reads the parameters).
 * - No mobile cache (the HTML is the same), no HTML minification (the gain is small and inline scripts must stay as
 *   written), no WebP URL conversion (images are WebP/AVIF already), no plugin-side compression (the server does it).
 */

if ( ! class_exists( 'Cache_Enabler' ) ) {
	WP_CLI::error( 'Cache Enabler is not active.' );
}

update_option(
	'cache_enabler',
	array(
		'cache_expires'                      => 1,
		'cache_expiry_time'                  => 10,
		'clear_site_cache_on_saved_post'     => 1,
		'clear_site_cache_on_saved_comment'  => 0,
		'clear_site_cache_on_saved_term'     => 1,
		'clear_site_cache_on_saved_user'     => 0,
		'clear_site_cache_on_changed_plugin' => 1,
		'convert_image_urls_to_webp'         => 0,
		'mobile_cache'                       => 0,
		'compress_cache'                     => 0,
		'minify_html'                        => 0,
		'minify_inline_css_js'               => 0,
		'excluded_post_ids'                  => '',
		'excluded_page_paths'                => '',
		'excluded_query_strings'             => '',
		'excluded_cookies'                   => '',
	)
);

if ( ! defined( 'WP_CACHE' ) || ! WP_CACHE ) {
	WP_CLI::warning( "Add define( 'WP_CACHE', true ); to wp-config.php (above the 'stop editing' line); the plugin cannot do it on every host." );
}
WP_CLI::success( 'Cache Enabler configured.' );
