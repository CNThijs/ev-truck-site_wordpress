<?php
/**
 * Statement. Variables: $attributes.
 *
 * One large sentence. The text is plain HTML paragraph text; the "scrub-words" animation only wraps words (and reverts), so
 * the full sentence is always in the page. Keep the markup in sync with edit() in index.js.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_text = (string) ( $attributes['text'] ?? '' );

// Empty state: nothing to show without text.
if ( '' === trim( wp_strip_all_tags( $chargenet_text ) ) ) {
	return;
}

chargenet_section_open(
	$attributes,
	array(
		'name'      => 'statement',
		'container' => 'wide',
	)
);
?>
<p class="statement__text" data-scrub>
<?php
echo wp_kses(
	$chargenet_text,
	array(
		'strong' => array(),
		'em'     => array(),
		'br'     => array(),
	)
);
?>
</p>
<?php
chargenet_section_close();
