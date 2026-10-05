<?php
/**
 * Team member. Variables: $attributes, $block (context).
 *
 * Keep the markup in sync with edit() in index.js. Photos are portraits, so the alt text is empty unless the
 * editor wrote one (the name is printed right beside it). Without a photo the initials are shown.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_name = trim( wp_strip_all_tags( (string) ( $attributes['name'] ?? '' ) ) );
if ( '' === $chargenet_name ) {
	return;
}

$chargenet_level    = max( 3, min( 4, (int) ( $block->context['chargenet/headingLevel'] ?? 2 ) + 1 ) );
$chargenet_role     = trim( wp_strip_all_tags( (string) ( $attributes['role'] ?? '' ) ) );
$chargenet_bio      = preg_split( '/\R+/', trim( (string) ( $attributes['bio'] ?? '' ) ), -1, PREG_SPLIT_NO_EMPTY );
$chargenet_email    = sanitize_email( (string) ( $attributes['email'] ?? '' ) );
$chargenet_linkedin = (string) ( $attributes['linkedin'] ?? '' );
$chargenet_photo    = chargenet_image(
	(int) ( $attributes['imageId'] ?? 0 ),
	array(
		'size'       => 'medium_large',
		'sizes'      => '(min-width: 64rem) 22vw, (min-width: 40rem) 33vw, 80vw',
		'class'      => 'person__img',
		'alt'        => (string) ( $attributes['imageAlt'] ?? '' ),
		'decorative' => '' === trim( (string) ( $attributes['imageAlt'] ?? '' ) ),
	)
);

$chargenet_initials = '';
$chargenet_words    = preg_split( '/\s+/', $chargenet_name );
foreach ( array_slice( false === $chargenet_words ? array() : $chargenet_words, 0, 2 ) as $chargenet_part ) {
	$chargenet_initials .= mb_strtoupper( mb_substr( $chargenet_part, 0, 1 ) );
}
?>
<li class="person" data-reveal>
	<figure class="person__photo">
		<?php if ( '' !== $chargenet_photo ) : ?>
			<?php echo $chargenet_photo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?>
		<?php else : ?>
			<span class="person__initials" aria-hidden="true"><?php echo esc_html( $chargenet_initials ); ?></span>
		<?php endif; ?>
	</figure>
	<h<?php echo (int) $chargenet_level; ?> class="person__name"><?php echo esc_html( $chargenet_name ); ?></h<?php echo (int) $chargenet_level; ?>>
	<?php if ( '' !== $chargenet_role ) : ?>
		<p class="person__role"><?php echo esc_html( $chargenet_role ); ?></p>
	<?php endif; ?>
	<?php if ( $chargenet_bio ) : ?>
		<div class="person__bio stack">
			<?php foreach ( $chargenet_bio as $chargenet_line ) : ?>
				<p><?php echo esc_html( $chargenet_line ); ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<?php if ( '' !== $chargenet_email || '' !== $chargenet_linkedin ) : ?>
		<ul class="person__links" role="list">
			<?php if ( '' !== $chargenet_email ) : ?>
				<li>
					<a class="person__link" href="<?php echo esc_url( 'mailto:' . $chargenet_email ); ?>">
						<?php chargenet_the_icon( 'mail' ); ?>
						<span class="visually-hidden">
							<?php
							/* translators: %s: person's name. */
							echo esc_html( sprintf( __( 'Email %s', 'chargenet' ), $chargenet_name ) );
							?>
						</span>
					</a>
				</li>
			<?php endif; ?>
			<?php if ( '' !== $chargenet_linkedin ) : ?>
				<li>
					<a class="person__link" href="<?php echo esc_url( $chargenet_linkedin ); ?>" target="_blank" rel="noopener">
						<?php chargenet_the_icon( 'linkedin' ); ?>
						<span class="visually-hidden">
							<?php
							/* translators: %s: person's name. */
							echo esc_html( sprintf( __( '%s on LinkedIn (opens in a new tab)', 'chargenet' ), $chargenet_name ) );
							?>
						</span>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</li>
