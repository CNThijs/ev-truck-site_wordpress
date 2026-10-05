<?php
/**
 * Single post body: title band (category and date above the title), featured image, text, original source and a
 * link back to the news.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_terms   = get_the_category();
$chargenet_eyebrow = trim( ( $chargenet_terms ? $chargenet_terms[0]->name . ' · ' : '' ) . get_the_date() );
$chargenet_blog    = (int) get_option( 'page_for_posts' );

echo do_blocks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered block.
	'<!-- wp:chargenet/hero ' . wp_json_encode(
		array(
			'variant' => 'title-band',
			'eyebrow' => $chargenet_eyebrow,
			'heading' => get_the_title(),
		)
	) . ' /-->'
);
?>
<article <?php post_class( 'section is-light post-single' ); ?>>
	<div class="container container--narrow stack">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="post-single__image">
				<?php
				echo chargenet_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
					(int) get_post_thumbnail_id(),
					array(
						'size'       => 'large',
						'sizes'      => '(min-width: 48rem) 48rem, 100vw',
						'decorative' => true,
					)
				);
				?>
			</figure>
		<?php endif; ?>
		<div class="post-single__content stack">
			<?php the_content(); ?>
		</div>
		<?php chargenet_the_post_source(); ?>
		<?php if ( $chargenet_blog ) : ?>
			<p><a class="link-arrow" href="<?php echo esc_url( (string) get_permalink( $chargenet_blog ) ); ?>"><?php esc_html_e( 'Back to news', 'chargenet' ); ?></a></p>
		<?php endif; ?>
	</div>
</article>
