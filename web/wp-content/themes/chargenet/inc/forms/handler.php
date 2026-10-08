<?php
/**
 * Form handler. One endpoint (admin-post.php) serves both forms and both ways of submitting: a normal POST
 * (answered with a redirect back to the page) and a fetch from view.js (answered with JSON). Checks, in order:
 * nonce, honeypot, minimum time, Turnstile (when on), rate limit, validation, report code. Nothing is stored for
 * a failed check; a code that is not on the list is counted per visitor so codes cannot be guessed.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_FORM_MIN_SECONDS = 3;
const CHARGENET_CODE_FAILS       = 5;
const CHARGENET_CODE_WINDOW      = 900;

/**
 * Address of the visitor as the server sees it (REMOTE_ADDR). Behind a proxy or CDN this is the proxy: see
 * docs/integrations.md.
 */
function chargenet_form_ip(): string {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
}

/**
 * Transient name of a rate-limit bucket (the address is hashed, never stored).
 *
 * @param string $bucket Bucket name.
 * @param string $ip     Address.
 */
function chargenet_rate_key( string $bucket, string $ip ): string {
	return 'cn_rl_' . substr( hash_hmac( 'sha256', $bucket . '|' . $ip, wp_salt() ), 0, 24 );
}

/**
 * Number of hits in a bucket.
 *
 * @param string $bucket Bucket.
 * @param string $ip     Address.
 */
function chargenet_rate_count( string $bucket, string $ip ): int {
	$data = get_transient( chargenet_rate_key( $bucket, $ip ) );
	return is_array( $data ) && ( $data['until'] ?? 0 ) > time() ? (int) $data['n'] : 0;
}

/**
 * Add a hit to a bucket; the window starts at the first hit.
 *
 * @param string $bucket Bucket.
 * @param string $ip     Address.
 * @param int    $window Seconds.
 */
function chargenet_rate_add( string $bucket, string $ip, int $window ): void {
	$key  = chargenet_rate_key( $bucket, $ip );
	$data = get_transient( $key );
	if ( ! is_array( $data ) || ( $data['until'] ?? 0 ) <= time() ) {
		$data = array(
			'n'     => 0,
			'until' => time() + $window,
		);
	}
	++$data['n'];
	set_transient( $key, $data, max( 1, $data['until'] - time() ) );
}

/**
 * Signed timestamp for the "form was open for a while" check.
 */
function chargenet_form_token(): string {
	$time = (string) time();
	return $time . '.' . substr( hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) ), 0, 20 );
}

/**
 * Fresh nonce and signed time for a form on a cached page (the HTML of the page may be hours old).
 */
add_action(
	'rest_api_init',
	static function (): void {
		register_rest_route(
			'chargenet/v1',
			'/form-token',
			array(
				'methods'             => 'GET',
				'permission_callback' => '__return_true', // Public by design: the values are the same any visitor gets in the page.
				'callback'            => static function (): WP_REST_Response {
					$response = new WP_REST_Response(
						array(
							'nonce' => wp_create_nonce( 'chargenet_form' ),
							'ts'    => chargenet_form_token(),
						)
					);
					$response->header( 'Cache-Control', 'no-store' );
					return $response;
				},
			)
		);
	}
);

/**
 * Seconds since the token was made, or -1 when it is not valid.
 *
 * @param string $token Token from the form.
 */
function chargenet_form_token_age( string $token ): int {
	$parts = explode( '.', $token );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return -1;
	}
	return hash_equals( substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), 0, 20 ), $parts[1] ) ? time() - (int) $parts[0] : -1;
}

/**
 * Run a function with the texts of a language (the handler runs outside the page's language).
 *
 * @param string   $lang en or nl.
 * @param callable $callback Function.
 * @return mixed
 */
function chargenet_forms_in_lang( string $lang, callable $callback ) {
	$wanted  = 'nl' === $lang ? 'nl_NL' : 'en_US';
	$current = determine_locale();
	if ( $wanted === $current ) {
		return $callback();
	}
	$load = static function ( string $locale ): void {
		unload_textdomain( 'chargenet' );
		load_textdomain( 'chargenet', CHARGENET_DIR . '/languages/chargenet-' . $locale . '.mo' );
	};
	$load( $wanted );
	try {
		return $callback();
	} finally {
		$load( $current );
	}
}

