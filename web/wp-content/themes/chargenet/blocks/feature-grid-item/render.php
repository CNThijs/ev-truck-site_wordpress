<?php
/**
 * Feature Grid item. Variables: $attributes, $block (context: heading level of the parent section).
 *
 * Keep the markup in sync with edit() in index.js. With a link, the title link stretches over the whole item.
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

$chargenet_level = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 1 ) );
$chargenet_cards = 'cards' === ( $block->context['chargenet/gridVariant'] ?? 'cards' );
$chargenet_url   = (string) ( $attributes['url'] ?? '' );
$chargenet_arrow = trim( wp_strip_all_tags( (string) ( $attributes['linkLabel'] ?? '' ) ) );
$chargenet_class = 'feature-grid__item' . ( $chargenet_cards ? ' card' : '' ) . ( '' !== $chargenet_url ? ' has-link' : '' );
?>
<li class="<?php echo esc_attr( $chargenet_class ); ?>" data-reveal>
	<?php if ( '' !== (string) ( $attributes['icon'] ?? '' ) ) : ?>
		<span class="feature-grid__icon"><?php chargenet_the_icon( (string) $attributes['icon'] ); ?></span>
	<?php endif; ?>
	<?php if ( '' !== $chargenet_title ) : ?>
		<h<?php echo (int) $chargenet_level; ?> class="feature-grid__title">
			<?php if ( '' !== $chargenet_url && '' === $chargenet_arrow ) : ?>
				<a class="feature-grid__link" href="<?php echo esc_url( $chargenet_url ); ?>"><?php echo esc_html( $chargenet_title ); ?></a>
			<?php else : ?>
				<?php echo esc_html( $chargenet_title ); ?>
			<?php endif; ?>
		</h<?php echo (int) $chargenet_level; ?>>
	<?php endif; ?>
	<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_text ) ) ) : ?>
		<p class="feature-grid__text"><?php echo wp_kses_post( $chargenet_text ); ?></p>
	<?php endif; ?>
	<?php if ( '' !== $chargenet_url && '' !== $chargenet_arrow ) : ?>
		<a class="link-arrow feature-grid__link" href="<?php echo esc_url( $chargenet_url ); ?>"><?php echo esc_html( $chargenet_arrow ); ?></a>
	<?php endif; ?>
</li>
