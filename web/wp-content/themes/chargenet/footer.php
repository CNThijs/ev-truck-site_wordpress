<?php
/**
 * Site footer.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>
<footer class="site-footer is-dark">
	<div class="container">
		<div class="site-footer__cta">
			<?php // The brand's tagline, as printed under the logo: it stays English on every language. ?>
			<p class="site-footer__tagline" lang="en">Keep charging <span>ahead.</span></p>
			<div class="cluster">
				<a class="btn btn--primary" href="mailto:info@chargenet.energy"><?php chargenet_the_icon( 'mail' ); ?>info@chargenet.energy</a>
				<a class="btn btn--secondary" href="https://www.linkedin.com/company/chargenet-eu/" target="_blank" rel="noopener"><?php chargenet_the_icon( 'linkedin' ); ?><?php esc_html_e( 'Connect with Us', 'chargenet' ); ?><span class="visually-hidden"> <?php esc_html_e( '(opens in a new tab)', 'chargenet' ); ?></span></a>
			</div>
		</div>
		<div class="site-footer__grid">
			<div class="site-footer__brand stack">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( CHARGENET_URI . '/assets/img/chargenet-logo.svg' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="1169" height="321" loading="lazy">
				</a>
				<p><?php esc_html_e( 'ChargeNet is the platform for shippers & transport companies to collaborate on decarbonisation of road logistics by sharing charging infrastructure at destination. With ChargeNet you can charge at the right price, at the right place, at the right time.', 'chargenet' ); ?></p>
			</div>
			<nav aria-label="<?php esc_attr_e( 'Footer', 'chargenet' ); ?>">
				<h2 class="site-footer__title"><?php esc_html_e( 'Company', 'chargenet' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<div id="footer-contact" class="stack">
				<h2 class="site-footer__title"><?php esc_html_e( 'Get in Touch', 'chargenet' ); ?></h2>
				<address class="site-footer__address">
					<span class="site-footer__icon"><?php chargenet_the_icon( 'map-pin' ); ?></span>
					<span>
						<?php esc_html_e( 'Industrial Park Kleefsewaard', 'chargenet' ); ?><br>
						Westervoortsedijk 73<br>
						<?php esc_html_e( '6827 AV, Arnhem, The Netherlands', 'chargenet' ); ?>
					</span>
				</address>
			</div>
		</div>
		<div class="site-footer__bottom">
			<p>
				<?php
				/* translators: %s: year. */
				printf( esc_html__( '© %s ChargeNet. All rights reserved.', 'chargenet' ), esc_html( gmdate( 'Y' ) ) );
				?>
			</p>
			<nav aria-label="<?php esc_attr_e( 'Legal', 'chargenet' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<button class="site-footer__consent wpconsent-open-preferences" type="button"><?php esc_html_e( 'Cookie settings', 'chargenet' ); ?></button>
			<?php chargenet_language_switcher( __( 'Footer language', 'chargenet' ) ); ?>
		</div>
	</div>
	<?php // Decoration: the name in outline type along the bottom edge. ?>
	<p class="site-footer__mark" aria-hidden="true">ChargeNet</p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
