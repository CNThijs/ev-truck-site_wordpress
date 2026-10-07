<?php
/**
 * Search results for the whole site (news and pages in the language of the page), as news cards.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_enqueue_style( 'chargenet-post-grid-style' ); // The card styles live with the Post Grid section.

get_header();
echo do_blocks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
	'<!-- wp:chargenet/hero ' . wp_json_encode(
		array(
			'variant' => 'title-band',
			'heading' => __( 'Search', 'chargenet' ),
			/* translators: %s: search query. */
			'intro'   => sprintf( __( 'Results for “%s”', 'chargenet' ), get_search_query() ),
		)
	) . ' /-->' .
	'<!-- wp:chargenet/post-filter ' . wp_json_encode(
		array(
			'showCategories' => false,
			'spaceTop'       => 'md',
			'spaceBottom'    => 'none',
		)
	) . ' /-->'
);
?>
<section class="section is-light" data-space-top="sm">
	<div class="container stack">
		<?php if ( have_posts() ) : ?>
			<ul class="post-grid__list post-grid__list--search" role="list">
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part(
						'template-parts/post-card',
						null,
						array(
							'item'  => chargenet_post_card_item( get_post() ),
							'level' => 2,
						)
					);
				}
				?>
			</ul>
			<?php
			the_posts_pagination(
				array(
					'class'     => 'post-grid__pages',
					'prev_text' => __( 'Previous', 'chargenet' ),
					'next_text' => __( 'Next', 'chargenet' ),
				)
			);
			?>
		<?php else : ?>
			<p><?php esc_html_e( 'Nothing found. Try other words, or browse the news.', 'chargenet' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
