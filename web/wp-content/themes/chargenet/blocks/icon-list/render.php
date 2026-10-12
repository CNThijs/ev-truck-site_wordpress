<?php
/**
 * Icon List. Variables: $attributes, $content (rendered items), $block (context).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no items, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_variant = in_array( $attributes['variant'] ?? '', array( 'tiles', 'chips', 'journey' ), true ) ? $attributes['variant'] : 'tiles';
$chargenet_title   = trim( wp_strip_all_tags( (string) ( $attributes['title'] ?? '' ) ) );
$chargenet_level   = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 2 ) );
$chargenet_tag     = 'journey' === $chargenet_variant ? 'ol' : 'ul';
?>
<div class="icon-list icon-list--<?php echo esc_attr( $chargenet_variant ); ?>">
	<?php if ( '' !== $chargenet_title ) : ?>
		<h<?php echo (int) $chargenet_level; ?> class="icon-list__title"><?php echo esc_html( $chargenet_title ); ?></h<?php echo (int) $chargenet_level; ?>>
	<?php endif; ?>
	<<?php echo esc_attr( $chargenet_tag ); ?> class="icon-list__items" role="list"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></<?php echo esc_attr( $chargenet_tag ); ?>>
</div>
