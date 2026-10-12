<?php
/**
 * Accessibility fixes for content that comes from the editor or the importer: a table header cell must not be empty
 * (axe rule empty-table-header). Tables wrap their text and never scroll sideways, so they need no focusable region.
 * Checked by bin/check-a11y.mjs; manual tests in docs/accessibility.md.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give empty table header cells a hidden label.
 *
 * @param string              $html  Block HTML.
 * @param array<string,mixed> $block Block.
 * @return string
 */
function chargenet_table_accessibility( string $html, array $block ): string {
	if ( 'core/table' !== ( $block['blockName'] ?? '' ) ) {
		return $html;
	}
	return (string) preg_replace( '/<th(\s[^>]*)?>(\s|&nbsp;)*<\/th>/', '<th$1><span class="screen-reader-text">' . esc_html__( 'Item', 'chargenet' ) . '</span></th>', $html );
}
add_filter( 'render_block', 'chargenet_table_accessibility', 10, 2 );
