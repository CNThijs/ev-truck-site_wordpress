<?php
/**
 * Server-side hardening baseline, for any environment: `wp eval-file bin/check-hardening.php [production]`
 * (on STRATO copy the file next to wp-config.php first and delete it afterwards). Prints ok/FAIL/warn per check and
 * exits 1 when something fails. HTTP-visible checks (headers, closed endpoints, exposed files): bin/check-security.mjs.
 * What each check means and how to fix it: docs/security.md.
 */

$cn_production = in_array( 'production', isset( $args ) ? (array) $args : array(), true ) || 'production' === wp_get_environment_type();
$cn_failed     = 0;
$cn_check      = static function ( bool $ok, string $name, string $detail = '', string $level = 'fail' ) use ( &$cn_failed, $cn_production ): void {
	if ( ! $cn_production && 'prod' === $level ) {
		$level = 'warn';
	}
	if ( 'prod' === $level ) {
		$level = 'fail';
	}
	if ( ! $ok && 'fail' === $level ) {
		++$cn_failed;
	}
	echo ( $ok ? 'ok   ' : ( 'warn' === $level ? 'warn ' : 'FAIL ' ) ) . $name . ( $detail && ! $ok ? "  [$detail]" : '' ) . "\n";
};

// --- wp-config -----------------------------------------------------------------------------------------------------
$cn_check( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT, 'File editing in the admin is off' );
$cn_check( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG, 'WP_DEBUG is off', '', 'prod' );
$cn_check( ! defined( 'WP_DEBUG_DISPLAY' ) || ! WP_DEBUG_DISPLAY || ! WP_DEBUG, 'Errors are not displayed to visitors', '', 'prod' );
$cn_check( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG, 'SCRIPT_DEBUG is off', '', 'warn' );
$cn_salts = true;
foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $cn_const ) {
	if ( ! defined( $cn_const ) || strlen( (string) constant( $cn_const ) ) < 40 || false !== stripos( (string) constant( $cn_const ), 'put your unique phrase here' ) ) {
		$cn_salts = false;
	}
}
$cn_check( $cn_salts && 8 === count( array_unique( array_map( 'constant', array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) ) ) ), 'All 8 keys and salts are set, long and different' );
$cn_check( ! ini_get( 'display_errors' ) || in_array( strtolower( (string) ini_get( 'display_errors' ) ), array( '0', 'off', 'stderr' ), true ) || 'cli' === PHP_SAPI, 'PHP display_errors is off', (string) ini_get( 'display_errors' ), 'prod' );
$cn_check( 0 === strpos( home_url(), 'https://' ) || ! $cn_production, 'Site address (home_url) uses https', home_url(), 'prod' );
$cn_check( 0 === strpos( site_url(), 'https://' ) || ! $cn_production, 'WordPress address (site_url) uses https', site_url(), 'prod' );

// --- Settings ------------------------------------------------------------------------------------------------------
$cn_check( ! get_option( 'users_can_register' ), 'Public registration is closed' );
$cn_check( 'subscriber' === get_option( 'default_role' ), 'Default role is subscriber', (string) get_option( 'default_role' ) );
$cn_check( 'closed' === get_option( 'default_comment_status' ) || 0 === (int) get_option( 'comments_notify' ) + (int) ( 'open' === get_option( 'default_comment_status' ) ), 'Comments are closed by default', (string) get_option( 'default_comment_status' ), 'warn' );
$cn_check( has_filter( 'xmlrpc_enabled', '__return_false' ) !== false, 'XML-RPC is disabled by the theme' );
$cn_check( has_filter( 'rest_endpoints', 'chargenet_rest_hide_users' ) !== false, 'REST user list is hidden by the theme' );
$cn_check( function_exists( 'chargenet_login_throttle' ), 'Login throttling is active (theme)' );

// --- Users and roles -----------------------------------------------------------------------------------------------
$cn_admins = get_users( array( 'role' => 'administrator' ) );
$cn_check( count( $cn_admins ) >= 1 && count( $cn_admins ) <= 3, 'Between 1 and 3 administrators', count( $cn_admins ) . ' found' );
$cn_check( false === get_user_by( 'login', 'admin' ) || ! $cn_production, 'No user named "admin"', '', 'prod' );
$cn_extra = array();
foreach ( get_users() as $cn_user ) {
	if ( array_intersect( array( 'author', 'contributor', 'shop_manager' ), $cn_user->roles ) ) {
		$cn_extra[] = $cn_user->user_login;
	}
}
$cn_check( ! $cn_extra, 'No unused elevated roles (author, contributor)', implode( ', ', $cn_extra ), 'warn' );
if ( class_exists( 'Two_Factor_Core' ) ) {
	$cn_no2fa = array();
	foreach ( array_merge( $cn_admins, get_users( array( 'role' => 'editor' ) ) ) as $cn_user ) {
		if ( ! Two_Factor_Core::is_user_using_two_factor( $cn_user->ID ) ) {
			$cn_no2fa[] = $cn_user->user_login;
		}
	}
	$cn_check( ! $cn_no2fa, 'Every administrator and editor has two-factor authentication', implode( ', ', $cn_no2fa ), 'prod' );
} else {
	$cn_check( false, 'Two Factor plugin is active', 'not active', 'prod' );
}

