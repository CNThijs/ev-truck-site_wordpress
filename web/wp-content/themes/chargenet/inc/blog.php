<?php
/**
 * News posts: author reference, comments off, reading time, table of contents, share links, structured data author,
 * the RSS feed and the image alt-text check. The templates are single.php, home.php, category.php and search.php.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_AUTHOR_META = '_chargenet_author';

// Author reference ---------------------------------------------------------------------------------------------.

/**
 * Register the author meta key (also in the REST API, for imports).
 */
function chargenet_register_author_meta(): void {
	register_post_meta(
		'post',
		CHARGENET_AUTHOR_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
		)
	);
}
add_action( 'init', 'chargenet_register_author_meta' );

/**
 * Who wrote the post: the author reference, or "ChargeNet" when none is set. The authors are organisations (for
 * example a partner that wrote a guest article), not WordPress users.
 *
 * @param int $post_id Post id (default: current post).
 */
function chargenet_post_author( int $post_id = 0 ): string {
	$name = trim( (string) get_post_meta( $post_id ? $post_id : (int) get_the_ID(), CHARGENET_AUTHOR_META, true ) );
	return '' !== $name ? $name : 'ChargeNet';
}

/**
 * Box in the post screen sidebar.
 */
function chargenet_author_meta_box(): void {
	add_meta_box( 'chargenet-author', __( 'Author', 'chargenet' ), 'chargenet_author_meta_box_render', 'post', 'side' );
}
add_action( 'add_meta_boxes', 'chargenet_author_meta_box' );

/**
 * Print the author field.
 *
 * @param WP_Post $post Post.
 */
function chargenet_author_meta_box_render( WP_Post $post ): void {
	wp_nonce_field( 'chargenet_author', 'chargenet_author_nonce' );
	printf(
		'<p><label for="chargenet_author">%s</label><br><input class="widefat" type="text" id="chargenet_author" name="chargenet_author" value="%s" placeholder="ChargeNet"></p><p class="description">%s</p>',
		esc_html__( 'Author reference', 'chargenet' ),
		esc_attr( (string) get_post_meta( $post->ID, CHARGENET_AUTHOR_META, true ) ),
		esc_html__( 'Shown under the title and in the structured data. Empty: ChargeNet.', 'chargenet' )
	);
}

/**
 * Save the author field.
 *
 * @param int $post_id Post id.
 */
