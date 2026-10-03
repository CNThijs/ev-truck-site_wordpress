<?php
/**
 * Rich text. Variables: $attributes, $content (rendered inner blocks).
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

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'rich-text',
		'container'  => 'narrow',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="rich-text stack">
	<?php chargenet_heading( $chargenet_heading, (int) ( $attributes['headingLevel'] ?? 2 ), $chargenet_title_id ); ?>
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
</div>
<?php
chargenet_section_close();
