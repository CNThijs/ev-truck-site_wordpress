<?php
/**
 * Locations list. Variables: $attributes. Server-rendered from the Locations content type (chargenet_locations_items()).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_items = chargenet_locations_items( (int) ( $attributes['count'] ?? 50 ) );

// Empty state: no locations yet, nothing to show.
if ( ! $chargenet_items ) {
	return;
}

$chargenet_title_id = chargenet_section_title_id( $attributes );
$chargenet_level    = min( 4, (int) ( $attributes['headingLevel'] ?? 2 ) + 1 );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'locations-list',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="locations stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<ul class="locations__list" role="list" data-reveal-group>
		<?php foreach ( $chargenet_items as $chargenet_item ) : ?>
			<li class="location" data-reveal>
				<h<?php echo (int) $chargenet_level; ?> class="location__name"><?php echo esc_html( $chargenet_item['name'] ); ?></h<?php echo (int) $chargenet_level; ?>>
				<?php if ( '' !== $chargenet_item['street'] . $chargenet_item['postal'] . $chargenet_item['city'] ) : ?>
					<address class="location__address">
						<?php if ( '' !== $chargenet_item['street'] ) : ?>
							<?php echo esc_html( $chargenet_item['street'] ); ?><br>
						<?php endif; ?>
						<?php echo esc_html( trim( $chargenet_item['postal'] . ' ' . $chargenet_item['city'] ) ); ?>
					</address>
				<?php endif; ?>
				<?php if ( '' !== $chargenet_item['access'] . $chargenet_item['details'] ) : ?>
					<dl class="location__facts">
						<?php if ( '' !== $chargenet_item['access'] ) : ?>
							<div><dt><?php esc_html_e( 'Access', 'chargenet' ); ?></dt><dd><?php echo esc_html( $chargenet_item['access'] ); ?></dd></div>
						<?php endif; ?>
						<?php if ( '' !== $chargenet_item['details'] ) : ?>
							<div><dt><?php esc_html_e( 'Charge points', 'chargenet' ); ?></dt><dd><?php echo esc_html( $chargenet_item['details'] ); ?></dd></div>
						<?php endif; ?>
					</dl>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
chargenet_section_close();
