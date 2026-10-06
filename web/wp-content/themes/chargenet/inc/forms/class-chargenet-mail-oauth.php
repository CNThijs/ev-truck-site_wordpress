<?php
/**
 * OAuth2 (client credentials) token for SMTP AUTH XOAUTH2 with Microsoft 365: PHPMailer asks this class for the
 * login string. The token is cached until shortly before it expires. Loaded only when PHPMailer is loaded.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PHPMailer\PHPMailer\OAuthTokenProvider;

/**
 * Token provider for Exchange Online SMTP.
 */
class Chargenet_Mail_OAuth implements OAuthTokenProvider {

	/**
	 * Settings.
	 *
	 * @param string $tenant  Directory (tenant) id.
	 * @param string $client  Application (client) id.
	 * @param string $secret  Client secret.
	 * @param string $mailbox Mailbox the application logs in as.
	 */
	public function __construct( private string $tenant, private string $client, private string $secret, private string $mailbox ) {}

	/**
	 * The XOAUTH2 string for the SMTP login.
	 */
	public function getOauth64(): string {
		$token = $this->token();
		// The SMTP XOAUTH2 login string is base64 by definition.
		return base64_encode( 'user=' . $this->mailbox . "\001auth=Bearer " . $token . "\001\001" ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}

	/**
	 * Access token from the cache or from Microsoft.
	 */
	private function token(): string {
		$key    = 'chargenet_ms_token_' . md5( $this->tenant . $this->client );
		$cached = get_transient( $key );
		if ( is_string( $cached ) && '' !== $cached ) {
			return $cached;
		}
		$response = wp_remote_post(
			'https://login.microsoftonline.com/' . rawurlencode( $this->tenant ) . '/oauth2/v2.0/token',
			array(
				'timeout' => 15,
				'body'    => array(
					'grant_type'    => 'client_credentials',
					'client_id'     => $this->client,
					'client_secret' => $this->secret,
					'scope'         => 'https://outlook.office365.com/.default',
				),
			)
		);
		$body     = is_wp_error( $response ) ? array() : json_decode( (string) wp_remote_retrieve_body( $response ), true );
		if ( empty( $body['access_token'] ) ) {
			$GLOBALS['chargenet_mail_err'] = 'Microsoft token request failed: ' . ( is_wp_error( $response ) ? $response->get_error_message() : (string) ( $body['error_description'] ?? 'unknown' ) );
			return '';
		}
		set_transient( $key, (string) $body['access_token'], max( 60, (int) ( $body['expires_in'] ?? 3600 ) - 120 ) );
		return (string) $body['access_token'];
	}
}
