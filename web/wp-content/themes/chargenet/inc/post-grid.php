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
 * @param array<string, mixed> $attributes Block attributes (count, categoryId).
 * @return array<int, array{title: string, url: string, date: string, date_label: string, category: string, excerpt: string, image_id: int, source_url: string, source_host: string}>
 */
function chargenet_post_grid_items( array $attributes ): array {
	$count    = max( 1, min( 12, (int) ( $attributes['count'] ?? 3 ) ) );
	$category = (int) ( $attributes['categoryId'] ?? 0 );

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'cat'                 => $category > 0 ? $category : 0,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	$items = array();
	foreach ( $query->posts as $post ) {
		$terms   = get_the_category( $post->ID );
		$source  = chargenet_post_source( $post->ID );
		$items[] = array(
			'title'       => get_the_title( $post ),
			'url'         => get_permalink( $post ),
			'date'        => get_the_date( 'c', $post ),
			'date_label'  => get_the_date( '', $post ),
			'category'    => $terms ? $terms[0]->name : '',
			'excerpt'     => wp_trim_words( get_the_excerpt( $post ), 24 ),
			'image_id'    => (int) get_post_thumbnail_id( $post ),
			'source_url'  => $source ? $source['url'] : '',
			'source_host' => $source ? $source['host'] : '',
		);
	}

	/**
	 * Filters the Post Grid items.
	 *
	 * @param array<int, array<string, mixed>> $items      Items.
	 * @param array<string, mixed>             $attributes Block attributes.
	 */
	return (array) apply_filters( 'chargenet_post_grid_items', $items, $attributes );
}
