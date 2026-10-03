<?php
/**
 * Site header.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'chargenet' ); ?></a>
<header class="site-header is-dark" data-header>
	<div class="container site-header__inner">
		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<img src="<?php echo esc_url( CHARGENET_URI . '/assets/img/chargenet-logo.svg' ); ?>" alt="<?php bloginfo( 'name' ); ?>" width="1169" height="321">
		</a>
		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-panel" data-nav-toggle>
			<span class="visually-hidden"><?php esc_html_e( 'Menu', 'chargenet' ); ?></span>
			<span class="nav-toggle__bars" aria-hidden="true"></span>
		</button>
		<div class="site-header__panel" id="site-panel" data-nav-panel>
			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'chargenet' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
			<div class="site-header__actions">
				<nav class="utility-nav" aria-label="<?php esc_attr_e( 'Account', 'chargenet' ); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'utility',
							'container'      => false,
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</nav>
				<?php chargenet_language_switcher(); ?>
				<?php chargenet_header_cta(); ?>
			</div>
		</div>
	</div>
</header>
<main id="main" class="site-main">
