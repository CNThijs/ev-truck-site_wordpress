<?php
/**
 * Rich Text and Image. Variables: $attributes, $content (rendered inner blocks).
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
$chargenet_position = 'left' === ( $attributes['imagePosition'] ?? '' ) ? 'left' : 'right';
$chargenet_image_id = (int) ( $attributes['imageId'] ?? 0 );
$chargenet_alt      = trim( (string) ( $attributes['imageAlt'] ?? '' ) );
$chargenet_eyebrow  = trim( wp_strip_all_tags( (string) ( $attributes['eyebrow'] ?? '' ) ) );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'rich-text-image',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="rti rti--image-<?php echo esc_attr( $chargenet_position ); ?>" data-reveal>
	<div class="rti__body stack">
		<?php if ( '' !== $chargenet_eyebrow ) : ?>
			<p class="t-eyebrow"><?php echo esc_html( $chargenet_eyebrow ); ?></p>
		<?php endif; ?>
		<?php chargenet_heading( $chargenet_heading, (int) ( $attributes['headingLevel'] ?? 2 ), $chargenet_title_id ); ?>
		<div class="rti__content stack"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
	</div>
	<?php if ( $chargenet_image_id && wp_attachment_is_image( $chargenet_image_id ) ) : ?>
		<figure class="rti__media">
			<?php
			echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes.
				$chargenet_image_id,
				'large',
				false,
				array_filter(
					array(
						'sizes' => '(min-width: 48rem) 45vw, 100vw',
						'alt'   => '' !== $chargenet_alt ? $chargenet_alt : null,
					)
				)
			);
			?>
		</figure>
	<?php endif; ?>
</div>
<?php
chargenet_section_close();
