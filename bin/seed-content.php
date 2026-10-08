<?php
/**
 * Builds the site content: media, pages, news posts (English and Dutch, linked as Polylang translations), the front page and the
 * menus. Run: `ddev wp eval-file bin/seed-content.php` (bin/setup-wp.sh does it on `ddev start`), or over SSH on a
 * fresh install after bin/setup-polylang.php. Add `force` to overwrite pages that were edited since seeding.
 *
 * Idempotent. Pages are identified by the meta key _chargenet_seed_key. A seeded page that was edited in the
 * editor is left alone (its content hash no longer matches) unless `force` is given. Menus are rebuilt every run.
 *
 * Content lives in content/ (lib.php helpers, media.php, menus.php, pages/*.php), media files in content/media/.
 */

if ( ! function_exists( 'PLL' ) ) {
	WP_CLI::error( 'Polylang is not active. Run bin/setup-polylang.php first.' );
}

$GLOBALS['chargenet_root']  = dirname( __DIR__ ); // Globals, because wp eval-file may run this file inside a function scope.
$GLOBALS['chargenet_langs'] = array( 'en', 'nl' );
$chargenet_root             = $GLOBALS['chargenet_root'];
// Run as the first administrator, so WordPress does not filter block markup as for an anonymous user.
wp_set_current_user( 1 );
$chargenet_force = in_array( 'force', isset( $args ) ? (array) $args : array(), true );

require_once $chargenet_root . '/content/lib.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$chargenet_langs = $GLOBALS['chargenet_langs'];

// SVG badges: allowed for this run only.
add_filter(
	'upload_mimes',
	static function ( array $mimes ): array {
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
	}
);
add_filter(
	'wp_check_filetype_and_ext',
	static function ( array $data, string $file, string $filename ): array {
		if ( 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
			return array(
				'ext'             => 'svg',
				'type'            => 'image/svg+xml',
				'proper_filename' => false,
			);
		}
		return $data;
	},
	10,
	3
);

/**
 * Post of a seed key in a language, or 0.
 *
 * @param string $meta Meta key.
 * @param string $key  Seed key.
 * @param string $lang Language slug.
 */
function chargenet_seed_find( string $meta, string $key, string $lang ): int {
	$posts = get_posts(
		array(
			'post_type'   => array( 'page', 'post', 'attachment' ),
			'post_status' => 'any',
			'meta_key'    => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'  => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'numberposts' => -1,
			'fields'      => 'ids',
		)
	);
	foreach ( $posts as $id ) {
		if ( pll_get_post_language( $id ) === $lang ) {
			return (int) $id;
		}
	}
	return 0;
}

/**
 * Attachment id of a media key for a language (one copy per language, linked as translations); 0 if the file is missing.
 *
 * @param string $key  Media key from content/media.php.
 * @param string $lang Language slug.
 */
function chargenet_seed_media( string $key, string $lang ): int {
	static $manifest = null;
	static $ids      = array();
	global $chargenet_root, $chargenet_langs;

	if ( isset( $ids[ $key ][ $lang ] ) ) {
		return $ids[ $key ][ $lang ];
	}
	$manifest = $manifest ?? require $chargenet_root . '/content/media.php';
	$item     = $manifest[ $key ] ?? null;
	$source   = $item ? $chargenet_root . '/content/media/' . $item['file'] : '';
	if ( ! $item || ! is_readable( $source ) ) {
		return 0;
	}

	$group = array();
	foreach ( $chargenet_langs as $code ) {
		$id = chargenet_seed_find( '_chargenet_seed_media', $key, $code );
		if ( ! $id ) {
			$tmp = wp_tempnam( $item['file'] );
			copy( $source, $tmp );
			$id = media_handle_sideload(
				array(
					'name'     => $item['file'],
					'tmp_name' => $tmp,
				),
				0,
				$item['title']
			);
			if ( is_wp_error( $id ) ) {
				WP_CLI::warning( "Media {$key}: " . $id->get_error_message() );
				continue;
			}
			pll_set_post_language( $id, $code );
			update_post_meta( $id, '_chargenet_seed_media', $key );
		}
		update_post_meta( $id, '_wp_attachment_image_alt', $item['alt'][ $code ] ?? '' );
		wp_update_post(
			array(
				'ID'         => $id,
				'post_title' => $item['title'],
			)
		);
		$group[ $code ]     = (int) $id;
		$ids[ $key ][ $code ] = (int) $id;
	}
	if ( count( $group ) > 1 ) {
		pll_save_post_translations( $group );
	}
	return $ids[ $key ][ $lang ] ?? 0;
}

/**
 * Give a page its slug even when another language already uses it (WP-CLI does not know Polylang's per-language slugs).
 *
 * @param int    $id   Page id.
 * @param string $slug Slug.
 */
