<?php
/**
 * 404 page.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<section class="not-found">
	<h1><?php esc_html_e( 'Page not found', 'chargenet' ); ?></h1>
	<p><?php esc_html_e( 'The page you are looking for does not exist.', 'chargenet' ); ?></p>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'chargenet' ); ?></a>
	<?php get_search_form(); ?>
</section>
<?php
get_footer();
