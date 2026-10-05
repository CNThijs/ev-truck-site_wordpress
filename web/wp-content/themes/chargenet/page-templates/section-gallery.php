<?php
/**
 * Section Gallery: every section and variant with sample content. Admin only (see inc/gallery.php).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CHARGENET_DIR . '/inc/gallery-samples.php';

$chargenet_gallery = chargenet_gallery_sections();

get_header();
?>
<section class="section is-dark gallery__intro" data-space-bottom="md">
	<div class="container stack">
		<p class="t-eyebrow">Admin only</p>
		<p class="t-hero">Section Gallery</p>
		<p class="t-lead">Every section with every variant and sample content, rendered by the same code as a real page. Not public, not indexed. Sample images are generated once and stored in the media library as “ChargeNet gallery sample”.</p>
		<?php if ( ! chargenet_gallery_samples() ) : ?>
			<p><strong>PHP GD is not available, so sample images are missing and image slots show their empty state.</strong></p>
		<?php endif; ?>
		<nav aria-label="Sections" class="cluster">
			<?php foreach ( $chargenet_gallery as $chargenet_entry ) : ?>
				<a class="btn btn--secondary btn--sm" href="#<?php echo esc_attr( $chargenet_entry['id'] ); ?>"><?php echo esc_html( $chargenet_entry['title'] ); ?></a>
			<?php endforeach; ?>
		</nav>
	</div>
</section>

<?php foreach ( $chargenet_gallery as $chargenet_entry ) : ?>
	<div class="gallery__group" id="<?php echo esc_attr( $chargenet_entry['id'] ); ?>">
		<div class="gallery__label is-paper">
			<div class="container">
				<h2 class="gallery__name"><?php echo esc_html( $chargenet_entry['title'] ); ?> <code>chargenet/<?php echo esc_html( $chargenet_entry['id'] ); ?></code></h2>
				<p><?php echo esc_html( $chargenet_entry['description'] ); ?></p>
			</div>
		</div>
		<?php foreach ( $chargenet_entry['variants'] as $chargenet_label => $chargenet_markup ) : ?>
			<div class="gallery__variant">
				<p class="gallery__variant-label"><span class="visually-hidden"><?php echo esc_html( $chargenet_entry['title'] ); ?>: </span><?php echo esc_html( $chargenet_label ); ?></p>
				<?php
				$chargenet_html = do_blocks( $chargenet_markup );
				echo '' !== trim( $chargenet_html ) ? $chargenet_html : '<p class="container gallery__empty">Renders nothing (empty state).</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered blocks.
				?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
<?php
get_footer();
