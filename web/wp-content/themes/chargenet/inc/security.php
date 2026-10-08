<?php
/**
 * WordPress hardening that belongs to the site: no file editing, no XML-RPC, no user enumeration, login throttling,
 * generic login errors, no version leaks. Server settings (permissions, PHP, headers) are checked by
 * bin/check-hardening.php and bin/check-security.mjs; the rest is in docs/security.md.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Theme and plugin editors in the admin (also set DISALLOW_FILE_EDIT in wp-config.php; this is the safety net).
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
	define( 'DISALLOW_FILE_EDIT', true ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- WordPress core constant.
}

// XML-RPC: refuse the request itself (its built-in system.multicall lets one request try many passwords).
if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
	status_header( 403 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	exit( 'XML-RPC is disabled.' );
}
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter(
	'xmlrpc_methods',
	static function (): array {
		return array();
	}
);
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Pingback header and link: the site has no pingbacks.
 *
 * @param array<string,string> $headers Response headers.
 * @return array<string,string>
 */
function chargenet_no_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'chargenet_no_pingback_header' );

/**
 * The REST user list is public by default and reveals login names. Logged-in users with the right to list users keep it.
 *
 * @param array<string,mixed> $endpoints REST endpoints.
 * @return array<string,mixed>
 */
function chargenet_rest_hide_users( array $endpoints ): array {
	if ( ! current_user_can( 'list_users' ) ) {
		foreach ( array_keys( $endpoints ) as $route ) {
			if ( 0 === strpos( $route, '/wp/v2/users' ) ) {
				unset( $endpoints[ $route ] );
			}
		}
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'chargenet_rest_hide_users' );

/**
 * /?author=1 redirects to /author/<login>/ and so reveals the login name: answer 404 instead. The site has no author pages.
 */
function chargenet_block_author_enumeration(): void {
	if ( ! is_admin() && ( isset( $_GET['author'] ) || is_author() ) && ! current_user_can( 'list_users' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
		include get_404_template();
		exit;
	}
}
add_action( 'template_redirect', 'chargenet_block_author_enumeration', 0 );

/**
 * One message for every failed login, so it does not tell whether the user name exists.
 */
function chargenet_generic_login_error(): string {
	return esc_html__( 'The login details are not correct, or login is temporarily blocked. Try again later.', 'chargenet' );
}
add_filter( 'login_errors', 'chargenet_generic_login_error' );

/**
 * Login throttling. After 5 failed attempts for the same IP address and user name within 15 minutes the pair is
 * blocked for 15 minutes. Only a hash of both is kept, in a transient that expires by itself (docs/privacy.md).
 */
const CHARGENET_LOGIN_MAX_ATTEMPTS = 5;
const CHARGENET_LOGIN_WINDOW       = 15 * MINUTE_IN_SECONDS;

/**
 * Transient key for one IP address and user name.
 *
 * @param string $username User name as typed.
 */
function chargenet_login_key( string $username ): string {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return 'cn_login_' . substr( hash_hmac( 'sha256', $ip . '|' . strtolower( $username ), wp_salt( 'nonce' ) ), 0, 32 );
}

/**
 * Refuse the login while the pair is blocked (also when the password is right: a guesser must not learn that).
 *
 * @param WP_User|WP_Error|null $user     User or error so far.
 * @param string                $username User name as typed.
 * @return WP_User|WP_Error|null
 */
function chargenet_login_throttle( $user, $username ) {
	if ( '' !== (string) $username && (int) get_transient( chargenet_login_key( (string) $username ) ) >= CHARGENET_LOGIN_MAX_ATTEMPTS ) {
		return new WP_Error( 'chargenet_blocked', chargenet_generic_login_error() );
	}
	return $user;
}
add_filter( 'authenticate', 'chargenet_login_throttle', 99, 2 ); // Last, so that nothing earlier lets a blocked pair in.

/**
 * Count a failed login.
 *
 * @param string $username User name as typed.
 */
function chargenet_login_failed( $username ): void {
	$key   = chargenet_login_key( (string) $username );
	$count = (int) get_transient( $key );
	if ( $count < CHARGENET_LOGIN_MAX_ATTEMPTS ) { // A blocked pair keeps its original expiry.
		set_transient( $key, $count + 1, CHARGENET_LOGIN_WINDOW );
	}
}
add_action( 'wp_login_failed', 'chargenet_login_failed' );

/**
 * Forget the failures after a good login.
 *
 * @param string $username User name.
 */
function chargenet_login_succeeded( $username ): void {
	delete_transient( chargenet_login_key( (string) $username ) );
}
add_action( 'wp_login', 'chargenet_login_succeeded' );

/**
 * RFC 9116 security.txt: where to report a vulnerability. Served by WordPress, so it works on every host without a
 * file in the web root. Expires one year ahead and so is always valid; review the contact when it changes.
 */
function chargenet_security_txt(): void {
	$path = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
	if ( '/.well-known/security.txt' !== $path && '/security.txt' !== $path ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	echo "Contact: mailto:security@chargenet.energy\n";
	echo 'Expires: ' . esc_html( gmdate( 'Y-m-d\TH:i:s\Z', strtotime( '+1 year' ) ) ) . "\n";
	echo "Preferred-Languages: en, nl\n";
	echo 'Canonical: ' . esc_url( home_url( '/.well-known/security.txt' ) ) . "\n";
	exit;
}
add_action( 'init', 'chargenet_security_txt', 0 );
