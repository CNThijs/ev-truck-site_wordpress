<?php
/**
 * Hero. Variables: $attributes, $content (rendered buttons).
 *
 * Keep the markup in sync with edit() in index.js. The h1 is this block's own heading field.
 * The background image is decorative (the text carries the meaning); the campaign cover image keeps its alt text.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_variant = in_array( $attributes['variant'] ?? '', array( 'banner', 'title-band', 'campaign' ), true ) ? $attributes['variant'] : 'banner';
$chargenet_heading = (string) ( $attributes['heading'] ?? '' );
$chargenet_intro   = (string) ( $attributes['intro'] ?? '' );
$chargenet_eyebrow = trim( wp_strip_all_tags( (string) ( $attributes['eyebrow'] ?? '' ) ) );
$chargenet_has_bg  = 'title-band' !== $chargenet_variant && (int) ( $attributes['imageId'] ?? 0 ) > 0;
$chargenet_cover   = 'campaign' === $chargenet_variant ? chargenet_image(
	(int) ( $attributes['coverId'] ?? 0 ),
	array(
		'size'  => 'large',
		'sizes' => '(min-width: 48rem) 40vw, 100vw',
		'class' => 'hero__cover-img',
		'alt'   => (string) ( $attributes['coverAlt'] ?? '' ),
	)
) : '';

// Empty state: nothing to show without a heading.
if ( '' === trim( wp_strip_all_tags( $chargenet_heading ) ) ) {
	return;
}

$chargenet_title_id = wp_unique_id( 'hero-title-' );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'hero',
		'labelledby' => $chargenet_title_id,
		'class'      => 'hero hero--' . $chargenet_variant,
	)
);
?>
<?php if ( $chargenet_has_bg ) : ?>
	<div class="hero__bg" data-hero-media aria-hidden="true">
		<?php
		echo chargenet_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
			(int) $attributes['imageId'],
			array(
				'size'       => 'full',
				'sizes'      => '100vw',
				'class'      => 'hero__bg-img',
				'decorative' => true,
				'priority'   => true,
			)
		);
		?>
		<span class="hero__overlay" data-overlay="<?php echo 'strong' === ( $attributes['overlay'] ?? '' ) ? 'strong' : 'standard'; ?>"></span>
	</div>
<?php endif; ?>
<div class="hero__inner">
	<div class="hero__body stack">
		<?php if ( '' !== $chargenet_eyebrow ) : ?>
			<p class="t-eyebrow"><?php echo esc_html( $chargenet_eyebrow ); ?></p>
		<?php endif; ?>
		<h1 id="<?php echo esc_attr( $chargenet_title_id ); ?>" class="hero__title"><?php echo wp_kses_post( $chargenet_heading ); ?></h1>
		<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_intro ) ) ) : ?>
			<p class="t-lead hero__intro"><?php echo wp_kses_post( $chargenet_intro ); ?></p>
		<?php endif; ?>
		<?php if ( '' !== trim( $content ) ) : ?>
			<div class="cluster hero__actions"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
		<?php endif; ?>
	</div>
	<?php if ( '' !== $chargenet_cover ) : ?>
		<figure class="hero__cover" data-reveal><?php echo $chargenet_cover; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?></figure>
	<?php endif; ?>
</div>
<?php
chargenet_section_close();
