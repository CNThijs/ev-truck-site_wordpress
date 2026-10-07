<?php
/**
 * Title: Key facts
 * Slug: chargenet/post-key-facts
 * Categories: chargenet-posts
 * Post Types: post
 * Description: A box with the most important facts of the article as a list.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!-- wp:group {"className":"is-style-key-facts","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-key-facts"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Key facts</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>First fact</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Second fact</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Third fact</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group -->
