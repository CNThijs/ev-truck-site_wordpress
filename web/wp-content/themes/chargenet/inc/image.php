<?php
/**
 * Responsive images for sections: modern output formats and one helper that sets srcset, sizes,
 * width/height and loading hints.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate WebP sizes from JPEG and PNG uploads, AVIF for JPEG when the server's image library can write it.
 * The original upload is kept as is; only the resized copies change format.
 *
 * @param array<string, string> $formats Source mime type => output mime type.
 * @return array<string, string>
 */
function chargenet_image_output_formats( array $formats ): array {
	$modern = wp_image_editor_supports( array( 'mime_type' => 'image/avif' ) ) ? 'image/avif' : 'image/webp';

	$formats['image/jpeg'] = $modern;
	$formats['image/png']  = 'image/webp';
	return $formats;
}
add_filter( 'image_editor_output_format', 'chargenet_image_output_formats' );

/**
 * Compression quality of generated images. WordPress' default (82) gives files about twice the size of quality 70
 * with no visible difference on photos (measured on the hero background: 56 KB to 26 KB). Applies to newly generated sizes.
 *
 * @param int    $quality Default quality.
 * @param string $mime    Mime type of the output.
 */
function chargenet_image_quality( int $quality, string $mime ): int {
	return 'image/avif' === $mime ? 60 : 70;
}
add_filter( 'wp_editor_set_quality', 'chargenet_image_quality', 10, 2 );

/**
 * Whether the section being rendered is the first one on the page (its images are in the first viewport).
 */
function chargenet_in_first_section(): bool {
	return 1 === chargenet_section_counter();
}

/**
 * Responsive <img> for an attachment, or an empty string when there is no image.
 *
 * WordPress adds srcset, width and height from the attachment metadata. Images in the first section load
 * eagerly with high priority; all others are lazy.
 *
 * @param int                  $attachment_id Attachment id.
 * @param array<string, mixed> $args          size (default large), sizes, class, alt (override; '' keeps the media alt),
 *                                            decorative (bool, alt=""), priority (bool; default: first section only).
 */
function chargenet_image( int $attachment_id, array $args = array() ): string {
	if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
		return '';
	}

	$priority = (bool) ( $args['priority'] ?? chargenet_in_first_section() );
	$attr     = array(
		'loading'  => $priority ? 'eager' : 'lazy',
		'decoding' => $priority ? 'sync' : 'async',
	);
	if ( $priority ) {
		$attr['fetchpriority'] = 'high';
	}
	if ( ! empty( $args['sizes'] ) ) {
		$attr['sizes'] = (string) $args['sizes'];
	}
	if ( ! empty( $args['class'] ) ) {
		$attr['class'] = (string) $args['class'];
	}
	if ( ! empty( $args['decorative'] ) ) {
		$attr['alt'] = '';
	} elseif ( '' !== trim( (string) ( $args['alt'] ?? '' ) ) ) {
		$attr['alt'] = trim( (string) $args['alt'] );
	}

	return wp_get_attachment_image( $attachment_id, (string) ( $args['size'] ?? 'large' ), false, $attr );
}

/**
 * Preload the hero background image so it downloads in parallel with the CSS instead of after it. Must name the same
 * source, srcset and sizes as the <img> that blocks/hero/render.php prints (size full, sizes 100vw).
 */
function chargenet_preload_hero_image(): void {
	if ( ! is_singular() ) {
		return;
	}
	$blocks = parse_blocks( (string) get_post_field( 'post_content', get_queried_object_id() ) );
	foreach ( $blocks as $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue;
		}
		$id  = 'chargenet/hero' === $block['blockName'] && 'title-band' !== ( $block['attrs']['variant'] ?? '' ) ? (int) ( $block['attrs']['imageId'] ?? 0 ) : 0;
		$src = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
		if ( $src ) {
			printf(
				'<link rel="preload" as="image" href="%s" imagesrcset="%s" imagesizes="100vw" fetchpriority="high">' . "\n",
				esc_url( $src[0] ),
				esc_attr( (string) wp_get_attachment_image_srcset( $id, 'full' ) )
			);
		}
		return; // Only the first real block can be in the first viewport.
	}
}
add_action( 'wp_head', 'chargenet_preload_hero_image', 3 );
