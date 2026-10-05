<?php
/**
 * Steps. Variables: $attributes, $content (rendered steps).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no steps, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_variant  = 'vertical' === ( $attributes['variant'] ?? '' ) ? 'vertical' : 'horizontal';
$chargenet_title_id = chargenet_section_title_id( $attributes );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'steps',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="steps steps--<?php echo esc_attr( $chargenet_variant ); ?> stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<div class="steps__track" data-draw-scope>
		<?php
		// The connector is an SVG line the "draw" animation preset can draw; without JavaScript it is simply there.
		$chargenet_vertical = 'vertical' === $chargenet_variant;
		?>
		<svg class="steps__line" aria-hidden="true" focusable="false" viewBox="<?php echo $chargenet_vertical ? '0 0 2 100' : '0 0 100 2'; ?>" preserveAspectRatio="none">
			<path d="<?php echo $chargenet_vertical ? 'M1 0V100' : 'M0 1H100'; ?>" pathLength="1" data-draw<?php echo $chargenet_vertical ? '' : ' data-draw-mode="enter"'; ?>></path>
		</svg>
		<ol class="steps__list" role="list" data-reveal-group>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
		</ol>
	</div>
</div>
<?php
chargenet_section_close();
