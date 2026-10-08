<?php
/**
 * Accessibility fixes for content that comes from the editor or the importer: tables that scroll sideways on small
 * screens must be reachable with the keyboard and named, and a table header cell must not be empty (axe rules
 * scrollable-region-focusable, empty-table-header). Checked by bin/check-a11y.mjs; manual tests in docs/accessibility.md.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Make each table figure a focusable, named region ("Table 1", "Table 2", ... so that the names are unique) and give
 * empty header cells a hidden label.
 *
 * @param string              $html  Block HTML.
 * @param array<string,mixed> $block Block.
 * @return string
 */
function chargenet_table_accessibility( string $html, array $block ): string {
	static $count = 0;
	if ( 'core/table' !== ( $block['blockName'] ?? '' ) || false === strpos( $html, '<figure' ) ) {
		return $html;
	}
	++$count;
	$label = sprintf(
		/* translators: %d: number of the table on the page. */
		__( 'Table %d', 'chargenet' ),
		$count
	);
	$html = (string) preg_replace( '/<figure /', '<figure tabindex="0" role="region" aria-label="' . esc_attr( $label ) . '" ', $html, 1 );
	return (string) preg_replace( '/<th(\s[^>]*)?>(\s|&nbsp;)*<\/th>/', '<th$1><span class="screen-reader-text">' . esc_html__( 'Item', 'chargenet' ) . '</span></th>', $html );
}
add_filter( 'render_block', 'chargenet_table_accessibility', 10, 2 );
