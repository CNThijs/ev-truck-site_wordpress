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
			$chargenet_image = chargenet_image(
				(int) $chargenet_item['image_id'],
				array(
					'size'       => 'large',
					'sizes'      => '(min-width: 64rem) 33vw, (min-width: 40rem) 50vw, 100vw',
					'class'      => 'post-card__img',
					'decorative' => true,
					'priority'   => false,
				)
			);
			?>
			<li class="post-card" data-reveal>
				<figure class="post-card__media">
					<?php echo $chargenet_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?>
				</figure>
				<div class="post-card__body">
					<?php if ( '' !== $chargenet_item['category'] || '' !== $chargenet_item['date_label'] ) : ?>
						<p class="post-card__meta">
							<?php if ( '' !== $chargenet_item['category'] ) : ?>
								<span class="badge"><?php echo esc_html( $chargenet_item['category'] ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $chargenet_item['date_label'] ) : ?>
								<time datetime="<?php echo esc_attr( $chargenet_item['date'] ); ?>"><?php echo esc_html( $chargenet_item['date_label'] ); ?></time>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<h<?php echo (int) $chargenet_level; ?> class="post-card__title">
						<a class="post-card__link" href="<?php echo esc_url( $chargenet_item['url'] ); ?>"><?php echo esc_html( $chargenet_item['title'] ); ?></a>
					</h<?php echo (int) $chargenet_level; ?>>
					<?php if ( '' !== $chargenet_item['excerpt'] ) : ?>
						<p class="post-card__excerpt"><?php echo esc_html( $chargenet_item['excerpt'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $chargenet_item['source_url'] ) ) : ?>
						<p class="post-card__source">
							<a class="link-arrow" href="<?php echo esc_url( $chargenet_item['source_url'] ); ?>" target="_blank" rel="noopener">
								<?php
								/* translators: %s: website of the original source, for example linkedin.com. */
								echo esc_html( sprintf( __( 'Read the original on %s', 'chargenet' ), $chargenet_item['source_host'] ) );
								?>
								<span class="visually-hidden"><?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span>
							</a>
						</p>
					<?php endif; ?>
				</div>
			</li>
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
