<?php
/**
 * Step. Variables: $attributes, $content (rendered text and lists), $block (context).
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
?>
<li class="step" data-reveal>
	<span class="step__number" aria-hidden="true"></span>
	<div class="step__body">
		<?php if ( '' !== $chargenet_title ) : ?>
			<h<?php echo (int) $chargenet_level; ?> class="step__title"><?php echo esc_html( $chargenet_title ); ?></h<?php echo (int) $chargenet_level; ?>>
		<?php endif; ?>
		<?php if ( '' !== trim( $content ) ) : ?>
			<div class="step__content"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
		<?php endif; ?>
	</div>
</li>
