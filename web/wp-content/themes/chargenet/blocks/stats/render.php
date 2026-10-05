<?php
/**
 * Statistics. Variables: $attributes, $content (rendered stat items).
 *
 * Keep the markup in sync with edit() in index.js. Numbers are printed in their final form; the animation
 * system may count up from data-count, but the page is complete without it.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no figures, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_variant  = 'with-text' === ( $attributes['variant'] ?? '' ) ? 'with-text' : 'row';
$chargenet_heading  = (string) ( $attributes['heading'] ?? '' );
$chargenet_title_id = '' !== trim( wp_strip_all_tags( $chargenet_heading ) ) ? wp_unique_id( 'section-title-' ) : '';
$chargenet_eyebrow  = trim( wp_strip_all_tags( (string) ( $attributes['eyebrow'] ?? '' ) ) );
$chargenet_text     = (string) ( $attributes['text'] ?? '' );
$chargenet_has_bg   = (int) ( $attributes['imageId'] ?? 0 ) > 0;
$chargenet_has_head = '' !== $chargenet_eyebrow || '' !== $chargenet_title_id || '' !== trim( wp_strip_all_tags( $chargenet_text ) );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'stats',
		'labelledby' => $chargenet_title_id,
		'class'      => 'stats stats--' . $chargenet_variant . ( $chargenet_has_bg ? ' stats--has-bg' : '' ),
	)
);
?>
<?php if ( $chargenet_has_bg ) : ?>
	<div class="stats__bg" data-hero-media aria-hidden="true">
		<?php
		echo chargenet_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
			(int) $attributes['imageId'],
			array(
				'size'       => 'full',
				'sizes'      => '100vw',
				'class'      => 'stats__bg-img',
				'decorative' => true,
			)
		);
		?>
		<span class="stats__overlay"></span>
	</div>
<?php endif; ?>
<div class="stats__layout">
	<?php if ( $chargenet_has_head ) : ?>
		<header class="stats__head stack">
			<?php if ( '' !== $chargenet_eyebrow ) : ?>
				<p class="t-eyebrow"><?php echo esc_html( $chargenet_eyebrow ); ?></p>
			<?php endif; ?>
			<?php chargenet_heading( $chargenet_heading, (int) ( $attributes['headingLevel'] ?? 2 ), $chargenet_title_id ); ?>
			<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_text ) ) ) : ?>
				<p class="t-lead"><?php echo wp_kses_post( $chargenet_text ); ?></p>
			<?php endif; ?>
		</header>
	<?php endif; ?>
	<ul class="stats__list" role="list" data-reveal-group>
		<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
	</ul>
</div>
<?php
chargenet_section_close();
