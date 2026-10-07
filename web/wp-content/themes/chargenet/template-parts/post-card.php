<?php
/**
 * News card: image, category, date, title (the one link of the card), excerpt and the original source. Used by the
 * Post Grid section and the search results. Arguments: item (see chargenet_post_card_item()) and level (heading).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_item  = (array) ( $args['item'] ?? array() );
$chargenet_level = (int) ( $args['level'] ?? 3 );
if ( ! $chargenet_item ) {
	return;
}
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
	<?php if ( '' !== $chargenet_image ) : ?>
		<figure class="post-card__media">
			<?php echo $chargenet_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?>
		</figure>
	<?php endif; ?>
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
