<?php
/**
 * Accordion item: native details/summary, closed on load. Variables: $attributes, $content (rendered answer).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_question = trim( wp_strip_all_tags( (string) ( $attributes['question'] ?? '' ) ) );
if ( '' === $chargenet_question || '' === trim( $content ) ) {
	return;
}
?>
<details class="accordion-item" data-reveal>
	<summary class="accordion-item__summary"><?php echo esc_html( $chargenet_question ); ?></summary>
	<div class="accordion-item__panel"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
</details>
