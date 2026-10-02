<?php
/**
 * Single post.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/content', 'single' );
	the_post_navigation();
}

get_footer();
