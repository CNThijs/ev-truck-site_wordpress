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

$chargenet_variant  = in_array( $attributes['variant'] ?? '', array( 'colour', 'marquee' ), true ) ? $attributes['variant'] : 'grayscale';
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
	<?php if ( 'marquee' === $chargenet_variant ) : ?>
		<?php // Two copies make the loop seamless; the second is hidden from assistive technology and cannot be focused. ?>
		<div class="logo-strip__marquee">
			<ul class="logo-strip__list logo-strip__track" role="list"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></ul>
			<ul class="logo-strip__list logo-strip__track logo-strip__track--copy" role="list" aria-hidden="true" inert><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></ul>
		</div>
	<?php else : ?>
		<ul class="logo-strip__list" role="list" data-reveal-group>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
		</ul>
	<?php endif; ?>
</div>
<?php
chargenet_section_close();
