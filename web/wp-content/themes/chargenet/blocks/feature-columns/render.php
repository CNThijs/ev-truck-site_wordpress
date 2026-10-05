<?php
/**
 * Feature Columns. Variables: $attributes, $content (rendered columns).
 *
 * Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no columns, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_heading  = (string) ( $attributes['heading'] ?? '' );
$chargenet_title_id = '' !== trim( wp_strip_all_tags( $chargenet_heading ) ) ? wp_unique_id( 'section-title-' ) : '';
$chargenet_eyebrow  = trim( wp_strip_all_tags( (string) ( $attributes['eyebrow'] ?? '' ) ) );
$chargenet_intro    = (string) ( $attributes['intro'] ?? '' );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'feature-columns',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="feature-columns stack">
	<?php if ( '' !== $chargenet_eyebrow || '' !== $chargenet_title_id || '' !== trim( wp_strip_all_tags( $chargenet_intro ) ) ) : ?>
		<header class="section-intro stack">
			<?php if ( '' !== $chargenet_eyebrow ) : ?>
				<p class="t-eyebrow"><?php echo esc_html( $chargenet_eyebrow ); ?></p>
			<?php endif; ?>
			<?php chargenet_heading( $chargenet_heading, (int) ( $attributes['headingLevel'] ?? 2 ), $chargenet_title_id ); ?>
			<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_intro ) ) ) : ?>
				<p class="t-lead"><?php echo wp_kses_post( $chargenet_intro ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>
	<div class="feature-columns__list" data-reveal-group>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</div>
</div>
<?php
chargenet_section_close();
