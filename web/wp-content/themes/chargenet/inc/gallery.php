<?php
/**
 * Admin-only Section Gallery at /section-gallery/: every section and variant with sample content.
 * Everyone else gets a 404. Sample content lives in inc/gallery-samples.php.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the route; flush rewrite rules once when it is first added.
 */
function chargenet_gallery_route(): void {
	add_rewrite_rule( '^section-gallery/?$', 'index.php?chargenet_gallery=1', 'top' );
	if ( '1' !== get_option( 'chargenet_gallery_route' ) ) {
		flush_rewrite_rules( false );
		update_option( 'chargenet_gallery_route', '1' );
	}
}
add_action( 'init', 'chargenet_gallery_route' );

/**
 * Register the query var.
 *
 * @param string[] $vars Public query vars.
 * @return string[]
 */
function chargenet_gallery_query_var( array $vars ): array {
	$vars[] = 'chargenet_gallery';
	return $vars;
}
add_filter( 'query_vars', 'chargenet_gallery_query_var' );

/**
 * Serve the template to administrators; 404 for everyone else.
 *
 * @param string $template Resolved template path.
 */
function chargenet_gallery_template( string $template ): string {
	if ( ! get_query_var( 'chargenet_gallery' ) ) {
		return $template;
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow' );

	if ( ! current_user_can( 'manage_options' ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		return get_404_template();
	}
	return CHARGENET_DIR . '/page-templates/section-gallery.php';
}
add_filter( 'template_include', 'chargenet_gallery_template' );

/**
 * Keep Polylang from redirecting the gallery to a language prefix: it is a tool page, not content.
 *
 * @param string|false $redirect Redirect URL.
 * @return string|false
 */
function chargenet_gallery_no_canonical_redirect( $redirect ) {
	return get_query_var( 'chargenet_gallery' ) ? false : $redirect;
}
add_filter( 'redirect_canonical', 'chargenet_gallery_no_canonical_redirect' );
add_filter( 'pll_check_canonical_url', 'chargenet_gallery_no_canonical_redirect' );

/**
 * Sample images for the gallery: generated once with GD (no files in the theme) and stored in the media library
 * as "ChargeNet gallery sample N", so the real responsive image pipeline (srcset, WebP) renders them.
 *
 * @return array<int, int> Sample number (1 and 2 wide, 3 portrait, 4 to 6 logos) => attachment id; empty without GD.
 */
function chargenet_gallery_samples(): array {
	static $samples = null;
	if ( null !== $samples ) {
		return $samples;
	}
	$samples = array();
	if ( ! function_exists( 'imagecreatetruecolor' ) ) {
		return $samples;
	}

	$specs = array(
		1 => array( 2000, 1125, array( 8, 58, 11 ), array( 21, 128, 61 ) ),
		2 => array( 2000, 1125, array( 14, 74, 20 ), array( 250, 225, 4 ) ),
		3 => array( 1000, 1400, array( 15, 95, 26 ), array( 201, 220, 203 ) ),
		4 => array( 480, 160, array( 8, 58, 11 ), array(), 'logo' ),
		5 => array( 480, 160, array( 21, 128, 61 ), array(), 'logo' ),
		6 => array( 480, 160, array( 17, 24, 39 ), array(), 'logo' ),
	);
	foreach ( $specs as $number => $spec ) {
		$existing = get_posts(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'meta_key'    => '_chargenet_gallery_sample', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'  => $number, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'numberposts' => 1,
				'fields'      => 'ids',
				'lang'        => '',
			)
		);
		if ( $existing ) {
			$samples[ $number ] = (int) $existing[0];
			continue;
		}

		list( $width, $height, $from, $to ) = $spec;
		$is_logo                            = 'logo' === ( $spec[4] ?? '' );
		$image                              = imagecreatetruecolor( $width, $height );
		if ( $is_logo ) {
			// A simple mark on a transparent background, so the logo variants have something to show.
			imagealphablending( $image, false );
			imagesavealpha( $image, true );
			imagefill( $image, 0, 0, imagecolorallocatealpha( $image, 0, 0, 0, 127 ) );
			imagealphablending( $image, true );
			$ink = imagecolorallocate( $image, $from[0], $from[1], $from[2] );
			imagefilledellipse( $image, 80, 80, 110, 110, $ink );
			imagefilledrectangle( $image, 170, 45, 420, 75, $ink );
			imagefilledrectangle( $image, 170, 95, 340, 115, $ink );
		}
		for ( $y = 0; ! $is_logo && $y < $height; $y += 4 ) {
			$mix   = $y / $height;
			$color = imagecolorallocate(
				$image,
				(int) ( $from[0] + ( $to[0] - $from[0] ) * $mix ),
				(int) ( $from[1] + ( $to[1] - $from[1] ) * $mix ),
				(int) ( $from[2] + ( $to[2] - $from[2] ) * $mix )
			);
			imagefilledrectangle( $image, 0, $y, $width, $y + 4, $color );
		}
		$ring = imagecolorallocatealpha( $image, 255, 255, 255, 105 );
		for ( $i = 1; ! $is_logo && $i <= 5; $i++ ) {
			imagefilledellipse( $image, (int) ( $width * 0.7 ), (int) ( $height * 0.45 ), $height * $i / 3, $height * $i / 3, $ring );
		}

		$upload = wp_upload_dir();
		$file   = trailingslashit( $upload['path'] ) . "chargenet-gallery-sample-{$number}." . ( $is_logo ? 'png' : 'jpg' );
		if ( $is_logo ) {
			imagepng( $image, $file );
		} else {
			imagejpeg( $image, $file, 82 );
		}

		$id = wp_insert_attachment(
			array(
				'post_mime_type' => $is_logo ? 'image/png' : 'image/jpeg',
				'post_title'     => "ChargeNet gallery sample {$number}",
				'post_status'    => 'inherit',
			),
			$file
		);
		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}
		require_once ABSPATH . 'wp-admin/includes/image.php';
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
		update_post_meta( $id, '_wp_attachment_image_alt', 'Sample image' );
		update_post_meta( $id, '_chargenet_gallery_sample', $number );
		$samples[ $number ] = (int) $id;
	}
	return $samples;
}