/**
 * The consent sentence shown next to the checkbox (and stored with the submission), without the link.
 *
 * @param string $lang en or nl.
 * @param string $form Form id (the newsletter has its own sentence).
 */
function chargenet_form_consent_text( string $lang, string $form = '' ): string {
	return (string) chargenet_forms_in_lang(
		$lang,
		static fn() => 'newsletter' === $form
			? __( 'I agree that ChargeNet sends me its newsletter and processes my email address for that, as described in the privacy policy.', 'chargenet' )
			: __( 'I agree that ChargeNet processes my details to handle this request, as described in the privacy policy.', 'chargenet' )
	);
}

/**
 * Check the Cloudflare Turnstile answer (only when Turnstile is on).
 *
 * @param string $response Token from the widget.
 * @param string $ip       Address.
 */
function chargenet_turnstile_ok( string $response, string $ip ): bool {
	if ( ! chargenet_turnstile_enabled() ) {
		return true;
	}
	if ( '' === $response ) {
		return false;
	}
	$reply = wp_remote_post(
		'https://challenges.cloudflare.com/turnstile/v0/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => chargenet_forms_setting( 'turnstile_secret' ),
				'response' => $response,
				'remoteip' => $ip,
			),
		)
	);
	$body  = is_wp_error( $reply ) ? array() : json_decode( (string) wp_remote_retrieve_body( $reply ), true );
	return ! empty( $body['success'] );
}

/**
 * A short text field: tags removed, spaces collapsed, at most $max characters.
 *
 * @param mixed $value Raw value.
 * @param int   $max   Maximum length.
 */
function chargenet_form_clean( $value, int $max ): string {
	return mb_substr( trim( (string) preg_replace( '/\s+/u', ' ', sanitize_text_field( (string) $value ) ) ), 0, $max );
}

/**
 * Validate and clean the fields of a form.
 *
 * @param array<string, mixed> $post Request values.
 * @param string               $form contact or trend_report.
 * @param string               $mode code or nocode (trend report).
 * @return array{errors: array<string, string>, clean: array<string, mixed>}
 */
function chargenet_form_validate( array $post, string $form, string $mode ): array {
	$errors = array();
	$clean  = array(
		'email' => sanitize_email( (string) ( $post['email'] ?? '' ) ),
	);
	if ( ! is_email( $clean['email'] ) || strlen( $clean['email'] ) > 254 ) {
		$errors['email'] = __( 'Enter a valid email address.', 'chargenet' );
	}
	if ( 'contact' === $form || 'nocode' === $mode ) {
		$clean['name'] = chargenet_form_clean( $post['name'] ?? '', 100 );
		if ( mb_strlen( $clean['name'] ) < 2 ) {
			$errors['name'] = __( 'Enter your name (at least 2 characters).', 'chargenet' );
		}
	}
	if ( 'contact' === $form ) {
		$clean['message'] = mb_substr( trim( sanitize_textarea_field( (string) ( $post['message'] ?? '' ) ) ), 0, 5000 );
		if ( mb_strlen( $clean['message'] ) < 10 ) {
			$errors['message'] = __( 'Enter a message (at least 10 characters).', 'chargenet' );
		}
	}
	if ( 'trend_report' === $form && 'nocode' === $mode ) {
		$clean['company'] = chargenet_form_clean( $post['company'] ?? '', 100 );
		if ( mb_strlen( $clean['company'] ) < 2 ) {
			$errors['company'] = __( 'Enter your company name (at least 2 characters).', 'chargenet' );
		}
	}
	if ( 'trend_report' === $form && 'code' === $mode ) {
		$clean['code'] = chargenet_code_normalise( (string) ( $post['code'] ?? '' ) );
		if ( '' === $clean['code'] ) {
			$errors['code'] = __( 'Enter your code.', 'chargenet' );
		}
	}
	if ( empty( $post['consent'] ) ) {
		$errors['consent'] = __( 'Please agree to the privacy statement to continue.', 'chargenet' );
	}
	return array(
		'errors' => $errors,
		'clean'  => $clean,
	);
}

/**
 * The form a request is for: contact, newsletter or trend_report.
 *
 * @param array<string, mixed> $post Request values.
 */
