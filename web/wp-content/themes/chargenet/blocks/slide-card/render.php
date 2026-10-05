<?php
/**
 * Slide Card. Variables: $attributes, $block (context).
 *
 * Keep the markup in sync with edit() in index.js. The image is decorative unless the editor wrote alt text (the
 * title sits right beside it). With a link the title is the link and covers the whole card; the optional label
 * is a visual cue only, so the card has a single link.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_title = trim( wp_strip_all_tags( (string) ( $attributes['title'] ?? '' ) ) );
if ( '' === $chargenet_title ) {
	return;
}

$chargenet_text  = (string) ( $attributes['text'] ?? '' );
$chargenet_url   = (string) ( $attributes['url'] ?? '' );
$chargenet_label = trim( wp_strip_all_tags( (string) ( $attributes['linkLabel'] ?? '' ) ) );
$chargenet_tags  = array_filter( array_map( 'trim', explode( ',', wp_strip_all_tags( (string) ( $attributes['tags'] ?? '' ) ) ) ) );
$chargenet_level = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 1 ) );
$chargenet_is_bg = 'image-bg' === ( $block->context['chargenet/sliderVariant'] ?? 'image-bg' );
$chargenet_alt   = trim( (string) ( $attributes['imageAlt'] ?? '' ) );
$chargenet_image = chargenet_image(
	(int) ( $attributes['imageId'] ?? 0 ),
	array(
		'size'       => 'large',
		'sizes'      => '(min-width: 48rem) 30vw, 85vw',
		'class'      => 'slide__img',
		'alt'        => $chargenet_alt,
		'decorative' => '' === $chargenet_alt,
		'priority'   => false,
	)
);
?>
<li class="slide<?php echo $chargenet_is_bg ? ' is-dark' : ''; ?>" data-reveal>
	<?php if ( '' !== $chargenet_image ) : ?>
		<figure class="slide__media"><?php echo $chargenet_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?></figure>
	<?php endif; ?>
	<div class="slide__body">
		<h<?php echo (int) $chargenet_level; ?> class="slide__title">
			<?php if ( '' !== $chargenet_url ) : ?>
				<a class="slide__link" href="<?php echo esc_url( $chargenet_url ); ?>"><?php echo esc_html( $chargenet_title ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $chargenet_title ); ?>
			<?php endif; ?>
		</h<?php echo (int) $chargenet_level; ?>>
		<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_text ) ) ) : ?>
			<p class="slide__text"><?php echo wp_kses_post( $chargenet_text ); ?></p>
		<?php endif; ?>
		<?php if ( $chargenet_tags ) : ?>
			<ul class="slide__tags" role="list">
				<?php foreach ( $chargenet_tags as $chargenet_tag ) : ?>
					<li class="badge badge--outline"><?php echo esc_html( $chargenet_tag ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( '' !== $chargenet_url && '' !== $chargenet_label ) : ?>
			<span class="slide__more" aria-hidden="true"><?php echo esc_html( $chargenet_label ); ?> →</span>
		<?php endif; ?>
	</div>
</li>
