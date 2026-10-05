<?php
/**
 * Page body.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class(); ?>>
	<?php if ( ! has_block( 'chargenet/hero' ) ) : // The Hero section carries the page's one h1. ?>
		<h1><?php the_title(); ?></h1>
	<?php endif; ?>
	<?php the_content(); ?>
</article>
