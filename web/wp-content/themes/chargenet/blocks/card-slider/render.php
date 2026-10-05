<?php
/**
 * Card Slider. Variables: $attributes, $content (rendered cards).
 *
 * Keep the markup in sync with edit() in index.js. Without JavaScript the cards sit in a scrollable row; view.js
 * un-hides the previous/next buttons. There is no auto-rotation.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no cards, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_variant  = 'image-top' === ( $attributes['variant'] ?? '' ) ? 'image-top' : 'image-bg';
$chargenet_title_id = chargenet_section_title_id( $attributes );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'card-slider',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="card-slider card-slider--<?php echo esc_attr( $chargenet_variant ); ?> stack" data-card-slider>
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<div class="card-slider__viewport" tabindex="0" role="region"<?php echo '' !== $chargenet_title_id ? ' aria-labelledby="' . esc_attr( $chargenet_title_id ) . '"' : ' aria-label="' . esc_attr__( 'Cards', 'chargenet' ) . '"'; ?>>
		<ul class="card-slider__track" role="list" data-reveal-group>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
		</ul>
	</div>
	<div class="card-slider__controls" hidden>
		<button type="button" class="card-slider__button card-slider__button--prev" data-slider-prev>
			<?php chargenet_the_icon( 'arrow-right' ); ?>
			<span class="visually-hidden"><?php esc_html_e( 'Previous cards', 'chargenet' ); ?></span>
		</button>
		<button type="button" class="card-slider__button" data-slider-next>
			<?php chargenet_the_icon( 'arrow-right' ); ?>
			<span class="visually-hidden"><?php esc_html_e( 'Next cards', 'chargenet' ); ?></span>
		</button>
	</div>
</div>
<?php
chargenet_section_close();
