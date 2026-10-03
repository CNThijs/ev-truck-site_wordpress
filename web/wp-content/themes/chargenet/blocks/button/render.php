<?php
/**
 * Button (child of sections). Variables: $attributes.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_label   = trim( wp_strip_all_tags( (string) ( $attributes['label'] ?? '' ) ) );
$chargenet_url     = (string) ( $attributes['url'] ?? '' );
$chargenet_variant = in_array( $attributes['variant'] ?? '', array( 'primary', 'secondary', 'ghost' ), true ) ? $attributes['variant'] : 'primary';
$chargenet_new_tab = ! empty( $attributes['opensInNewTab'] );

if ( '' === $chargenet_label || '' === $chargenet_url ) {
	return;
}
?>
<a class="btn btn--<?php echo esc_attr( $chargenet_variant ); ?>" href="<?php echo esc_url( $chargenet_url ); ?>"<?php echo $chargenet_new_tab ? ' target="_blank" rel="noopener"' : ''; ?>>
	<?php echo esc_html( $chargenet_label ); ?>
	<?php if ( $chargenet_new_tab ) : ?>
		<span class="visually-hidden"><?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span>
	<?php endif; ?>
</a>
