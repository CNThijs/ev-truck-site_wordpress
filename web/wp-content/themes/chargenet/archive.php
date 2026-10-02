<?php
/**
 * Category, tag, date and author archives.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<header class="archive-header">
	<?php the_archive_title( '<h1>', '</h1>' ); ?>
	<?php the_archive_description( '<div class="archive-header__description">', '</div>' ); ?>
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
