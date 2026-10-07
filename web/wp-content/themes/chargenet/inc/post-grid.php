<?php
/**
 * Data for the Post Grid section: recent posts as a plain array, so the markup does not depend on WP_Post
 * (the Section Gallery swaps in sample data through the chargenet_post_grid_items filter).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Posts for a Post Grid section: newest first, in the current language (Polylang filters the query),
 * optionally limited to one category.
 *
 * With the "paginate" attribute (the News page) the query follows the page being viewed, and the number of pages
 * is returned through $total_pages.
 *
 * @param array<string, mixed> $attributes  Block attributes (count, categoryId, paginate, exclude).
 * @param int                  $total_pages Number of result pages (set when paginating).
 * @return array<int, array{title: string, url: string, date: string, date_label: string, category: string, excerpt: string, image_id: int, source_url: string, source_host: string}>
 */
function chargenet_post_grid_items( array $attributes, int &$total_pages = 1 ): array {
	$count    = max( 1, min( 12, (int) ( $attributes['count'] ?? 3 ) ) );
	$category = (int) ( $attributes['categoryId'] ?? 0 );
	$paginate = ! empty( $attributes['paginate'] );
	$exclude  = (int) ( $attributes['exclude'] ?? 0 );

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'cat'                 => $category > 0 ? $category : 0,
			'post__not_in'        => $exclude > 0 ? array( $exclude ) : array(),
			'ignore_sticky_posts' => true,
			'paged'               => $paginate ? max( 1, (int) get_query_var( 'paged' ) ) : 1,
			'no_found_rows'       => ! $paginate,
		)
	);

	$total_pages = $paginate ? (int) $query->max_num_pages : 1;

	$items = array_map( 'chargenet_post_card_item', $query->posts );

	/**
	 * Filters the Post Grid items.
	 *
	 * @param array<int, array<string, mixed>> $items      Items.
	 * @param array<string, mixed>             $attributes Block attributes.
	 */
	return (array) apply_filters( 'chargenet_post_grid_items', $items, $attributes );
}

/**
 * One post (or page, in search results) as the plain array the card template prints.
 *
 * @param WP_Post $post Post.
 * @return array{title: string, url: string, date: string, date_label: string, category: string, excerpt: string, image_id: int, source_url: string, source_host: string}
 */
function chargenet_post_card_item( WP_Post $post ): array {
	$is_post = 'post' === $post->post_type;
	$terms   = $is_post ? get_the_category( $post->ID ) : array();
	$source  = $is_post ? chargenet_post_source( $post->ID ) : null;
	return array(
		'title'       => get_the_title( $post ),
		'url'         => (string) get_permalink( $post ),
		'date'        => $is_post ? get_the_date( 'c', $post ) : '',
		'date_label'  => $is_post ? get_the_date( '', $post ) : '',
		'category'    => $terms ? $terms[0]->name : '',
		'excerpt'     => wp_trim_words( get_the_excerpt( $post ), 24 ),
		'image_id'    => (int) get_post_thumbnail_id( $post ),
		'source_url'  => $source ? $source['url'] : '',
		'source_host' => $source ? $source['host'] : '',
	);
}
