<?php
/**
 * Bento. Variables: $attributes, $content (rendered cards).
 *
 * A mosaic of 3 to 6 cards. The grid is a fixed 6-column layout per card count (see style.scss), so it never has an empty
 * cell; any other count falls back to an even grid. Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no cards, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_heading  = (string) ( $attributes['heading'] ?? '' );
$chargenet_title_id = '' !== trim( wp_strip_all_tags( $chargenet_heading ) ) ? wp_unique_id( 'section-title-' ) : '';
$chargenet_eyebrow  = trim( wp_strip_all_tags( (string) ( $attributes['eyebrow'] ?? '' ) ) );
$chargenet_intro    = (string) ( $attributes['intro'] ?? '' );
$chargenet_count    = substr_count( $content, 'class="bento-item ' );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'bento',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="bento stack">
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
	<div class="bento__grid bento__grid--n<?php echo (int) $chargenet_count; ?>" data-reveal-group>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</div>
</div>
<?php
chargenet_section_close();
