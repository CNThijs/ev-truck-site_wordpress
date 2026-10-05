<?php
/**
 * Single post body.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article <?php post_class(); ?>>
	<h1><?php the_title(); ?></h1>
	<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
	<?php the_content(); ?>
	<?php chargenet_the_post_source(); ?>
</article>
