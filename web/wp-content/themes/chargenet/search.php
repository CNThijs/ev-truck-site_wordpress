<?php
/**
 * Search results.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<header class="archive-header">
	<h1>
		<?php
		/* translators: %s: search query. */
		printf( esc_html__( 'Search results for: %s', 'chargenet' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
		?>
	</h1>
</header>
<?php
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content', 'summary' );
	}
	the_posts_pagination();
} else {
	get_template_part( 'template-parts/content', 'none' );
}

get_footer();
