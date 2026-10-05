<?php
/**
 * Original source of a post: an optional external URL (article, LinkedIn post, report) a news item is based on.
 * Editors set it in the "Original source" box on the post screen; readers get a "Read the original" link under the post.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_SOURCE_META = '_chargenet_source_url';

/**
 * Register the meta key (also available in the REST API for the blog import).
 */
function chargenet_register_source_meta(): void {
	register_post_meta(
		'post',
		CHARGENET_SOURCE_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
		)
	);
}
add_action( 'init', 'chargenet_register_source_meta' );

/**
 * Add the "Original source" box to the post screen.
 */
function chargenet_source_meta_box(): void {
	add_meta_box( 'chargenet-source', __( 'Original source', 'chargenet' ), 'chargenet_source_meta_box_render', 'post', 'side' );
}
add_action( 'add_meta_boxes', 'chargenet_source_meta_box' );

/**
 * Render the box.
 *
 * @param WP_Post $post Post being edited.
 */
function chargenet_source_meta_box_render( WP_Post $post ): void {
	wp_nonce_field( 'chargenet_source', 'chargenet_source_nonce' );
	printf(
		'<p><label for="chargenet-source-url">%1$s</label><input type="url" id="chargenet-source-url" name="chargenet_source_url" value="%2$s" class="widefat" placeholder="https://"></p><p class="description">%3$s</p>',
		esc_html__( 'Source URL', 'chargenet' ),
		esc_attr( (string) get_post_meta( $post->ID, CHARGENET_SOURCE_META, true ) ),
		esc_html__( 'Link to the article, post or report this news item is based on. Readers see it as "Read the original on …" under the post.', 'chargenet' )
	);
}

/**
 * Save the box.
 *
 * @param int $post_id Post id.
 */
function chargenet_source_meta_save( int $post_id ): void {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$nonce = isset( $_POST['chargenet_source_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['chargenet_source_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'chargenet_source' ) ) {
		return;
	}
	$url = isset( $_POST['chargenet_source_url'] ) ? esc_url_raw( wp_unslash( $_POST['chargenet_source_url'] ), array( 'http', 'https' ) ) : '';
	if ( '' === $url ) {
		delete_post_meta( $post_id, CHARGENET_SOURCE_META );
	} else {
		update_post_meta( $post_id, CHARGENET_SOURCE_META, $url );
	}
}
add_action( 'save_post_post', 'chargenet_source_meta_save' );

/**
 * Copy the source URL into a new translation (Polylang). It is copied once, not kept in sync.
 *
 * @param string[] $keys Meta keys Polylang copies.
 * @param bool     $sync True when called for synchronisation of an existing translation.
 * @return string[]
 */
function chargenet_source_meta_copy( array $keys, bool $sync ): array {
	if ( ! $sync ) {
		$keys[] = CHARGENET_SOURCE_META;
	}
	return $keys;
}
add_filter( 'pll_copy_post_metas', 'chargenet_source_meta_copy', 10, 2 );

/**
 * Source URL and site name (for example linkedin.com) of a post, or null without a valid http(s) source.
 *
 * @param int $post_id Post id.
 * @return array{url: string, host: string}|null
 */
function chargenet_post_source( int $post_id ): ?array {
	$url  = esc_url_raw( (string) get_post_meta( $post_id, CHARGENET_SOURCE_META, true ), array( 'http', 'https' ) );
	$host = '' !== $url ? preg_replace( '/^www\./', '', (string) wp_parse_url( $url, PHP_URL_HOST ) ) : '';
	return '' !== $url && '' !== $host ? array(
		'url'  => $url,
		'host' => (string) $host,
	) : null;
}

/**
 * Print the "Read the original" link for a post, if it has a source URL. Opens in a new tab and says so.
 *
 * @param int $post_id Post id (default: current post).
 */
function chargenet_the_post_source( int $post_id = 0 ): void {
	$source = chargenet_post_source( $post_id ? $post_id : (int) get_the_ID() );
	if ( ! $source ) {
		return;
	}
	?>
	<aside class="post-source" aria-label="<?php esc_attr_e( 'Original source', 'chargenet' ); ?>">
		<a class="link-arrow" href="<?php echo esc_url( $source['url'] ); ?>" target="_blank" rel="noopener">
			<?php
			/* translators: %s: website of the original source, for example linkedin.com. */
			echo esc_html( sprintf( __( 'Read the original on %s', 'chargenet' ), $source['host'] ) );
			?>
			<span class="visually-hidden"><?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span>
		</a>
	</aside>
	<?php
}
