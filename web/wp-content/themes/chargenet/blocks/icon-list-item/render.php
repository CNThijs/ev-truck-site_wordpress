<?php
/**
 * Icon List Item. Variables: $attributes, $block (context).
 *
 * Keep the markup in sync with edit() in index.js. Icons are decoration: the item always has visible text.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_title = trim( wp_strip_all_tags( (string) ( $attributes['title'] ?? '' ) ) );
$chargenet_text  = (string) ( $attributes['text'] ?? '' );
if ( '' === $chargenet_title && '' === trim( wp_strip_all_tags( $chargenet_text ) ) ) {
	return;
}

$chargenet_variant = (string) ( $block->context['chargenet/iconListVariant'] ?? 'tiles' );
$chargenet_level   = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 2 ) );
$chargenet_icon    = (string) ( $attributes['icon'] ?? '' );
?>
<li class="icon-item" data-reveal>
	<?php if ( '' !== $chargenet_icon ) : ?>
		<span class="icon-item__icon"><?php chargenet_the_icon( $chargenet_icon ); ?></span>
	<?php endif; ?>
	<div class="icon-item__body">
		<?php if ( '' !== $chargenet_title ) : ?>
			<?php if ( 'chips' === $chargenet_variant ) : ?>
				<span class="icon-item__title"><?php echo esc_html( $chargenet_title ); ?></span>
			<?php else : ?>
				<h<?php echo (int) $chargenet_level; ?> class="icon-item__title"><?php echo esc_html( $chargenet_title ); ?></h<?php echo (int) $chargenet_level; ?>>
			<?php endif; ?>
		<?php endif; ?>
		<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_text ) ) ) : ?>
			<p class="icon-item__text"><?php echo wp_kses_post( $chargenet_text ); ?></p>
		<?php endif; ?>
	</div>
</li>
