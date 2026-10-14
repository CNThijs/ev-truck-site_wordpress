<?php
/**
 * Newsletter unsubscribe. The confirmation email carries a signed link to /newsletter/unsubscribe/. The link only shows
 * a confirm button (mail scanners open links, so a GET never changes anything); the POST removes every newsletter
 * subscription stored for that address. Nothing else (contact messages, report requests) is touched.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Signature for a subscription: ties the link to the submission and its address.
 *
 * @param int    $id    Submission id.
 * @param string $email Address.
 */
function chargenet_unsubscribe_token( int $id, string $email ): string {
	return substr( hash_hmac( 'sha256', 'unsubscribe|' . $id . '|' . strtolower( $email ), wp_salt( 'auth' ) ), 0, 32 );
}

/**
 * Link for a subscription, used in the confirmation email.
 *
 * @param int    $id    Submission id.
 * @param string $email Address.
 * @param string $lang  en or nl.
 */
function chargenet_unsubscribe_url( int $id, string $email, string $lang ): string {
	return add_query_arg(
		array(
			's' => $id,
			't' => chargenet_unsubscribe_token( $id, $email ),
			'l' => 'nl' === $lang ? 'nl' : 'en',
		),
		home_url( '/newsletter/unsubscribe/' )
	);
}

add_action(
	'init',
	static function (): void {
		add_rewrite_rule( '^newsletter/unsubscribe/?$', 'index.php?cn_unsubscribe=1', 'top' );
	}
);

add_filter(
	'query_vars',
	static function ( array $vars ): array {
		$vars[] = 'cn_unsubscribe';
		return $vars;
	}
);

/**
 * Show the confirm page, or process the confirmation.
 */
function chargenet_unsubscribe_serve(): void {
	if ( ! get_query_var( 'cn_unsubscribe' ) ) {
		return;
	}
	$is_post = isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) );
	// phpcs:disable WordPress.Security.NonceVerification.Recommended,WordPress.Security.NonceVerification.Missing -- Authenticated by the signed link itself.
	$source = $is_post ? $_POST : $_GET;
	$id     = isset( $source['s'] ) ? absint( $source['s'] ) : 0;
	$token  = isset( $source['t'] ) ? sanitize_text_field( wp_unslash( $source['t'] ) ) : '';
	$lang   = isset( $source['l'] ) && 'nl' === $source['l'] ? 'nl' : 'en';
	// phpcs:enable

	$email = $id && CHARGENET_SUBMISSION === get_post_type( $id ) && 'newsletter' === chargenet_submission_get( $id, 'form' )
		? (string) chargenet_submission_get( $id, 'email' )
		: '';
	$valid = '' !== $email && hash_equals( chargenet_unsubscribe_token( $id, $email ), $token );
	$done  = false;

	if ( $valid && $is_post ) {
		foreach ( chargenet_submissions_by_email( $email, 'newsletter' ) as $submission ) {
			wp_delete_post( $submission, true );
		}
		$done = true;
	}

	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow' );
	status_header( $valid || $done ? 200 : 404 );

	chargenet_forms_in_lang(
		$lang,
		static function () use ( $valid, $done, $id, $token, $lang ): void {
			get_header();
			echo '<section class="not-found">';
			if ( $done ) {
				echo '<h1>' . esc_html__( 'You are unsubscribed', 'chargenet' ) . '</h1>';
				echo '<p>' . esc_html__( 'We removed your email address from the newsletter list. You will not receive the newsletter again.', 'chargenet' ) . '</p>';
			} elseif ( $valid ) {
				echo '<h1>' . esc_html__( 'Unsubscribe from the newsletter', 'chargenet' ) . '</h1>';
				echo '<p>' . esc_html__( 'Confirm that you no longer want to receive the ChargeNet newsletter.', 'chargenet' ) . '</p>';
				echo '<form method="post" action="' . esc_url( home_url( '/newsletter/unsubscribe/' ) ) . '">';
				echo '<input type="hidden" name="s" value="' . esc_attr( (string) $id ) . '">';
				echo '<input type="hidden" name="t" value="' . esc_attr( $token ) . '">';
				echo '<input type="hidden" name="l" value="' . esc_attr( $lang ) . '">';
				echo '<button type="submit" class="button">' . esc_html__( 'Unsubscribe', 'chargenet' ) . '</button>';
				echo '</form>';
			} else {
				echo '<h1>' . esc_html__( 'This link is not valid', 'chargenet' ) . '</h1>';
				echo '<p>' . esc_html__( 'The link is incorrect or you are already unsubscribed. Email info@chargenet.energy and we will remove you.', 'chargenet' ) . '</p>';
			}
			echo '</section>';
			get_footer();
		}
	);
	exit;
}
add_action( 'template_redirect', 'chargenet_unsubscribe_serve', 1 );

/**
 * Flush the rewrite rules once when this code is first deployed.
 */
function chargenet_unsubscribe_flush(): void {
	if ( '1' !== get_option( 'chargenet_unsubscribe_rules' ) ) {
		flush_rewrite_rules( false );
		update_option( 'chargenet_unsubscribe_rules', '1', true );
	}
}
add_action( 'init', 'chargenet_unsubscribe_flush', 99 );
