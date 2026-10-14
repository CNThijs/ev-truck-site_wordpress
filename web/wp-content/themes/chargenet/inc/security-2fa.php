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
 * Keep such a user on the profile page. The same applies to admin-ajax.php and admin-post.php (both run admin_init and
 * were a way around the gate: an administrator's export action is reachable through admin-post.php) and, below, to the
 * REST API. Only the public contact form handler is let through, so a logged-in tester can still send a form.
 */
function chargenet_2fa_gate(): void {
	$user = wp_get_current_user();
	if ( ! $user->exists() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ! chargenet_2fa_missing( $user ) ) {
		return;
	}
	global $pagenow;
	$action = isset( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only compared with a fixed value.
	if ( 'profile.php' === $pagenow || 'async-upload.php' === $pagenow || ( 'admin-post.php' === $pagenow && 'chargenet_form' === $action ) ) {
		return;
	}
	if ( wp_doing_ajax() ) {
		wp_send_json_error( array( 'message' => __( 'Two-factor authentication is required.', 'chargenet' ) ), 403 );
	}
	wp_safe_redirect( admin_url( 'profile.php#two-factor-options' ) );
	exit;
}
add_action( 'admin_init', 'chargenet_2fa_gate' );

/**
 * The REST API takes cookie logins too, so the gate has to cover it: such a user could otherwise edit content or read
 * submissions without ever setting up a second factor. The Two Factor plugin's own routes (needed to set it up) and
 * the public routes of this theme stay open.
 *
 * @param mixed           $result  Response so far.
 * @param WP_REST_Server  $server  Server.
 * @param WP_REST_Request $request Request.
 * @return mixed
 */
function chargenet_2fa_rest_gate( $result, $server, $request ) {
	$user = wp_get_current_user();
	if ( $user->exists() && chargenet_2fa_missing( $user ) ) {
		$route = (string) $request->get_route();
		if ( 0 !== strpos( $route, '/two-factor/' ) && 0 !== strpos( $route, '/chargenet/v1/' ) ) {
			return new WP_Error( 'chargenet_2fa_required', __( 'Two-factor authentication is required.', 'chargenet' ), array( 'status' => 403 ) );
		}
	}
	return $result;
}
add_filter( 'rest_pre_dispatch', 'chargenet_2fa_rest_gate', 10, 3 );

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
