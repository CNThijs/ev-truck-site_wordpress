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
	<ol class="steps__list" role="list" data-reveal-group>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</ol>
</div>
<?php
chargenet_section_close();
