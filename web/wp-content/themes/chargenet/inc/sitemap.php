<?php
/**
 * XML sitemaps, one per language: `/sitemap_index.xml` lists `/sitemap-en.xml` and `/sitemap-nl.xml`. Each file has the
 * indexable pages, news posts and category archives of that language with the real last modification date and the
 * hreflang alternates. Rank Math's sitemap module is off (it cannot split by language) and so are the core sitemaps.
 * Served on `init`, before routing, so no rewrite rules to flush.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wp_sitemaps_enabled', '__return_false' );

/**
 * Is a post allowed in the sitemap: published, no password, not set to noindex in Rank Math.
 *
 * @param WP_Post $post Post.
 */
function chargenet_sitemap_includes( WP_Post $post ): bool {
	$robots = get_post_meta( $post->ID, 'rank_math_robots', true );
	return '' === $post->post_password && ! ( is_array( $robots ) && in_array( 'noindex', $robots, true ) );
}

/**
 * The sitemap entries of one language: array of loc, lastmod (Y-m-d\TH:i:sP) and alternates (hreflang => URL).
 *
 * @param string $lang Language slug.
 * @return array<int,array<string,mixed>>
 */
function chargenet_sitemap_entries( string $lang ): array {
	$default = (string) pll_default_language( 'slug' );
	$entries = array();

	$posts = get_posts(
		array(
			'post_type'        => array( 'page', 'post' ),
			'post_status'      => 'publish',
			'lang'             => $lang,
			'posts_per_page'   => 1000, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- One file per language; paginate when a language passes 1,000 URLs.
			'orderby'          => 'modified',
			'order'            => 'DESC',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);
	foreach ( $posts as $post ) {
		if ( ! chargenet_sitemap_includes( $post ) ) {
			continue;
		}
		$alternates = array();
		foreach ( pll_get_post_translations( $post->ID ) as $code => $id ) {
			if ( 'publish' === get_post_status( $id ) ) {
				$alternates[ $code ] = (string) get_permalink( $id );
			}
		}
		$entries[] = array(
			'loc'        => (string) get_permalink( $post ),
			'lastmod'    => mysql2date( 'c', $post->post_modified_gmt . ' +0000', false ),
			'alternates' => $alternates,
			'default'    => $default,
		);
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'lang'       => $lang,
			'hide_empty' => true,
		)
	);
	foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
		$newest     = get_posts(
			array(
				'cat'              => $term->term_id,
				'lang'             => $lang,
				'posts_per_page'   => 1,
				'orderby'          => 'modified',
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);
		$alternates = array();
		foreach ( pll_get_term_translations( $term->term_id ) as $code => $id ) {
			$link = get_term_link( (int) $id, 'category' );
			if ( ! is_wp_error( $link ) ) {
				$alternates[ $code ] = $link;
			}
		}
		$entries[] = array(
			'loc'        => (string) get_term_link( $term ),
			'lastmod'    => $newest ? mysql2date( 'c', $newest[0]->post_modified_gmt . ' +0000', false ) : '',
			'alternates' => $alternates,
			'default'    => $default,
		);
	}
	return $entries;
}

/**
 * Serve /sitemap_index.xml and /sitemap-<lang>.xml.
 */
function chargenet_serve_sitemap(): void {
	if ( ! isset( $_SERVER['REQUEST_URI'] ) || ! function_exists( 'pll_languages_list' ) ) {
		return;
	}
	$path = trim( (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ), '/' );
	if ( 'sitemap_index.xml' !== $path && ! preg_match( '#^sitemap-([a-z]{2,3})\.xml$#', $path, $m ) ) {
		return;
	}
	$languages = pll_languages_list();
	if ( isset( $m[1] ) && ! in_array( $m[1], $languages, true ) ) {
		return;
	}

	status_header( 200 );
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex, follow' );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

	if ( ! isset( $m[1] ) ) {
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( $languages as $lang ) {
			$lastmods = array_filter( wp_list_pluck( chargenet_sitemap_entries( $lang ), 'lastmod' ) );
			echo "\t<sitemap><loc>" . esc_url( home_url( "/sitemap-{$lang}.xml" ) ) . '</loc>';
			echo $lastmods ? '<lastmod>' . esc_html( max( $lastmods ) ) . '</lastmod>' : '';
			echo "</sitemap>\n";
		}
		echo "</sitemapindex>\n";
		exit;
	}

	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
	foreach ( chargenet_sitemap_entries( $m[1] ) as $entry ) {
		echo "\t<url><loc>" . esc_url( $entry['loc'] ) . '</loc>';
		echo $entry['lastmod'] ? '<lastmod>' . esc_html( $entry['lastmod'] ) . '</lastmod>' : '';
		foreach ( $entry['alternates'] as $code => $url ) {
			echo '<xhtml:link rel="alternate" hreflang="' . esc_attr( $code ) . '" href="' . esc_url( $url ) . '"/>';
		}
		if ( isset( $entry['alternates'][ $entry['default'] ] ) ) {
			echo '<xhtml:link rel="alternate" hreflang="x-default" href="' . esc_url( $entry['alternates'][ $entry['default'] ] ) . '"/>';
		}
		echo "</url>\n";
	}
	echo "</urlset>\n";
	exit;
}
add_action( 'init', 'chargenet_serve_sitemap', 30 );
