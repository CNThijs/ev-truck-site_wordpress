<?php
/**
 * HTTP security headers, one list for every environment. PHP sends them on pages WordPress renders. Pages served from
 * the page cache never reach PHP, so on production the same list goes into .htaccess: print it with
 * `wp eval 'echo chargenet_security_headers_htaccess();'` (docs/htaccess.md, docs/security.md). Set the constant
 * CHARGENET_HEADERS_AT_SERVER in wp-config.php once the .htaccess block is in place to stop PHP from sending them twice.
 *
 * The Content Security Policy starts in report-only mode (violations are logged, nothing is blocked). Switch to
 * enforcing with `define( 'CHARGENET_CSP_MODE', 'enforce' );` in wp-config.php after the reports are clean.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Content Security Policy. Inline scripts and styles are allowed because the page cache makes per-request nonces
 * impossible and several approved tools (consent banner, Tag Manager) write inline code; everything else is limited to
 * our own origin and the approved services. No map provider is approved yet.
 */
function chargenet_csp(): string {
	$google = 'https://www.googletagmanager.com https://*.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com';
	$policy = array(
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://challenges.cloudflare.com",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data: https://*.google-analytics.com https://*.googletagmanager.com",
		"font-src 'self'",
		"connect-src 'self' $google",
		"frame-src 'self' https://challenges.cloudflare.com https://www.googletagmanager.com",
		"object-src 'none'",
		"base-uri 'self'",
		"form-action 'self'",
		"frame-ancestors 'self'",
		'report-uri ' . esc_url_raw( rest_url( 'chargenet/v1/csp-report' ) ),
	);
	return implode( '; ', $policy );
}

/**
 * All headers for the current environment.
 *
 * @param bool $https Whether the site is served over HTTPS (HSTS only makes sense then).
 * @return array<string,string>
 */
function chargenet_security_headers( bool $https = true ): array {
	$enforce = defined( 'CHARGENET_CSP_MODE' ) && 'enforce' === CHARGENET_CSP_MODE;
	$headers = array(
		( $enforce ? 'Content-Security-Policy' : 'Content-Security-Policy-Report-Only' ) => chargenet_csp(),
		'X-Content-Type-Options'     => 'nosniff',
		'X-Frame-Options'            => 'SAMEORIGIN',
		'Referrer-Policy'            => 'strict-origin-when-cross-origin',
		'Permissions-Policy'         => 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()',
		'Cross-Origin-Opener-Policy' => 'same-origin',
	);
	if ( $https ) {
		$headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
	}
	return $headers;
}

/**
 * Send the headers on pages WordPress renders.
 */
function chargenet_send_security_headers(): void {
	if ( defined( 'CHARGENET_HEADERS_AT_SERVER' ) && CHARGENET_HEADERS_AT_SERVER ) {
		return;
	}
	foreach ( chargenet_security_headers( is_ssl() ) as $name => $value ) {
		header( $name . ': ' . $value );
	}
}
add_action( 'send_headers', 'chargenet_send_security_headers' );

/**
 * The same headers as an .htaccess block (mod_headers), for pages and files served without PHP.
 */
function chargenet_security_headers_htaccess(): string {
	$out = "# BEGIN ChargeNet security headers\n<IfModule mod_headers.c>\n";
	foreach ( chargenet_security_headers( true ) as $name => $value ) {
		$out .= "\tHeader always set $name \"" . str_replace( '"', '\\"', $value ) . "\"\n";
	}
	return $out . "</IfModule>\n# END ChargeNet security headers\n";
}

/**
 * Receive CSP violation reports and write one line each to the PHP error log (no IP address, no user agent). Capped at
 * 200 reports an hour so a broken page cannot flood the log.
 */
add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'chargenet/v1',
			'/csp-report',
			array(
				'methods'             => 'POST',
				'permission_callback' => '__return_true', // Browsers send reports without credentials.
				'callback'            => static function ( WP_REST_Request $request ): WP_REST_Response {
					$count = (int) get_transient( 'cn_csp_reports' );
					if ( $count < 200 ) {
						set_transient( 'cn_csp_reports', $count + 1, HOUR_IN_SECONDS );
						$body   = json_decode( (string) $request->get_body(), true );
						$report = is_array( $body ) ? ( $body['csp-report'] ?? ( $body[0]['body'] ?? array() ) ) : array();
						$line   = array();
						// Old browsers send csp-report with dashed keys, newer ones camelCase.
						foreach ( array(
							'violated-directive' => 'effectiveDirective',
							'blocked-uri'        => 'blockedURL',
							'document-uri'       => 'documentURL',
						) as $old => $new ) {
							$line[] = substr( (string) preg_replace( '/[^\x20-\x7E]/', '', (string) ( $report[ $old ] ?? $report[ $new ] ?? '' ) ), 0, 200 );
						}
						error_log( vsprintf( 'CSP violation: %s blocked %s on %s', $line ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					}
					return new WP_REST_Response( null, 204 );
				},
			)
		);
	}
);
