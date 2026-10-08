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

// Horizontal scroll pins the page for as long as the row is wide, so it shows at most 10 cards: with more, 10 are
// picked at random on each render (original order kept). Page caching keeps one pick until the cache is cleared.
$chargenet_content = $content;
if ( 'horizontal' === ( $attributes['animation'] ?? '' ) && count( $block->inner_blocks ) > CHARGENET_HORIZONTAL_MAX_CARDS ) {
	$chargenet_cards = iterator_to_array( $block->inner_blocks );
	$chargenet_keep  = (array) array_rand( $chargenet_cards, CHARGENET_HORIZONTAL_MAX_CARDS );
	sort( $chargenet_keep );
	$chargenet_content = '';
	foreach ( $chargenet_keep as $chargenet_index ) {
		$chargenet_content .= $chargenet_cards[ $chargenet_index ]->render();
	}
}

// Empty state: no cards, nothing to show.
if ( '' === trim( $chargenet_content ) ) {
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
	<div class="card-slider__viewport" tabindex="0" role="group"<?php echo '' !== $chargenet_title_id ? ' aria-labelledby="' . esc_attr( $chargenet_title_id ) . '"' : ' aria-label="' . esc_attr__( 'Cards', 'chargenet' ) . '"'; ?>>
		<ul class="card-slider__track" role="list" data-reveal-group>
			<?php echo $chargenet_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
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