function chargenet_form_id( array $post ): string {
	$form = (string) ( $post['cn_form'] ?? '' );
	return in_array( $form, array( 'contact', 'newsletter' ), true ) ? $form : 'trend_report';
}

/**
 * Process a submission. Returns what to show; stores the submission and sends the email on success.
 *
 * @param array<string, mixed> $post Request values (unslashed).
 * @param string               $ip   Address of the visitor.
 * @return array{ok: bool, errors: array<string, string>, message: string, form: string, mode: string, id: int}
 */
function chargenet_form_process( array $post, string $ip ): array {
	$form = chargenet_form_id( $post );
	$mode = 'trend_report' === $form && isset( $post['cn_mode'] ) && 'nocode' === $post['cn_mode'] ? 'nocode' : 'code';
	$lang = isset( $post['cn_lang'] ) && 'nl' === $post['cn_lang'] ? 'nl' : 'en';
	$out  = array(
		'ok'      => false,
		'errors'  => array(),
		'message' => '',
		'form'    => $form,
		'mode'    => 'trend_report' === $form ? $mode : '',
		'id'      => 0,
	);

	return (array) chargenet_forms_in_lang(
		$lang,
		static function () use ( $post, $ip, $form, $mode, $lang, $out ): array {
			$fail = static function ( string $message, array $errors = array() ) use ( $out ): array {
				return array_merge(
					$out,
					array(
						'message' => $message,
						'errors'  => $errors,
					)
				);
			};

			// Bots fill the hidden field: answer as if it worked, store nothing.
			if ( '' !== trim( (string) ( $post['website'] ?? '' ) ) ) {
				return array_merge(
					$out,
					array(
						'ok'      => true,
						'message' => __( 'Thank you.', 'chargenet' ),
					)
				);
			}
			if ( chargenet_form_token_age( (string) ( $post['cn_ts'] ?? '' ) ) < CHARGENET_FORM_MIN_SECONDS ) {
				return $fail( __( 'That was very fast. Please wait a moment and send the form again.', 'chargenet' ) );
			}
			if ( ! chargenet_turnstile_ok( (string) ( $post['cf-turnstile-response'] ?? '' ), $ip ) ) {
				return $fail( __( 'We could not verify that you are not a robot. Reload the page and try again.', 'chargenet' ) );
			}
			if ( chargenet_rate_count( 'submit:' . $form, $ip ) >= 6 ) {
				return $fail( __( 'Too many requests. Please try again in an hour.', 'chargenet' ) );
			}

			$checked = chargenet_form_validate( $post, $form, $mode );
			if ( $checked['errors'] ) {
				return $fail( __( 'Please check the fields marked below.', 'chargenet' ), $checked['errors'] );
			}
			$clean = $checked['clean'];

			if ( 'trend_report' === $form && 'code' === $mode ) {
				if ( chargenet_rate_count( 'codefail', $ip ) >= CHARGENET_CODE_FAILS ) {
					return $fail( __( 'Too many attempts with a code that is not valid. Try again later, or request the report without a code.', 'chargenet' ) );
				}
				if ( ! chargenet_code_exists( (string) $clean['code'] ) ) {
					chargenet_rate_add( 'codefail', $ip, CHARGENET_CODE_WINDOW );
					return $fail(
						__( 'Please check the fields marked below.', 'chargenet' ),
						array( 'code' => __( 'This code is not valid. Check the code or request the report without one.', 'chargenet' ) )
					);
				}
			}

			// Worded before the submission is stored and sent: sending mail can reset the loaded texts.
			if ( 'contact' === $form ) {
				$success = __( 'Thank you for your message. We have sent a copy to your email address and will get back to you soon.', 'chargenet' );
			} elseif ( 'newsletter' === $form ) {
				$success = __( 'Thank you for subscribing. We have sent a confirmation to your email address.', 'chargenet' );
			} else {
				$success = __( 'Thank you. We are sending the Trend Report 2027 to your email address. If you do not see it within a few minutes, check your spam folder.', 'chargenet' );
			}
			$error_save = __( 'Something went wrong on our side. Please try again later or email info@chargenet.energy.', 'chargenet' );

			// Already subscribed: the same answer, nothing stored or sent again.
			if ( 'newsletter' === $form && chargenet_submissions_by_email( (string) $clean['email'], 'newsletter' ) ) {
				return array_merge(
					$out,
					array(
						'ok'      => true,
						'message' => $success,
					)
				);
			}

			$utm = array();
			foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term' ) as $key ) {
				$value = isset( $post[ 'cn_' . $key ] ) ? chargenet_form_clean( preg_replace( '/[^A-Za-z0-9_\-\. ]/', '', (string) $post[ 'cn_' . $key ] ), 100 ) : '';
				if ( '' !== $value ) {
					$utm[ $key ] = $value;
				}
			}
			$id = chargenet_submission_create(
				array_merge(
					$clean,
					array(
						'form'         => $form,
						'lang'         => $lang,
						'consent_text' => chargenet_form_consent_text( $lang, $form ),
						'utm'          => $utm,
					)
				)
			);
			if ( ! $id ) {
				return $fail( $error_save );
			}
			chargenet_rate_add( 'submit:' . $form, $ip, HOUR_IN_SECONDS );
			if ( ! empty( $clean['code'] ) ) {
				chargenet_code_record_use( (string) $clean['code'] );
			}
			chargenet_mail_attempt( $id ); // A failure is stored and retried; the visitor still gets the confirmation.

			return array_merge(
				$out,
				array(
					'ok'      => true,
					'id'      => $id,
					'message' => $success,
				)
			);
		}
	);
}

