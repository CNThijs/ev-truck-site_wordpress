<?php
/**
 * Feature Column. Variables: $attributes, $content (rendered lists and headings), $block (context).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_title = trim( wp_strip_all_tags( (string) ( $attributes['title'] ?? '' ) ) );
if ( '' === $chargenet_title && '' === trim( $content ) ) {
	return;
}

$chargenet_level = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 1 ) );
$chargenet_url   = (string) ( $attributes['url'] ?? '' );
$chargenet_label = trim( wp_strip_all_tags( (string) ( $attributes['linkLabel'] ?? '' ) ) );
?>
<div class="feature-column" data-reveal>
	<?php if ( '' !== $chargenet_title ) : ?>
		<h<?php echo (int) $chargenet_level; ?> class="feature-column__title"><?php echo esc_html( $chargenet_title ); ?></h<?php echo (int) $chargenet_level; ?>>
	<?php endif; ?>
	<div class="feature-column__body"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
	<?php if ( '' !== $chargenet_url && '' !== $chargenet_label ) : ?>
		<a class="link-arrow feature-column__link" href="<?php echo esc_url( $chargenet_url ); ?>"><?php echo esc_html( $chargenet_label ); ?></a>
	<?php endif; ?>
</div>
