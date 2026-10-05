<?php
/**
 * Section blocks: category, automatic registration from /blocks, editor restrictions, patterns.
 *
 * Sources live in blocks/<name>/ and are built by @wordpress/scripts into build/blocks/<name>/.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CHARGENET_DIR . '/inc/section.php';

/**
 * Add the "ChargeNet Sections" block category, first in the inserter.
 *
 * @param array<int, array<string, string>> $categories Block categories.
 * @return array<int, array<string, string>>
 */
function chargenet_block_category( array $categories ): array {
	array_unshift(
		$categories,
		array(
			'slug'  => 'chargenet-sections',
			'title' => __( 'ChargeNet Sections', 'chargenet' ),
		)
	);
	return $categories;
}
add_filter( 'block_categories_all', 'chargenet_block_category' );

/**
 * Register every built block found in build/blocks.
 */
function chargenet_register_blocks(): void {
	$files = glob( CHARGENET_DIR . '/build/blocks/*/block.json' );
	foreach ( false === $files ? array() : $files as $file ) {
		$block = register_block_type( dirname( $file ) );
		if ( $block ) {
			// Editor strings: languages/chargenet-<locale>-<handle>.json, made by npm run translations.
			wp_set_script_translations( generate_block_asset_handle( $block->name, 'editorScript' ), 'chargenet', CHARGENET_DIR . '/languages' );
		}
	}
}
add_action( 'init', 'chargenet_register_blocks' );

/**
 * Give every top-level section block the shared attributes. A block's own definition of the same key wins.
 *
 * @param array<string, mixed> $metadata block.json contents.
 * @return array<string, mixed>
 */
function chargenet_shared_section_attributes( array $metadata ): array {
	$is_section = 0 === strpos( (string) ( $metadata['name'] ?? '' ), 'chargenet/' ) && empty( $metadata['parent'] ) && empty( $metadata['ancestor'] );
	if ( $is_section ) {
		$metadata['attributes'] = array_merge( chargenet_section_attributes(), $metadata['attributes'] ?? array() );
	}
	return $metadata;
}
add_filter( 'block_type_metadata', 'chargenet_shared_section_attributes' );

/**
 * Pages: approved sections plus the text blocks sections nest (only inside sections). Other post types keep the normal editor.
 *
 * @param bool|string[]           $allowed Allowed blocks.
 * @param WP_Block_Editor_Context $context Editor context.
 * @return bool|string[]
 */
function chargenet_allowed_blocks( $allowed, WP_Block_Editor_Context $context ) {
	if ( empty( $context->post ) || 'page' !== $context->post->post_type ) {
		return $allowed;
	}
	$registry = WP_Block_Type_Registry::get_instance();
	$sections = array_keys(
		array_filter(
			$registry->get_all_registered(),
			static fn( WP_Block_Type $type ): bool => 0 === strpos( $type->name, 'chargenet/' ) && empty( $type->parent )
		)
	);
	// Child blocks (e.g. chargenet/button) are placed by their parent section, not the root inserter.
	$children = array_keys(
		array_filter(
			$registry->get_all_registered(),
			static fn( WP_Block_Type $type ): bool => 0 === strpos( $type->name, 'chargenet/' ) && ! empty( $type->parent )
		)
	);
	// core/* text blocks are only for use inside sections: give them a parent rule so the inserter
	// never offers them at the root of a page. Runs before the editor serialises block definitions.
	$parents = array_merge( $sections, $children );
	foreach ( array( 'core/paragraph', 'core/heading', 'core/list', 'core/quote' ) as $name ) {
		$type = $registry->get_registered( $name );
		if ( $type ) {
			$type->parent = 'core/paragraph' === $name ? array_merge( $parents, array( 'core/quote' ) ) : $parents;
		}
	}
	return array_values( array_merge( $sections, $children, array( 'core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/quote' ) ) );
}
add_filter( 'allowed_block_types_all', 'chargenet_allowed_blocks', 10, 2 );

/**
 * Pattern category for the starter pages (patterns live in /patterns, registered by WordPress).
 */
function chargenet_pattern_category(): void {
	register_block_pattern_category(
		'chargenet-pages',
		array( 'label' => __( 'ChargeNet pages', 'chargenet' ) )
	);
}
add_action( 'init', 'chargenet_pattern_category' );

/**
 * Hook for inlining critical CSS later. Return CSS from the filter; empty prints nothing.
 */
function chargenet_print_critical_css(): void {
	$css = (string) apply_filters( 'chargenet_critical_css', '' );
	if ( '' !== $css ) {
		echo '<style id="chargenet-critical">' . wp_strip_all_tags( $css ) . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tags stripped.
	}
}
add_action( 'wp_head', 'chargenet_print_critical_css', 2 );

/**
 * Remind developers to build the blocks (build/ is not committed).
 */
function chargenet_missing_build_notice(): void {
	if ( ! is_dir( CHARGENET_DIR . '/build/blocks' ) && current_user_can( 'manage_options' ) ) {
		echo '<div class="notice notice-warning"><p>' . esc_html__( 'ChargeNet section blocks are not built. Run npm run build (or npm run dev) in the repository.', 'chargenet' ) . '</p></div>';
	}
}
add_action( 'admin_notices', 'chargenet_missing_build_notice' );
