<?php
/**
 * Two-factor authentication for administrators and editors, on top of the free Two Factor plugin (which has no way to
 * require it). Allowed methods: authenticator app (TOTP) and backup codes; the email method is left out so that login
 * does not depend on the mail setup. A user in a required role without a second factor can only reach their own
 * profile until one is set up. Does nothing when the plugin is not active (DDEV, where it stays off).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Roles that must use two-factor authentication.
 *
 * @return string[]
 */
function chargenet_2fa_roles(): array {
	return array( 'administrator', 'editor' );
}

/**
 * Only the authenticator app and backup codes.
 *
 * @param array<string,mixed> $providers Provider classes by key.
 * @return array<string,mixed>
 */
function chargenet_2fa_providers( $providers ) {
	return array_intersect_key( (array) $providers, array_flip( array( 'Two_Factor_Totp', 'Two_Factor_Backup_Codes' ) ) );
}
add_filter( 'two_factor_providers', 'chargenet_2fa_providers' );

/**
 * Whether this user is in a required role and has no second factor yet.
 *
 * @param WP_User $user User.
 */
function chargenet_2fa_missing( WP_User $user ): bool {
	return class_exists( 'Two_Factor_Core' )
		&& array() !== array_intersect( chargenet_2fa_roles(), $user->roles )
		&& ! Two_Factor_Core::is_user_using_two_factor( $user->ID );
}

/**
 * Keep such a user on the profile page.
 */
function chargenet_2fa_gate(): void {
	$user = wp_get_current_user();
	if ( ! $user->exists() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! chargenet_2fa_missing( $user ) ) {
		return;
	}
	global $pagenow;
	if ( in_array( $pagenow, array( 'profile.php', 'admin-post.php', 'async-upload.php' ), true ) ) {
		return;
	}
	wp_safe_redirect( admin_url( 'profile.php#two-factor-options' ) );
	exit;
}
add_action( 'admin_init', 'chargenet_2fa_gate' );

/**
 * Tell the user why.
 */
function chargenet_2fa_notice(): void {
	if ( chargenet_2fa_missing( wp_get_current_user() ) ) {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
			esc_html__( 'Two-factor authentication is required.', 'chargenet' ),
			esc_html__( 'Scroll to "Two-Factor Options", set up an authenticator app and save backup codes. Until then you can only use this page.', 'chargenet' )
		);
	}
}
add_action( 'admin_notices', 'chargenet_2fa_notice' );
