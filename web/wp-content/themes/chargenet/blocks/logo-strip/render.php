<?php
/**
 * Logo Strip. Variables: $attributes, $content (rendered logos).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no logos yet, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_variant  = 'colour' === ( $attributes['variant'] ?? '' ) ? 'colour' : 'grayscale';
$chargenet_title_id = chargenet_section_title_id( $attributes );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'logo-strip',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="logo-strip logo-strip--<?php echo esc_attr( $chargenet_variant ); ?> stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<ul class="logo-strip__list" role="list" data-reveal-group>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</ul>
</div>
<?php
chargenet_section_close();
