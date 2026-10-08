<?php
/**
 * News category: title band with the category name and description, the news filter and the posts with paging.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_term = get_queried_object();

get_header();
echo do_blocks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
	'<!-- wp:chargenet/hero ' . wp_json_encode(
		array(
			'variant' => 'title-band',
			'eyebrow' => __( 'News', 'chargenet' ),
			'heading' => single_cat_title( '', false ),
			'intro'   => wp_strip_all_tags( term_description() ),
		)
	) . ' /-->' .
	'<!-- wp:chargenet/post-filter ' . wp_json_encode(
		array(
			'spaceTop'    => 'md',
			'spaceBottom' => 'none',
		)
	) . ' /-->' .
	'<!-- wp:chargenet/post-grid ' . wp_json_encode(
		array(
			'count'        => 9,
			'headingLevel' => 1, // The cards are the page's h2 headings under the title band's h1.
			'paginate'     => true,
			'categoryId'   => $chargenet_term instanceof WP_Term ? (int) $chargenet_term->term_id : 0,
			'spaceTop'     => 'sm',
		)
	) . ' /-->'
);
get_footer();
