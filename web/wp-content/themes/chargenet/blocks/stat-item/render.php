<?php
/**
 * Statistic. Variables: $attributes.
 *
 * Keep the markup in sync with edit() in index.js. data-count carries the plain number for the animation system.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_value  = trim( wp_strip_all_tags( (string) ( $attributes['value'] ?? '' ) ) );
$chargenet_prefix = trim( wp_strip_all_tags( (string) ( $attributes['prefix'] ?? '' ) ) );
$chargenet_suffix = trim( wp_strip_all_tags( (string) ( $attributes['suffix'] ?? '' ) ) );
$chargenet_label  = trim( wp_strip_all_tags( (string) ( $attributes['label'] ?? '' ) ) );
if ( '' === $chargenet_value ) {
	return;
}

$chargenet_numeric = is_numeric( $chargenet_value );
?>
<li class="stat" data-reveal>
	<span class="stat__value"<?php echo $chargenet_numeric ? ' data-count="' . esc_attr( $chargenet_value ) . '" data-count-prefix="' . esc_attr( $chargenet_prefix ) . '" data-count-suffix="' . esc_attr( $chargenet_suffix ) . '"' : ''; ?>><?php echo esc_html( $chargenet_prefix . $chargenet_value . $chargenet_suffix ); ?></span>
	<?php if ( '' !== $chargenet_label ) : ?>
		<span class="stat__label"><?php echo esc_html( $chargenet_label ); ?></span>
	<?php endif; ?>
</li>
