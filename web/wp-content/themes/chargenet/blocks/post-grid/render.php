<?php
/**
 * Post Grid. Variables: $attributes. Server-rendered from recent posts; the editor previews this same output.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_pages = 1;
$chargenet_items = chargenet_post_grid_items( $attributes, $chargenet_pages );

// Empty state: no posts yet, nothing to show.
if ( ! $chargenet_items ) {
	return;
}

$chargenet_variant  = 'featured-grid' === ( $attributes['variant'] ?? '' ) ? 'featured-grid' : 'latest';
$chargenet_title_id = chargenet_section_title_id( $attributes );
$chargenet_level    = min( 4, (int) ( $attributes['headingLevel'] ?? 2 ) + 1 );
$chargenet_label    = trim( wp_strip_all_tags( (string) ( $attributes['linkLabel'] ?? '' ) ) );
$chargenet_more_url = (string) ( $attributes['linkUrl'] ?? '' );
if ( '' === $chargenet_more_url && '' !== $chargenet_label ) {
	$chargenet_blog_page = (int) get_option( 'page_for_posts' ); // Polylang returns the current language's page.
	$chargenet_more_url  = $chargenet_blog_page ? (string) get_permalink( $chargenet_blog_page ) : '';
}

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'post-grid',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="post-grid post-grid--<?php echo esc_attr( $chargenet_variant ); ?> stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<ul class="post-grid__list" role="list" data-reveal-group>
		<?php foreach ( $chargenet_items as $chargenet_item ) : ?>
			<?php
			get_template_part(
				'template-parts/post-card',
				null,
				array(
					'item'  => $chargenet_item,
					'level' => $chargenet_level,
				)
			);
			?>
		<?php endforeach; ?>
	</ul>
	<?php if ( $chargenet_pages > 1 ) : ?>
		<nav class="post-grid__pages" aria-label="<?php esc_attr_e( 'Pages', 'chargenet' ); ?>">
			<?php
			echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
				array(
					'total'     => $chargenet_pages,
					'current'   => max( 1, (int) get_query_var( 'paged' ) ),
					'prev_text' => esc_html__( 'Previous', 'chargenet' ),
					'next_text' => esc_html__( 'Next', 'chargenet' ),
				)
			);
			?>
		</nav>
	<?php endif; ?>
	<?php if ( '' !== $chargenet_label && '' !== $chargenet_more_url ) : ?>
		<p class="post-grid__more"><a class="btn btn--secondary" href="<?php echo esc_url( $chargenet_more_url ); ?>"><?php echo esc_html( $chargenet_label ); ?></a></p>
	<?php endif; ?>
</div>
<?php
chargenet_section_close();