/**
 * Block markup string for a block (dynamic sections have no saved HTML, only attributes and inner blocks).
 *
 * @param string               $name     Block name, e.g. chargenet/hero.
 * @param array<string, mixed> $attrs    Attributes.
 * @param string               $children Markup of the inner blocks.
 */
function chargenet_gallery_block( string $name, array $attrs = array(), string $children = '' ): string {
	$name  = str_replace( 'core/', '', $name );
	$json  = $attrs ? ' ' . serialize_block_attributes( $attrs ) : '';
	$empty = '' === $children;
	return $empty ? "<!-- wp:{$name}{$json} /-->" : "<!-- wp:{$name}{$json} -->{$children}<!-- /wp:{$name} -->";
}

/**
 * Paragraph block markup.
 *
 * @param string $text Text.
 */
function chargenet_gallery_p( string $text ): string {
	return '<!-- wp:paragraph --><p>' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
}

/**
 * Heading block markup.
 *
 * @param string $text  Text.
 * @param int    $level Level.
 */
function chargenet_gallery_h( string $text, int $level = 4 ): string {
	return '<!-- wp:heading {"level":' . $level . '} --><h' . $level . ' class="wp-block-heading">' . esc_html( $text ) . '</h' . $level . '><!-- /wp:heading -->';
}

/**
 * Bullet list block markup.
 *
 * @param string[] $items List items.
 */
function chargenet_gallery_ul( array $items ): string {
	$html = '';
	foreach ( $items as $item ) {
		$html .= '<!-- wp:list-item --><li>' . esc_html( $item ) . '</li><!-- /wp:list-item -->';
	}
	return '<!-- wp:list --><ul class="wp-block-list">' . $html . '</ul><!-- /wp:list -->';
}

/**
 * Button child block markup.
 *
 * @param string $label   Label.
 * @param string $variant primary, secondary or ghost.
 */
function chargenet_gallery_button( string $label, string $variant = 'primary' ): string {
	return chargenet_gallery_block(
		'chargenet/button',
		array(
			'label'   => $label,
			'url'     => '#',
			'variant' => $variant,
		)
	);
}

/**
 * Sample posts for the Post Grid in the gallery (nothing is saved, so no fake posts reach the public blog).
 * A category id of 999999 stands for "no posts" and gives the empty state.
 *
 * @param array<int, array<string, mixed>> $items      Items from the real query.
 * @param array<string, mixed>             $attributes Block attributes.
 * @return array<int, array<string, mixed>>
 */
function chargenet_gallery_post_items( array $items, array $attributes ): array {
	if ( ! get_query_var( 'chargenet_gallery' ) ) {
		return $items;
	}
	if ( 999999 === (int) ( $attributes['categoryId'] ?? 0 ) ) {
		return array();
	}
	$samples = chargenet_gallery_samples();
	$titles  = array(
		array( 'ChargeNet and Maxem partner to unlock more EV truck charging capacity', 'Accelerate', 'Smart load balancing makes room for more trucks on the same grid connection.' ),
		array( 'A new charging site in Nieuwegein joins the network', 'Locations', 'Fast chargers are now open to approved carriers through the ChargeNet app.' ),
		array( 'ChargeNet at the Charge & Connect event', 'Events', 'Turning routes into real charging plans.' ),
		array( 'Electric trucks are gaining momentum', 'Research', 'A tipping point is ahead, research expects.' ),
		array( 'ChargeNet moves to the Connectr office in Arnhem', 'Company', 'A new home for the growing team.' ),
	);
	$items   = array();
	foreach ( $titles as $index => $row ) {
		$items[] = array(
			'title'       => $row[0],
			'url'         => '#',
			'date'        => '2026-06-' . sprintf( '%02d', 30 - $index * 3 ),
			'date_label'  => wp_date( 'j F Y', strtotime( '2026-06-' . sprintf( '%02d', 30 - $index * 3 ) ) ),
			'category'    => $row[1],
			'excerpt'     => $row[2],
			'image_id'    => (int) ( $samples[ 1 + $index % 2 ] ?? 0 ),
			'source_url'  => 0 === $index % 2 ? 'https://www.linkedin.com/posts/example' : '',
			'source_host' => 0 === $index % 2 ? 'linkedin.com' : '',
		);
	}
	return array_slice( $items, 0, max( 1, (int) ( $attributes['count'] ?? 3 ) ) );
}
add_filter( 'chargenet_post_grid_items', 'chargenet_gallery_post_items', 10, 2 );
