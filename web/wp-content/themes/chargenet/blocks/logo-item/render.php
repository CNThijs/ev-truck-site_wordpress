<?php
/**
 * Logo. Variables: $attributes.
 *
 * Keep the markup in sync with edit() in index.js. The name is the alt text (so a linked logo has a link name);
 * without a name the media item's alt text is used.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_name = trim( wp_strip_all_tags( (string) ( $attributes['name'] ?? '' ) ) );
$chargenet_url  = (string) ( $attributes['url'] ?? '' );
$chargenet_logo = chargenet_image(
	(int) ( $attributes['imageId'] ?? 0 ),
	array(
		'size'     => 'medium',
		'sizes'    => '160px',
		'class'    => 'logo-item__img',
		'alt'      => $chargenet_name,
		'priority' => false,
	)
);
if ( '' === $chargenet_logo ) {
	return;
}
?>
<li class="logo-item" data-reveal>
	<?php if ( '' !== $chargenet_url ) : ?>
		<a class="logo-item__link" href="<?php echo esc_url( $chargenet_url ); ?>" target="_blank" rel="noopener"><?php echo $chargenet_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?><span class="visually-hidden"> <?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span></a>
	<?php else : ?>
		<?php echo $chargenet_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?>
	<?php endif; ?>
</li>