// --- Plugins, themes, updates --------------------------------------------------------------------------------------
require_once ABSPATH . 'wp-admin/includes/plugin.php';
require_once ABSPATH . 'wp-admin/includes/update.php';
$cn_allowed = array( 'polylang', 'seo-by-rank-math', 'wpconsent-cookies-banner-privacy-suite', 'cache-enabler', 'two-factor' );
$cn_active  = array_map( static fn( $p ) => dirname( $p ), (array) get_option( 'active_plugins', array() ) );
$cn_check( ! array_diff( $cn_active, $cn_allowed ), 'Only approved plugins are active (docs/plugins.md)', implode( ', ', array_diff( $cn_active, $cn_allowed ) ) );
$cn_installed = array_map( static fn( $p ) => dirname( $p ), array_keys( get_plugins() ) );
$cn_check( ! array_diff( $cn_installed, $cn_active ), 'No inactive plugins installed', implode( ', ', array_diff( $cn_installed, $cn_active ) ), 'prod' );
$cn_themes = array_keys( wp_get_themes() );
$cn_check( array( 'chargenet' ) === $cn_themes, 'Only the chargenet theme is installed', implode( ', ', $cn_themes ), 'warn' );
$cn_plugin_updates = (array) get_site_transient( 'update_plugins' );
$cn_check( empty( $cn_plugin_updates['response'] ), 'No plugin updates pending', implode( ', ', array_keys( (array) ( $cn_plugin_updates['response'] ?? array() ) ) ), 'warn' );
$cn_core_updates = get_core_updates();
$cn_check( empty( $cn_core_updates ) || 'latest' === ( $cn_core_updates[0]->response ?? 'latest' ) || 'development' === ( $cn_core_updates[0]->response ?? '' ), 'WordPress core is up to date', (string) get_bloginfo( 'version' ), 'warn' );

// --- Files ---------------------------------------------------------------------------------------------------------
$cn_mode = static fn( string $path ): int => file_exists( $path ) ? (int) ( fileperms( $path ) & 0777 ) : -1;
$cn_config = file_exists( ABSPATH . 'wp-config.php' ) ? ABSPATH . 'wp-config.php' : dirname( ABSPATH ) . '/wp-config.php';
$cn_check( -1 === $cn_mode( $cn_config ) || 0 === ( $cn_mode( $cn_config ) & 0007 ), 'wp-config.php is not world-readable (use 600 or 640)', decoct( $cn_mode( $cn_config ) ), 'prod' );
$cn_check( $cn_mode( ABSPATH . '.htaccess' ) < 0 || 0 === ( $cn_mode( ABSPATH . '.htaccess' ) & 0002 ), '.htaccess is not world-writable', decoct( max( 0, $cn_mode( ABSPATH . '.htaccess' ) ) ) );
$cn_writable = array();
foreach ( array( ABSPATH, WP_CONTENT_DIR, get_template_directory() ) as $cn_dir ) {
	if ( $cn_mode( $cn_dir ) & 0002 ) {
		$cn_writable[] = $cn_dir;
	}
}
$cn_check( ! $cn_writable, 'No world-writable core, content or theme folder (use 755)', implode( ', ', $cn_writable ), 'prod' );
$cn_php = array();
$cn_up  = wp_get_upload_dir()['basedir'];
if ( is_dir( $cn_up ) ) {
	foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $cn_up, FilesystemIterator::SKIP_DOTS ) ) as $cn_file ) {
		if ( preg_match( '/\.(php\d?|phtml|phar)$/i', $cn_file->getFilename() ) ) {
			$cn_php[] = str_replace( $cn_up, 'uploads', $cn_file->getPathname() );
		}
	}
}
$cn_check( ! $cn_php, 'No PHP files in uploads', implode( ', ', array_slice( $cn_php, 0, 3 ) ) );
$cn_check( ! file_exists( WP_CONTENT_DIR . '/debug.log' ), 'No wp-content/debug.log' );
$cn_check( ! glob( ABSPATH . '{*.sql,*.zip,*.tar.gz,wp-config.php.*,*.bak,*.old}', GLOB_BRACE ), 'No backup or dump files in the web root', implode( ', ', array_map( 'basename', (array) glob( ABSPATH . '{*.sql,*.zip,*.tar.gz,wp-config.php.*,*.bak,*.old}', GLOB_BRACE ) ) ) );
$cn_check( ! file_exists( ABSPATH . 'check-host.php' ) && ! file_exists( ABSPATH . 'check-hardening.php' ), 'Check scripts are removed from the web root' );
$cn_htaccess = file_exists( ABSPATH . '.htaccess' ) ? (string) file_get_contents( ABSPATH . '.htaccess' ) : '';
$cn_check( false !== strpos( $cn_htaccess, 'BEGIN ChargeNet performance' ) && false !== strpos( $cn_htaccess, 'BEGIN ChargeNet security headers' ) && false !== strpos( $cn_htaccess, 'BEGIN ChargeNet protection' ), '.htaccess has the ChargeNet blocks (docs/htaccess.md)', '', 'prod' );

echo "\n" . ( $cn_failed ? "$cn_failed check(s) failed" : 'All checks passed' ) . ( $cn_production ? ' (production rules)' : ' (local rules: some failures are warnings)' ) . ".\n";
if ( $cn_failed && class_exists( 'WP_CLI' ) ) {
	WP_CLI::halt( 1 );
}
