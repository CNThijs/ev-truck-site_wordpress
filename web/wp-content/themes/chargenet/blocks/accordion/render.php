<?php
/**
 * Accordion (FAQ). Variables: $attributes, $content (rendered items), $block.
 *
 * Keep the markup in sync with edit() in index.js. Optional FAQPage structured data is built from the items.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Empty state: no questions, nothing to show.
if ( '' === trim( $content ) ) {
	return;
}

$chargenet_variant  = 'with-aside' === ( $attributes['variant'] ?? '' ) ? 'with-aside' : 'single';
$chargenet_title_id = chargenet_section_title_id( $attributes );
$chargenet_aside_h  = trim( wp_strip_all_tags( (string) ( $attributes['asideHeading'] ?? '' ) ) );
$chargenet_aside_t  = (string) ( $attributes['asideText'] ?? '' );
$chargenet_aside_l  = trim( wp_strip_all_tags( (string) ( $attributes['asideLabel'] ?? '' ) ) );
$chargenet_aside_u  = (string) ( $attributes['asideUrl'] ?? '' );
$chargenet_has_side = 'with-aside' === $chargenet_variant && ( '' !== $chargenet_aside_h || '' !== trim( wp_strip_all_tags( $chargenet_aside_t ) ) );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'accordion',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="accordion accordion--<?php echo esc_attr( $chargenet_variant ); ?> stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<div class="accordion__layout<?php echo $chargenet_has_side ? ' accordion__layout--aside' : ''; ?>">
		<div class="accordion__list" data-reveal-group>
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?>
		</div>
		<?php if ( $chargenet_has_side ) : ?>
			<aside class="accordion__aside card stack">
				<?php chargenet_heading( $chargenet_aside_h, min( 4, (int) ( $attributes['headingLevel'] ?? 2 ) + 1 ), '', 'accordion__aside-title' ); ?>
				<?php if ( '' !== trim( wp_strip_all_tags( $chargenet_aside_t ) ) ) : ?>
					<p><?php echo wp_kses_post( $chargenet_aside_t ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $chargenet_aside_l && '' !== $chargenet_aside_u ) : ?>
					<p><a class="btn btn--primary" href="<?php echo esc_url( $chargenet_aside_u ); ?>"><?php echo esc_html( $chargenet_aside_l ); ?></a></p>
				<?php endif; ?>
			</aside>
		<?php endif; ?>
	</div>
</div>
<?php
if ( ! empty( $attributes['faqSchema'] ) ) {
	$chargenet_entities = array();
	foreach ( $block->inner_blocks as $chargenet_item ) {
		$chargenet_question = trim( wp_strip_all_tags( (string) ( $chargenet_item->attributes['question'] ?? '' ) ) );
		$chargenet_answer   = '';
		foreach ( $chargenet_item->inner_blocks as $chargenet_part ) {
			$chargenet_answer .= $chargenet_part->render();
		}
		$chargenet_answer = trim( wp_strip_all_tags( $chargenet_answer ) );
		if ( '' !== $chargenet_question && '' !== $chargenet_answer ) {
			$chargenet_entities[] = array(
				'@type'          => 'Question',
				'name'           => $chargenet_question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $chargenet_answer,
				),
			);
		}
	}
	if ( $chargenet_entities ) {
		wp_print_inline_script_tag(
			(string) wp_json_encode(
				array(
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $chargenet_entities,
				),
				JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			),
			array( 'type' => 'application/ld+json' )
		);
	}
}
chargenet_section_close();
