<?php
/**
 * Bento card. Variables: $attributes, $content (text and lists), $block (context).
 *
 * Keep the markup in sync with edit() in index.js. A photo is decoration (the title is the text), so it has an empty alt.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_title = trim( wp_strip_all_tags( (string) ( $attributes['title'] ?? '' ) ) );
if ( '' === $chargenet_title && '' === trim( $content ) ) {
	return;
}

$chargenet_tone  = in_array( $attributes['tone'] ?? '', array( 'light', 'dark', 'accent', 'photo' ), true ) ? $attributes['tone'] : 'light';
$chargenet_level = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 1 ) );
$chargenet_url   = (string) ( $attributes['url'] ?? '' );
$chargenet_label = trim( wp_strip_all_tags( (string) ( $attributes['linkLabel'] ?? '' ) ) );
$chargenet_icon  = (string) ( $attributes['icon'] ?? '' );
$chargenet_photo = '';
if ( 'photo' === $chargenet_tone ) {
	$chargenet_img = chargenet_image(
		(int) ( $attributes['imageId'] ?? 0 ),
		array(
			'size'       => 'large',
			'sizes'      => '(min-width: 64rem) 50vw, 100vw',
			'class'      => 'bento-item__img',
			'decorative' => true,
		)
	);
	if ( '' !== $chargenet_img ) {
		// Decoration far down the page on most screens: with JavaScript the photo starts loading 300px before it comes into view
		// (assets/src/js/lazy.js) instead of competing with the hero image for bandwidth; without JavaScript the <noscript> copy loads.
		$chargenet_deferred = (string) preg_replace( '/\s(src|srcset|sizes)=/', ' data-lazy-$1=', $chargenet_img );
		$chargenet_photo    = $chargenet_deferred . '<noscript>' . $chargenet_img . '</noscript>';
	}
}
?>
<article class="bento-item bento-item--<?php echo esc_attr( $chargenet_tone ); ?>" data-reveal>
	<?php if ( '' !== $chargenet_photo ) : ?>
		<div class="bento-item__media" aria-hidden="true"><?php echo $chargenet_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes the image markup. ?></div>
	<?php endif; ?>
	<div class="bento-item__inner">
		<?php if ( '' !== $chargenet_icon ) : ?>
			<span class="bento-item__icon"><?php chargenet_the_icon( $chargenet_icon ); ?></span>
		<?php endif; ?>
		<?php if ( '' !== $chargenet_title ) : ?>
			<h<?php echo (int) $chargenet_level; ?> class="bento-item__title">
				<?php if ( '' !== $chargenet_url ) : ?>
					<a class="bento-item__title-link" href="<?php echo esc_url( $chargenet_url ); ?>"><?php echo esc_html( $chargenet_title ); ?></a>
				<?php else : ?>
					<?php echo esc_html( $chargenet_title ); ?>
				<?php endif; ?>
			</h<?php echo (int) $chargenet_level; ?>>
		<?php endif; ?>
		<div class="bento-item__body"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered inner blocks. ?></div>
		<?php // The title is the card's one link and covers the whole card; the label is a visual cue only. ?>
		<?php if ( '' !== $chargenet_url && '' !== $chargenet_label ) : ?>
			<span class="link-arrow bento-item__link" aria-hidden="true"><?php echo esc_html( $chargenet_label ); ?></span>
		<?php endif; ?>
	</div>
</article>
