<?php
/**
 * Blog index (the News page). Its sections are the content of the page chosen as the posts page, so editors change
 * the heading and layout in the editor; without such a page the plain list in index.php is used.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_page = (int) get_option( 'page_for_posts' ); // Polylang returns the current language's page.
if ( ! $chargenet_page ) {
	require __DIR__ . '/index.php';
	return;
}

get_header();
echo do_blocks( (string) get_post_field( 'post_content', $chargenet_page ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
get_footer();
