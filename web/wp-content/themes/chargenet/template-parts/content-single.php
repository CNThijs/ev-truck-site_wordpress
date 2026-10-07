<?php
/**
 * Single news post: title band (category and date above the title), byline with reading time, featured image,
 * text (with a table of contents for long posts), original source, share links, newsletter sign-up, related news and
 * a call to action. The comments are off.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_id      = (int) get_the_ID();
$chargenet_terms   = get_the_category();
$chargenet_eyebrow = trim( ( $chargenet_terms ? $chargenet_terms[0]->name . ' · ' : '' ) . get_the_date() );
$chargenet_blog    = (int) get_option( 'page_for_posts' );
$chargenet_share   = chargenet_share_links( (string) get_permalink(), get_the_title() );
$chargenet_minutes = chargenet_reading_minutes( (string) get_post_field( 'post_content', $chargenet_id ) );
$chargenet_contact = chargenet_seeded_page_url( 'contact' );

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
		<p class="post-single__byline">
			<?php
			/* translators: %s: author of the post, for example ChargeNet. */
			echo esc_html( sprintf( __( 'By %s', 'chargenet' ), chargenet_post_author( $chargenet_id ) ) );
			?>
			<span aria-hidden="true">·</span>
			<?php
			/* translators: %d: minutes to read. */
			echo esc_html( sprintf( __( '%d min read', 'chargenet' ), $chargenet_minutes ) );
			?>
		</p>
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
		<aside class="post-share" aria-labelledby="post-share-title">
			<h2 class="post-share__title" id="post-share-title"><?php esc_html_e( 'Share this article', 'chargenet' ); ?></h2>
			<ul class="post-share__list" role="list">
				<li><a class="btn btn--secondary btn--sm" href="<?php echo esc_url( $chargenet_share['linkedin'] ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'LinkedIn', 'chargenet' ); ?><span class="visually-hidden"> <?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span></a></li>
				<li><a class="btn btn--secondary btn--sm" href="<?php echo esc_url( $chargenet_share['email'] ); ?>"><?php esc_html_e( 'Email', 'chargenet' ); ?></a></li>
				<li hidden data-copy-item><button type="button" class="btn btn--secondary btn--sm" data-copy-link="<?php echo esc_url( (string) get_permalink() ); ?>" data-copied="<?php esc_attr_e( 'Link copied', 'chargenet' ); ?>"><?php esc_html_e( 'Copy link', 'chargenet' ); ?></button></li>
			</ul>
			<p class="visually-hidden" role="status" data-copy-status></p>
		</aside>
		<?php if ( $chargenet_blog ) : ?>
			<p><a class="link-arrow" href="<?php echo esc_url( (string) get_permalink( $chargenet_blog ) ); ?>"><?php esc_html_e( 'Back to news', 'chargenet' ); ?></a></p>
		<?php endif; ?>
	</div>
</article>
<?php
// Newsletter, related news and the call to action are sections of the section library.
echo do_blocks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
	'<!-- wp:chargenet/newsletter-signup ' . wp_json_encode(
		array(
			'sectionBackground' => 'paper',
			'eyebrow'           => __( 'Newsletter', 'chargenet' ),
			'heading'           => __( 'News about electric truck charging in your inbox', 'chargenet' ),
			'intro'             => __( 'Partnerships, new charging locations and what we learn along the way.', 'chargenet' ),
		)
	) . ' /-->'
);

$chargenet_related_attrs = array(
	'count'      => 3,
	'exclude'    => $chargenet_id,
	'eyebrow'    => __( 'Keep reading', 'chargenet' ),
	'heading'    => __( 'Related news', 'chargenet' ),
	'categoryId' => $chargenet_terms ? (int) $chargenet_terms[0]->term_id : 0,
	'linkLabel'  => __( 'View all news', 'chargenet' ),
);
// A category with fewer than three other posts: show the newest news instead.
if ( count( chargenet_post_grid_items( $chargenet_related_attrs ) ) < 3 ) {
	$chargenet_related_attrs['categoryId'] = 0;
}
echo do_blocks( '<!-- wp:chargenet/post-grid ' . wp_json_encode( $chargenet_related_attrs ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered block.

if ( '' !== $chargenet_contact ) {
	echo do_blocks( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
		'<!-- wp:chargenet/cta-band ' . wp_json_encode( array( 'heading' => __( 'Want to charge your fleet at the right price, or open up your charge points?', 'chargenet' ) ) ) . ' -->' .
		'<!-- wp:chargenet/button ' . wp_json_encode(
			array(
				'label' => __( 'Contact us', 'chargenet' ),
				'url'   => $chargenet_contact,
			)
		) . ' /-->' .
		'<!-- /wp:chargenet/cta-band -->'
	);
}
?>
<script>
// Copy link: the button is hidden until this runs, so without JavaScript only the two links show.
(function(){var b=document.querySelector('[data-copy-link]');if(!b||!navigator.clipboard){return;}b.closest('[data-copy-item]').hidden=false;b.addEventListener('click',function(){navigator.clipboard.writeText(b.dataset.copyLink).then(function(){document.querySelector('[data-copy-status]').textContent=b.dataset.copied;});});})();
</script>
