<?php
/**
 * Tracking: Google Tag Manager after consent, the campaign cookie and the consent log. Nothing loads from Google until
 * the visitor accepts statistics (WPConsent unblocks the script). The container ID is not a secret but it is not in the
 * repository either: define it in wp-config.php, `define( 'CHARGENET_GTM_ID', 'GTM-XXXXXXX' );`. Without it nothing
 * is added. Consent log: a private table (random visitor ID, time, choices, language, banner text version; no IP
 * address, no browser details), deleted after 36 months. Docs: docs/tracking.md.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_CONSENT_DB_VERSION = '1';
const CHARGENET_CONSENT_MONTHS     = 36;

/**
 * Container ID from wp-config.php, or empty.
 */
function chargenet_gtm_id(): string {
	$id = defined( 'CHARGENET_GTM_ID' ) ? (string) CHARGENET_GTM_ID : '';
	return preg_match( '/^GTM-[A-Z0-9]{4,12}$/', $id ) ? $id : '';
}

/**
 * Tag Manager loader. The type and data attributes make WPConsent hold the script until statistics are accepted.
 */
function chargenet_gtm_script(): void {
	$id = chargenet_gtm_id();
	if ( '' === $id ) {
		return;
	}
	$js = "window.dataLayer=window.dataLayer||[];window.dataLayer.push({'gtm.start':new Date().getTime(),event:'gtm.js'});"
		. "var s=document.createElement('script');s.async=true;s.src='https://www.googletagmanager.com/gtm.js?id=" . $id . "';document.head.appendChild(s);";
	printf( '<script type="text/plain" data-wpconsent-name="google-tag-manager" data-wpconsent-category="statistics">%s</script>' . "\n", $js ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- ID matched against a strict pattern.
}
add_action( 'wp_head', 'chargenet_gtm_script', 1 );

/**
 * Version of the banner text: changes when the wording changes, so a record shows what the visitor saw.
 */
function chargenet_consent_version(): string {
	return substr( md5( wp_json_encode( chargenet_consent_texts() ) ), 0, 8 );
}

/**
 * Inline script on every page: sends each saved choice to the consent log, and keeps the campaign cookie while
 * statistics are accepted (UTM parameters of the landing URL, 30 days; the form script fills the hidden fields from it, blocks/_shared/form.js).
 */
function chargenet_tracking_script(): void {
	$config = array(
		'url'     => rest_url( 'chargenet/v1/consent' ),
		'lang'    => chargenet_current_lang(),
		'version' => chargenet_consent_version(),
	);
	$js     = '(function(c){'
		. "function id(){try{return localStorage.getItem('cn_consent_id')||''}catch(e){return ''}}"
		. "window.addEventListener('wpconsent_consent_saved',function(e){var p=e.detail||{};"
		. "fetch(c.url,{method:'POST',keepalive:true,headers:{'Content-Type':'application/json'},body:JSON.stringify({id:id(),statistics:!!p.statistics,marketing:!!p.marketing,lang:c.lang,version:c.version})})"
		. ".then(function(r){return r.json()}).then(function(j){if(j&&j.id)try{localStorage.setItem('cn_consent_id',j.id)}catch(e){}}).catch(function(){})});"
		. "document.addEventListener('wpconsent_consent_processed',function(e){if(!(e.detail||{}).statistics)return;"
		. "var q=new URLSearchParams(location.search),o=new URLSearchParams();['utm_source','utm_medium','utm_campaign','utm_term'].forEach(function(k){if(q.get(k))o.set(k,q.get(k).slice(0,100))});"
		. "if(String(o))document.cookie='cn_campaign='+encodeURIComponent(String(o))+';max-age=2592000;path=/;SameSite=Lax'+(location.protocol==='https:'?';Secure':'')})"
		. '})(' . wp_json_encode( $config ) . ');';
	wp_print_inline_script_tag( $js );
}
add_action( 'wp_head', 'chargenet_tracking_script', 2 );

/**
 * Name of the consent log table.
 */
function chargenet_consent_table(): string {
	global $wpdb;
	return $wpdb->prefix . 'chargenet_consent';
}

/**
 * Create the table (once per version).
 */
function chargenet_consent_install(): void {
	if ( CHARGENET_CONSENT_DB_VERSION === get_option( 'chargenet_consent_db_version' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table   = chargenet_consent_table();
	$charset = $wpdb->get_charset_collate();
	dbDelta(
		"CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			visitor char(32) NOT NULL,
			created_at datetime NOT NULL,
			statistics tinyint(1) NOT NULL DEFAULT 0,
			marketing tinyint(1) NOT NULL DEFAULT 0,
			lang varchar(5) NOT NULL DEFAULT '',
			version char(8) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			KEY visitor (visitor),
			KEY created_at (created_at)
		) {$charset};"
	);
	update_option( 'chargenet_consent_db_version', CHARGENET_CONSENT_DB_VERSION, true );
}
add_action( 'init', 'chargenet_consent_install', 5 );

/**
 * Store one choice. Returns the visitor ID (new when the one sent is missing or malformed).
 *
 * @param WP_REST_Request $request Request.
 */
function chargenet_consent_record( WP_REST_Request $request ): WP_REST_Response {
	global $wpdb;
	$visitor = (string) $request->get_param( 'id' );
	if ( ! preg_match( '/^[a-f0-9]{32}$/', $visitor ) ) {
		$visitor = bin2hex( random_bytes( 16 ) );
	}
	$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		chargenet_consent_table(),
		array(
			'visitor'    => $visitor,
			'created_at' => current_time( 'mysql', true ),
			'statistics' => $request->get_param( 'statistics' ) ? 1 : 0,
			'marketing'  => $request->get_param( 'marketing' ) ? 1 : 0,
			'lang'       => (string) $request->get_param( 'lang' ),
			'version'    => (string) $request->get_param( 'version' ),
		)
	);
	return new WP_REST_Response( array( 'id' => $visitor ) );
}

add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'chargenet/v1',
			'/consent',
			array(
				'methods'             => 'POST',
				'callback'            => 'chargenet_consent_record',
				'permission_callback' => '__return_true', // Public by design: visitors are anonymous. Every field is validated.
				'args'                => array(
					'id'         => array( 'validate_callback' => static fn( $v ) => is_string( $v ) && strlen( $v ) <= 32 ),
					'statistics' => array( 'type' => 'boolean' ),
					'marketing'  => array( 'type' => 'boolean' ),
					'lang'       => array(
						'required'          => true,
						'validate_callback' => static fn( $v ) => is_string( $v ) && preg_match( '/^[a-z]{2}$/', $v ),
					),
					'version'    => array(
						'required'          => true,
						'validate_callback' => static fn( $v ) => is_string( $v ) && preg_match( '/^[a-f0-9]{8}$/', $v ),
					),
				),
			)
		);
	}
);

/**
 * Delete records older than 36 months.
 */
function chargenet_consent_purge(): void {
	global $wpdb;
	$table = chargenet_consent_table();
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s", gmdate( 'Y-m-d H:i:s', strtotime( '-' . CHARGENET_CONSENT_MONTHS . ' months' ) ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}
add_action( 'chargenet_consent_purge_daily', 'chargenet_consent_purge' );

add_action(
	'init',
	static function (): void {
		if ( ! wp_next_scheduled( 'chargenet_consent_purge_daily' ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'chargenet_consent_purge_daily' );
		}
	}
);
