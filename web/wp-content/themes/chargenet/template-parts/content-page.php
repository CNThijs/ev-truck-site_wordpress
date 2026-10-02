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
	<h1><?php the_title(); ?></h1>
	<?php the_content(); ?>
</article>