/**
 * Does the request want JSON (the fetch of view.js)?
 */
function chargenet_form_wants_json(): bool {
	return isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'application/json' );
}

/**
 * The endpoint.
 */
function chargenet_form_handle(): void {
	$post  = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified below.
	$lang  = isset( $post['cn_lang'] ) && 'nl' === $post['cn_lang'] ? 'nl' : 'en';
	$valid = (bool) wp_verify_nonce( (string) ( $post['_wpnonce'] ?? '' ), 'chargenet_form' );

	if ( $valid ) {
		$result = chargenet_form_process( $post, chargenet_form_ip() );
	} else {
		$result = array(
			'ok'      => false,
			'errors'  => array(),
			'message' => (string) chargenet_forms_in_lang( $lang, static fn() => __( 'This page has expired. Reload the page and send the form again.', 'chargenet' ) ),
			'form'    => chargenet_form_id( $post ),
			'mode'    => isset( $post['cn_mode'] ) && 'nocode' === $post['cn_mode'] ? 'nocode' : 'code',
			'id'      => 0,
		);
	}

	if ( chargenet_form_wants_json() ) {
		wp_send_json(
			array(
				'ok'      => $result['ok'],
				'errors'  => (object) $result['errors'],
				'message' => $result['message'],
			),
			$result['ok'] ? 200 : 422
		);
	}

	// Without JavaScript: keep the result for a few minutes and go back to the page.
	$key = wp_generate_password( 12, false );
	set_transient(
		'cn_state_' . $key,
		array(
			'form'    => $result['form'],
			'mode'    => $result['mode'],
			'ok'      => $result['ok'],
			'errors'  => $result['errors'],
			'message' => $result['message'],
			'old'     => $result['ok'] ? array() : array_intersect_key( $post, array_flip( array( 'name', 'email', 'company', 'message', 'code' ) ) ),
		),
		10 * MINUTE_IN_SECONDS
	);
	$back   = wp_validate_redirect( (string) ( $post['cn_return'] ?? '' ), home_url( '/' ) );
	$anchor = 'cn-' . $result['form'] . ( '' !== $result['mode'] ? '-' . $result['mode'] : '' );
	wp_safe_redirect( add_query_arg( 'cn_k', $key, $back ) . '#' . $anchor );
	exit;
}
add_action( 'admin_post_nopriv_chargenet_form', 'chargenet_form_handle' );
add_action( 'admin_post_chargenet_form', 'chargenet_form_handle' );

/**
 * The result of a submission without JavaScript, for the form on the page (null when there is none).
 *
 * @return array<string, mixed>|null
 */
function chargenet_form_state(): ?array {
	static $state = false;
	if ( false === $state ) {
		$key   = isset( $_GET['cn_k'] ) ? preg_replace( '/[^A-Za-z0-9]/', '', sanitize_text_field( wp_unslash( $_GET['cn_k'] ) ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$state = '' !== $key ? get_transient( 'cn_state_' . $key ) : null;
		$state = is_array( $state ) ? $state : null;
	}
	return $state;
}