function chargenet_author_meta_save( int $post_id ): void {
	$nonce = isset( $_POST['chargenet_author_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['chargenet_author_nonce'] ) ) : '';
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! wp_verify_nonce( $nonce, 'chargenet_author' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$name = isset( $_POST['chargenet_author'] ) ? sanitize_text_field( wp_unslash( $_POST['chargenet_author'] ) ) : '';
	if ( '' === $name ) {
		delete_post_meta( $post_id, CHARGENET_AUTHOR_META );
	} else {
		update_post_meta( $post_id, CHARGENET_AUTHOR_META, $name );
	}
}
add_action( 'save_post_post', 'chargenet_author_meta_save' );

/**
 * Copy the author into a new translation.
 *
 * @param string[] $keys Meta keys Polylang copies.
 * @param bool     $sync True when synchronising.
 * @return string[]
 */
function chargenet_author_meta_copy( array $keys, bool $sync ): array {
	if ( ! $sync ) {
		$keys[] = CHARGENET_AUTHOR_META;
	}
	return $keys;
}
add_filter( 'pll_copy_post_metas', 'chargenet_author_meta_copy', 10, 2 );

// No comments -------------------------------------------------------------------------------------------------.

add_filter( 'comments_open', '__return_false', 99 );
add_filter( 'pings_open', '__return_false', 99 );
add_filter( 'feed_links_show_comments_feed', '__return_false' );
add_filter( 'comments_array', '__return_empty_array', 99 );

/**
 * Remove comment and trackback support from posts and pages, and the comments menu.
 */
function chargenet_remove_comments(): void {
	foreach ( array( 'post', 'page' ) as $type ) {
		remove_post_type_support( $type, 'comments' );
		remove_post_type_support( $type, 'trackbacks' );
	}
}
add_action( 'init', 'chargenet_remove_comments', 100 );

/**
 * Take the comments screens out of the admin.
 */
function chargenet_remove_comments_menu(): void {
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'chargenet_remove_comments_menu' );

// Reading time, table of contents --------------------------------------------------------------------------.

/**
 * Minutes to read a text (200 words a minute, at least one).
 *
 * @param string $html Text with or without HTML.
 */
function chargenet_reading_minutes( string $html ): int {
	$parts = preg_split( '/\s+/u', trim( wp_strip_all_tags( $html ) ), -1, PREG_SPLIT_NO_EMPTY );
	$words = is_array( $parts ) ? count( $parts ) : 0;
	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * Give the h2 and h3 headings of a post an id and put a table of contents above the text when there are three or
 * more h2 headings. Only on the main text of a single post.
 *
 * @param string $content Post content.
 */
function chargenet_post_toc( string $content ): string {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$items = array();
	$used  = array();
	$count = 0;
	$out   = (string) preg_replace_callback(
		'#<h([23])([^>]*)>(.*?)</h\1>#is',
		static function ( array $m ) use ( &$items, &$used, &$count ): string {
			$text = trim( wp_strip_all_tags( $m[3] ) );
			if ( '' === $text ) {
				return $m[0];
			}
			if ( preg_match( '/\sid="([^"]+)"/', $m[2], $id ) ) {
				$slug = $id[1];
			} else {
				$slug = sanitize_title( $text );
				$slug = '' !== $slug ? $slug : 'section';
				$base = $slug;
				for ( $i = 2; isset( $used[ $slug ] ); $i++ ) {
					$slug = $base . '-' . $i;
				}
				$m[2] .= ' id="' . esc_attr( $slug ) . '"';
			}
			$used[ $slug ] = true;
			if ( '2' === $m[1] ) {
				++$count;
			}
			$items[] = array(
				'level' => (int) $m[1],
				'id'    => $slug,
				'text'  => $text,
			);
			return '<h' . $m[1] . $m[2] . '>' . $m[3] . '</h' . $m[1] . '>';
		},
		$content
	);
	if ( $count < 3 ) {
		return $out;
	}
	$list = '';
	foreach ( $items as $item ) {
		$list .= sprintf( '<li class="post-toc__item post-toc__item--h%d"><a href="#%s">%s</a></li>', $item['level'], esc_attr( $item['id'] ), esc_html( $item['text'] ) );
	}
	$toc = '<nav class="post-toc" aria-labelledby="post-toc-title"><p class="post-toc__title" id="post-toc-title">' . esc_html__( 'In this article', 'chargenet' ) . '</p><ul class="post-toc__list" role="list">' . $list . '</ul></nav>';
	return $toc . $out;
}
add_filter( 'the_content', 'chargenet_post_toc', 9 );

// Share links ---------------------------------------------------------------------------------------------------.

/**
 * Share addresses for a post: LinkedIn and email (links), copy link (a button that view code reveals).
 *
 * @param string $url   Address of the post.
 * @param string $title Title of the post.
 * @return array{linkedin: string, email: string}
 */
function chargenet_share_links( string $url, string $title ): array {
	return array(
		'linkedin' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ),
		'email'    => 'mailto:?subject=' . rawurlencode( $title ) . '&body=' . rawurlencode( $title . "\n" . $url ),
	);
}

// Structured data: the author is an organisation ---------------------------------------------------------------.

/**
 * Rank Math describes the author of a post as the WordPress user (a Person). Our author is the reference on the
 * post (an organisation such as ChargeNet or a partner), so replace it.
 *
 * @param array<string, mixed> $data JSON-LD graph.
 * @return array<string, mixed>
 */
function chargenet_schema_author( array $data ): array {
	if ( ! is_singular( 'post' ) ) {
		return $data;
	}
	foreach ( $data as $key => $node ) {
		if ( is_array( $node ) && 'Person' === ( $node['@type'] ?? '' ) ) {
			unset( $data[ $key ] );
		}
	}
	foreach ( $data as $key => $node ) {
		if ( is_array( $node ) && isset( $node['author'] ) ) {
			$data[ $key ]['author'] = array(
				'@type' => 'Organization',
				'name'  => chargenet_post_author( (int) get_queried_object_id() ),
			);
		}
	}
	return $data;
}
add_filter( 'rank_math/json_ld', 'chargenet_schema_author', 99 );

// RSS feed: image and excerpt first -----------------------------------------------------------------------------.

/**
 * Put the featured image and the source line in the feed items.
 *
 * @param string $content Feed content.
 */
function chargenet_feed_content( string $content ): string {
	$image = get_post_thumbnail_id() ? wp_get_attachment_image( (int) get_post_thumbnail_id(), 'large', false, array( 'alt' => '' ) ) : '';
	return ( '' !== $image ? '<p>' . $image . '</p>' : '' ) . $content;
}
add_filter( 'the_content_feed', 'chargenet_feed_content' );

// Alt text check ---------------------------------------------------------------------------------------------------.

/**
 * Images in a text without alt text (no alt attribute or an empty one).
 *
 * @param string $html Post content.
 * @return int Number of images.
 */
function chargenet_images_without_alt( string $html ): int {
	$missing = 0;
	if ( preg_match_all( '#<img\b[^>]*>#i', $html, $images ) ) {
		foreach ( $images[0] as $tag ) {
			if ( ! preg_match( '/\salt="[^"]+"/', $tag ) ) {
				++$missing;
			}
		}
	}
	return $missing;
}

/**
 * Warn editors about images without alt text in the post they are editing.
 */
function chargenet_alt_notice(): void {
	$screen = get_current_screen();
	if ( ! $screen || 'post' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	$id = (int) get_the_ID();
	if ( ! $id ) {
		return;
	}
	$missing = chargenet_images_without_alt( (string) get_post_field( 'post_content', $id ) );
	if ( $missing > 0 ) {
		printf(
			'<div class="notice notice-warning"><p>%s</p></div>',
			esc_html(
				sprintf(
					/* translators: %d: number of images. */
					_n( '%d image in this post has no alt text. Add a short description of the image, or choose "decorative" if it only decorates.', '%d images in this post have no alt text. Add a short description of each image, or choose "decorative" if it only decorates.', $missing, 'chargenet' ),
					$missing
				)
			)
		);
	}
}
add_action( 'admin_notices', 'chargenet_alt_notice' );

// Block styles and patterns for posts ---------------------------------------------------------------------------.

/**
 * Block styles for the news posts (the look is in assets/src/scss/components/_post.scss; the patterns in
 * patterns/post-*.php use them).
 */
function chargenet_register_post_block_styles(): void {
	register_block_style(
		'core/quote',
		array(
			'name'  => 'pull-quote',
			'label' => __( 'Pull quote', 'chargenet' ),
		)
	);
	foreach ( array(
		'callout'   => __( 'Callout', 'chargenet' ),
		'key-facts' => __( 'Key facts', 'chargenet' ),
		'post-cta'  => __( 'Call to action', 'chargenet' ),
	) as $name => $label ) {
		register_block_style(
			'core/group',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'chargenet_register_post_block_styles' );
