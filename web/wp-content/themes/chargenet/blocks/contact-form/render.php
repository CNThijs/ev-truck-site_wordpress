<?php
/**
 * Contact form. Variables: $attributes. The form itself is printed by inc/forms/render.php; view.js enhances it.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$chargenet_title_id = chargenet_section_title_id( $attributes );

chargenet_section_open(
	$attributes,
	array(
		'name'       => 'contact-form',
		'container'  => 'narrow',
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="form-section form-section--contact-form stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<div class="cn-form-wrap" data-reveal>
		<?php chargenet_render_contact_form(); ?>
	</div>
</div>
<?php
chargenet_section_close();
