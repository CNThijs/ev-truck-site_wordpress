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
		'labelledby' => $chargenet_title_id,
	)
);
?>
<div class="form-section form-section--contact-form stack">
	<?php chargenet_section_header( $attributes, $chargenet_title_id ); ?>
	<div class="contact-split">
		<aside class="contact-aside stack" data-reveal>
			<h2 class="contact-aside__title"><?php esc_html_e( 'Get in Touch', 'chargenet' ); ?></h2>
			<ul class="contact-aside__list" role="list">
				<li>
					<span class="contact-aside__icon"><?php chargenet_the_icon( 'mail' ); ?></span>
					<a href="mailto:info@chargenet.energy">info@chargenet.energy</a>
				</li>
				<li>
					<span class="contact-aside__icon"><?php chargenet_the_icon( 'linkedin' ); ?></span>
					<a href="https://www.linkedin.com/company/chargenet-eu/" target="_blank" rel="noopener">
						<?php esc_html_e( 'Connect with Us', 'chargenet' ); ?>
						<span class="visually-hidden"><?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span>
					</a>
				</li>
				<li>
					<span class="contact-aside__icon"><?php chargenet_the_icon( 'map-pin' ); ?></span>
					<address>
						<?php esc_html_e( 'Industrial Park Kleefsewaard', 'chargenet' ); ?><br>
						Westervoortsedijk 73<br>
						<?php esc_html_e( '6827 AV, Arnhem, The Netherlands', 'chargenet' ); ?>
					</address>
				</li>
			</ul>
		</aside>
		<div class="cn-form-wrap contact-card" data-reveal>
			<?php chargenet_render_contact_form(); ?>
		</div>
	</div>
</div>
<?php
chargenet_section_close();
