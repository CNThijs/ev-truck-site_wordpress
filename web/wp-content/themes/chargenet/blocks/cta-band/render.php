<?php
/**
 * Call To Action Band. Variables: $attributes, $content (rendered buttons).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_heading  = (string) ( $attributes['heading'] ?? '' );
$chargenet_title_id = '' !== trim( wp_strip_all_tags( $chargenet_heading ) ) ? wp_unique_id( 'section-title-' ) : '';
$chargenet_text     = (string) ( $attributes['text'] ?? '' );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'cta-band',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="cta-band stack">
	<?php chargenet_heading( $chargenet_heading, (int) ( $attributes['headingLevel'] ?? 2 ), $chargenet_title_id ); ?>
	<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_text ) ) ) : ?>
		<p class="t-lead"><?php echo wp_kses_post( $chargenet_text ); ?></p>
	<?php endif; ?>
	<?php if ( '' !== trim( $content ) ) : ?>
		<div class="cluster cta-band__actions"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
	<?php endif; ?>
</div>
<?php
chargenet_section_close();