function chargenet_seed_set_slug( int $id, string $slug ): void {
	global $wpdb;
	if ( get_post_field( 'post_name', $id ) !== $slug ) {
		$wpdb->update( $wpdb->posts, array( 'post_name' => $slug ), array( 'ID' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		clean_post_cache( $id );
	}
}

// Pages: pass 1 creates them, pass 2 fills in links between pages.
$chargenet_defs  = array();
foreach ( require $chargenet_root . '/content/pages/index.php' as $chargenet_name ) {
	$chargenet_defs[] = require $chargenet_root . '/content/pages/' . $chargenet_name . '.php';
}
$chargenet_pages = array(); // key => lang => id.
$chargenet_seo   = require $chargenet_root . '/content/seo.php';

$chargenet_link = static function ( string $key, string $lang ) use ( &$chargenet_pages ): string {
	$id = $chargenet_pages[ $key ][ $lang ] ?? 0;
	return $id ? wp_make_link_relative( (string) get_permalink( $id ) ) : '';
};

foreach ( $chargenet_defs as $chargenet_def ) {
	// Polylang cannot tell two pages of one slug apart in the main query (the Dutch URL redirects to English).
	if ( 'home' !== $chargenet_def['key'] && count( array_unique( $chargenet_def['slugs'] ) ) < count( $chargenet_def['slugs'] ) ) {
		WP_CLI::error( "Page {$chargenet_def['key']}: every language needs its own slug." );
	}
}

foreach ( array( 1, 2 ) as $chargenet_pass ) {
	foreach ( $chargenet_defs as $chargenet_def ) {
		$chargenet_key = $chargenet_def['key'];
		foreach ( $chargenet_langs as $chargenet_lang ) {
			$content = ( $chargenet_def['build'] )(
				$chargenet_lang,
				static fn( string $media ): int => chargenet_seed_media( $media, $chargenet_lang ),
				static fn( string $other ): string => $chargenet_link( $other, $chargenet_lang )
			);
			$hash    = md5( $content );
			$id      = chargenet_seed_find( '_chargenet_seed_key', $chargenet_key, $chargenet_lang );

			// The Home pages of an earlier setup run have no seed key: adopt them.
			if ( ! $id && 'home' === $chargenet_key && get_option( 'page_on_front' ) ) {
				$id = (int) pll_get_post( (int) get_option( 'page_on_front' ), $chargenet_lang );
			}

			if ( ! $id ) {
				$id = (int) wp_insert_post(
					array(
						'post_type'    => 'page',
						'post_status'  => 'publish',
						'post_title'   => $chargenet_def['titles'][ $chargenet_lang ],
						'post_name'    => $chargenet_def['slugs'][ $chargenet_lang ],
						'post_content' => wp_slash( $content ),
					)
				);
				pll_set_post_language( $id, $chargenet_lang );
				echo "Created {$chargenet_key} ({$chargenet_lang})\n";
			} else {
				$stored = (string) get_post_meta( $id, '_chargenet_seed_hash', true );
				$edited = '' !== $stored && md5( (string) get_post_field( 'post_content', $id ) ) !== $stored;
				if ( $edited && ! $chargenet_force ) {
					WP_CLI::warning( "{$chargenet_key} ({$chargenet_lang}) was edited since seeding: left alone (use force to overwrite)." );
					$chargenet_pages[ $chargenet_key ][ $chargenet_lang ] = $id;
					continue;
				}
				if ( 1 === $chargenet_pass || md5( (string) get_post_field( 'post_content', $id ) ) !== $hash ) {
					wp_update_post(
						array(
							'ID'           => $id,
							'post_title'   => $chargenet_def['titles'][ $chargenet_lang ],
							'post_content' => wp_slash( $content ),
						)
					);
				}
			}
			chargenet_seed_set_slug( $id, $chargenet_def['slugs'][ $chargenet_lang ] );
			update_post_meta( $id, '_chargenet_seed_key', $chargenet_key );
			update_post_meta( $id, '_chargenet_seed_hash', md5( (string) get_post_field( 'post_content', $id ) ) );

			// SEO title and description (content/seo.php): an editor's own value in Rank Math wins.
			$seo = $chargenet_seo[ $chargenet_key ][ $chargenet_lang ] ?? null;
			if ( $seo ) {
				$seo_fields = array(
					'rank_math_title'       => 'home' === $chargenet_key ? $seo['title'] : $seo['title'] . ' %sep% %sitename%',
					'rank_math_description' => $seo['description'],
				);
				foreach ( $seo_fields as $seo_meta => $seo_value ) {
					$seo_current = (string) get_post_meta( $id, $seo_meta, true );
					if ( '' === $seo_current || get_post_meta( $id, '_chargenet_seed_' . $seo_meta, true ) === $seo_current || $chargenet_force ) {
						update_post_meta( $id, $seo_meta, $seo_value );
						update_post_meta( $id, '_chargenet_seed_' . $seo_meta, $seo_value );
					}
				}
			}
			$chargenet_pages[ $chargenet_key ][ $chargenet_lang ] = $id;
		}
		pll_save_post_translations( $chargenet_pages[ $chargenet_key ] );
	}
}

// Front page: the English Home page (Polylang serves its translation at /nl/).
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $chargenet_pages['home']['en'] );
if ( isset( $chargenet_pages['blog']['en'] ) ) {
	update_option( 'page_for_posts', $chargenet_pages['blog']['en'] ); // The News page; Polylang serves the Dutch one.
}

// Downloads: files kept out of git (content/downloads/) are copied to uploads/chargenet-downloads/, where the theme
// serves them at /downloads/<file>. Missing files are skipped.
foreach ( glob( $chargenet_root . '/content/downloads/*.pdf' ) ?: array() as $chargenet_file ) {
	$chargenet_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'chargenet-downloads';
	wp_mkdir_p( $chargenet_dir );
	copy( $chargenet_file, $chargenet_dir . '/' . basename( $chargenet_file ) );
}

// News posts (the old site's 18 posts, both languages).
require_once $chargenet_root . '/content/blog.php';
chargenet_seed_blog( $chargenet_langs, $chargenet_force );

// Menus: rebuilt every run from content/menus.php.
$chargenet_menus = require $chargenet_root . '/content/menus.php';
$chargenet_theme = get_option( 'stylesheet' );
$chargenet_locs  = (array) get_theme_mod( 'nav_menu_locations', array() );
$chargenet_opts  = isset( PLL()->options ) ? (array) PLL()->options['nav_menus'] : array();
$chargenet_dflt  = pll_default_language();

/**
 * Add menu items, skipping pages that do not exist yet and parents without children.
 *
 * @param int                              $menu_id Menu id.
 * @param array<int, array<string, mixed>> $items   Items.
 * @param string                           $lang    Language.
 * @param array<string, array<string,int>> $pages   Seeded pages: key => lang => id.
 * @param int                              $parent  Parent item id.
 * @return int Number of items added.
 */
function chargenet_seed_menu_items( int $menu_id, array $items, string $lang, array $pages, int $parent = 0 ): int {
	$added = 0;
	foreach ( $items as $item ) {
		$args = array(
			'menu-item-title'     => $item['label'],
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
			'menu-item-target'    => ! empty( $item['new_tab'] ) ? '_blank' : '',
		);
		if ( isset( $item['children'] ) ) {
			$id = (int) wp_update_nav_menu_item(
				$menu_id,
				0,
				array_merge(
					$args,
					array(
						'menu-item-type' => 'custom',
						'menu-item-url'  => '#',
					)
				)
			);
			if ( 0 === chargenet_seed_menu_items( $menu_id, $item['children'], $lang, $pages, $id ) ) {
				wp_delete_post( $id, true );
				continue;
			}
			++$added;
			continue;
		}
		if ( isset( $item['url'] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array_merge(
					$args,
					array(
						'menu-item-type' => 'custom',
						'menu-item-url'  => $item['url'],
					)
				)
			);
			++$added;
			continue;
		}
		$page = $pages[ $item['key'] ][ $lang ] ?? 0;
		if ( $page ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array_merge(
					$args,
					array(
						'menu-item-type'      => 'post_type',
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page,
					)
				)
			);
			++$added;
		}
	}
	return $added;
}

foreach ( $chargenet_menus as $chargenet_lang => $chargenet_locations ) {
	foreach ( $chargenet_locations as $chargenet_location => $chargenet_items ) {
		$name   = ucfirst( $chargenet_location ) . ' (' . $chargenet_lang . ')';
		$object = wp_get_nav_menu_object( $name );
		if ( $object ) {
			wp_delete_nav_menu( $object->term_id );
		}
		$menu_id = (int) wp_create_nav_menu( $name );
		chargenet_seed_menu_items( $menu_id, $chargenet_items, $chargenet_lang, $chargenet_pages );
		$chargenet_opts[ $chargenet_theme ][ $chargenet_location ][ $chargenet_lang ] = $menu_id;
		if ( $chargenet_lang === $chargenet_dflt ) {
			$chargenet_locs[ $chargenet_location ] = $menu_id;
		}
	}
}
set_theme_mod( 'nav_menu_locations', $chargenet_locs );
if ( isset( PLL()->options ) ) {
	PLL()->options['nav_menus'] = $chargenet_opts;
}
flush_rewrite_rules( false );

echo 'Content seeded: ' . count( $chargenet_defs ) . " pages x 2 languages, menus rebuilt.\n";
