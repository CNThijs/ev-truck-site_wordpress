<?php
/**
 * Emails of the forms: one editable template per form and language (subject and body with placeholders, HTML and a
 * plain-text version made from it), sent with wp_mail, delivery status per submission and retries.
 *
 * Contact form: one message to the team address, the visitor in BCC (so they receive the same email), sent from an
 * authenticated chargenet.energy address with the visitor's name as display name and their address in Reply-To.
 * Trend report: to the visitor from noreply@, the team in BCC. A BCC address is never put in a visible header: with
 * SMTP the BCC only exists in the envelope. Sending goes through Microsoft 365 (see docs/integrations.md).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_MAIL_MAX_ATTEMPTS = 5;

/**
 * Default template of a form in a language.
 *
 * @param string $form contact or trend_report.
 * @param string $lang en or nl.
 * @return array{subject: string, body: string}
 */
function chargenet_mail_default_template( string $form, string $lang ): array {
	$t = array(
		'trend_report' => array(
			'en' => array(
				'subject' => 'Your Trend Report 2027',
				'body'    => '<p>{greeting}</p><p>Thank you for your interest in the ChargeNet Trend Report 2027. You can download the report here:</p><p><a href="{report_url}">Download the Trend Report 2027</a></p><p>Do you have questions? Just reply to this email.</p><p>Kind regards,<br>The ChargeNet team</p>',
			),
			'nl' => array(
				'subject' => 'Uw Trendrapport 2027',
				'body'    => '<p>{greeting}</p><p>Bedankt voor uw interesse in het ChargeNet Trendrapport 2027. U kunt het rapport hier downloaden:</p><p><a href="{report_url}">Download het Trendrapport 2027</a></p><p>Heeft u vragen? Antwoord gerust op deze e-mail.</p><p>Met vriendelijke groet,<br>Het ChargeNet-team</p>',
			),
		),
		'contact'      => array(
			'en' => array(
				'subject' => 'Contact form: {name}',
				'body'    => '<p>Thank you for contacting ChargeNet. This is the message that was sent through our contact form; we will get back to you as soon as possible.</p><p><strong>Name:</strong> {name}<br><strong>Email:</strong> {email}</p><p><strong>Message:</strong><br>{message}</p><p>Received on {date}.</p>',
			),
			'nl' => array(
				'subject' => 'Contactformulier: {name}',
				'body'    => '<p>Bedankt voor uw bericht aan ChargeNet. Dit is het bericht dat via ons contactformulier is verstuurd; wij nemen zo snel mogelijk contact met u op.</p><p><strong>Naam:</strong> {name}<br><strong>E-mail:</strong> {email}</p><p><strong>Bericht:</strong><br>{message}</p><p>Ontvangen op {date}.</p>',
			),
		),
	);
	return $t[ $form ][ $lang ] ?? $t['contact']['en'];
}

/**
 * Template in use: the edited one, or the default for any empty part.
 *
 * @param string $form contact or trend_report.
 * @param string $lang en or nl.
 * @return array{subject: string, body: string}
 */
function chargenet_mail_template( string $form, string $lang ): array {
	$default = chargenet_mail_default_template( $form, $lang );
	$stored  = (array) ( ( (array) get_option( CHARGENET_TEMPLATES_OPTION, array() ) )[ $form ][ $lang ] ?? array() );
	return array(
		'subject' => '' !== trim( (string) ( $stored['subject'] ?? '' ) ) ? (string) $stored['subject'] : $default['subject'],
		'body'    => '' !== trim( wp_strip_all_tags( (string) ( $stored['body'] ?? '' ) ) ) ? (string) $stored['body'] : $default['body'],
	);
}

/**
 * Plain text made from the HTML of an email: paragraphs become blank lines, links show their address.
 *
 * @param string $html HTML.
 */
function chargenet_mail_html_to_text( string $html ): string {
	$text = preg_replace( '#<a\s[^>]*href="([^"]*)"[^>]*>(.*?)</a>#is', '$2 ($1)', $html );
	$text = preg_replace( '#<br\s*/?>#i', "\n", (string) $text );
	$text = preg_replace( '#</(p|div|h[1-6]|li|tr)>#i', "\n\n", (string) $text );
	$text = html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return trim( (string) preg_replace( "/\n{3,}/", "\n\n", (string) $text ) ) . "\n";
}

/**
 * Render a form's email: subject, HTML (in the layout) and plain text. Placeholder values are escaped.
 *
 * @param string               $form contact or trend_report.
 * @param string               $lang en or nl.
 * @param array<string, mixed> $v    name, email, company, message, code.
 * @return array{subject: string, html: string, text: string}
 */
function chargenet_mail_render( string $form, string $lang, array $v ): array {
	$tpl      = chargenet_mail_template( $form, $lang );
	$name     = trim( (string) ( $v['name'] ?? '' ) );
	$greeting = 'nl' === $lang ? ( '' !== $name ? 'Beste ' . $name . ',' : 'Goedendag,' ) : ( '' !== $name ? 'Hello ' . $name . ',' : 'Hello,' );
	$date     = wp_date( 'nl' === $lang ? 'j F Y, H:i' : 'F j, Y, H:i' );

	$plain                       = array(
		'{name}'      => $name,
		'{email}'     => (string) ( $v['email'] ?? '' ),
		'{company}'   => (string) ( $v['company'] ?? '' ),
		'{code}'      => (string) ( $v['code'] ?? '' ),
		'{site_name}' => 'ChargeNet',
		'{date}'      => $date,
		'{greeting}'  => $greeting,
	);
	$html_values                 = array_map( 'esc_html', $plain );
	$html_values['{message}']    = nl2br( esc_html( (string) ( $v['message'] ?? '' ) ) );
	$html_values['{report_url}'] = esc_url( (string) chargenet_forms_setting( 'report_url' ) );
	$plain['{message}']          = (string) ( $v['message'] ?? '' );
	$plain['{report_url}']       = (string) chargenet_forms_setting( 'report_url' );

	$subject = trim( (string) preg_replace( '/\s+/', ' ', strtr( $tpl['subject'], $plain ) ) );
	$body    = wp_kses_post( strtr( $tpl['body'], $html_values ) );

	ob_start();
	chargenet_mail_layout( $body, $lang );
	$html = (string) ob_get_clean();

	return array(
		'subject' => $subject,
		'html'    => $html,
		'text'    => chargenet_mail_html_to_text( $body ) . "\nChargeNet\nhttps://chargenet.energy\n",
	);
}

/**
 * HTML layout of every email: a simple table layout with inline styles (mail clients ignore style sheets).
 *
 * @param string $body Body HTML.
 * @param string $lang Language.
 */
function chargenet_mail_layout( string $body, string $lang ): void {
	?>
<!doctype html>
<html lang="<?php echo esc_attr( $lang ); ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f5f2;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f5f2;">
<tr><td align="center" style="padding:24px 12px;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;overflow:hidden;font-family:Arial,Helvetica,sans-serif;color:#1a1f1a;">
<tr><td style="background:#083a0b;padding:20px 28px;color:#fae104;font-size:20px;font-weight:bold;letter-spacing:1px;">CHARGENET</td></tr>
<tr><td style="padding:28px;font-size:16px;line-height:1.55;"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised with wp_kses_post. ?></td></tr>
<tr><td style="padding:16px 28px;background:#f4f5f2;font-size:12px;color:#5b655b;">ChargeNet B.V. · Industrial Park Kleefsewaard, Westervoortsedijk 73, 6827 AV Arnhem · <a href="https://chargenet.energy" style="color:#0f5f1a;">chargenet.energy</a></td></tr>
</table>
</td></tr>
</table>
</body>
</html>
	<?php
}

/**
 * A name that is safe in a header: no line breaks, quotes, brackets or separators.
 *
 * @param string $name Raw name.
 */
function chargenet_mail_header_name( string $name ): string {
	$name = trim( (string) preg_replace( '/[\x00-\x1F\x7F"<>,;:@]+/u', ' ', $name ) );
	return mb_substr( (string) preg_replace( '/\s+/', ' ', $name ), 0, 80 );
}

/**
 * An address with a display name for a header, or the bare address.
 *
 * @param string $email Address.
 * @param string $name  Display name.
 */
function chargenet_mail_address( string $email, string $name = '' ): string {
	$name = chargenet_mail_header_name( $name );
	return '' !== $name ? sprintf( '%s <%s>', $name, $email ) : $email; // No quotes: wp_mail keeps them in the name.
}

/**
 * Recipients and headers of a form's email.
 *
 * @param string               $form contact or trend_report.
 * @param array<string, mixed> $v    Submission values.
 * @param bool                 $test Test: only to the given address, no team copy.
 * @return array{to: string, headers: string[], bcc: string}
 */
function chargenet_mail_envelope( string $form, array $v, bool $test = false ): array {
	$bcc     = '';
	$visitor = (string) $v['email'];
	$name    = (string) ( $v['name'] ?? '' );
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	if ( 'contact' === $form ) {
		$to        = $test ? $visitor : (string) chargenet_forms_setting( 'contact_to' );
		$headers[] = 'From: ' . chargenet_mail_address( (string) chargenet_forms_setting( 'contact_from' ), '' !== $name ? $name : 'ChargeNet website' );
		$headers[] = 'Reply-To: ' . chargenet_mail_address( $visitor, $name );
		$bcc       = $test ? '' : $visitor;
	} else {
		$to        = chargenet_mail_address( $visitor, $name );
		$headers[] = 'From: ' . chargenet_mail_address( (string) chargenet_forms_setting( 'trend_from' ), 'ChargeNet' );
		$headers[] = 'Reply-To: ' . chargenet_mail_address( (string) chargenet_forms_setting( 'contact_to' ), 'ChargeNet' );
		$bcc       = $test ? '' : (string) chargenet_forms_setting( 'trend_bcc' );
	}
	return array(
		'to'      => $to,
		'headers' => $headers,
		'bcc'     => $bcc,
	);
}

/**
 * Is mail sent through SMTP? Only then is a BCC safe: the BCC address exists in the envelope and in no header. With
 * PHP's own mail the BCC would be written into the message headers, so a separate copy is sent instead.
 */
function chargenet_mail_bcc_safe(): bool {
	return (bool) apply_filters( 'chargenet_mail_bcc_safe', defined( 'CHARGENET_SMTP_MODE' ) && defined( 'CHARGENET_SMTP_USER' ) );
}

/**
 * Send the email of a form.
 *
 * @param string               $form contact or trend_report.
 * @param string               $lang en or nl.
 * @param array<string, mixed> $v    Submission values.
 * @param bool                 $test Test send (no team copy).
 */
function chargenet_mail_send( string $form, string $lang, array $v, bool $test = false ): bool {
	if ( ! is_email( (string) ( $v['email'] ?? '' ) ) ) {
		return false;
	}
	$mail                          = chargenet_mail_render( $form, $lang, $v );
	$envelope                      = chargenet_mail_envelope( $form, $v, $test );
	$GLOBALS['chargenet_mail_alt'] = $mail['text'];
	$GLOBALS['chargenet_mail_err'] = '';
	$headers                       = $envelope['headers'];
	$extra                         = '';
	if ( '' !== $envelope['bcc'] ) {
		if ( chargenet_mail_bcc_safe() ) {
			$headers[] = 'Bcc: ' . $envelope['bcc'];
		} else {
			$extra = $envelope['bcc']; // A second, identical message to the BCC address (no BCC header anywhere).
		}
	}
	$sent = wp_mail( $envelope['to'], $mail['subject'], $mail['html'], $headers );
	if ( $sent && '' !== $extra ) {
		wp_mail( $extra, $mail['subject'], $mail['html'], $headers );
	}
	unset( $GLOBALS['chargenet_mail_alt'] );
	return (bool) $sent;
}

/**
 * Plain-text part, timeouts and the Microsoft 365 transport.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $mailer Mailer.
 */
function chargenet_mail_init( $mailer ): void { // phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- PHPMailer's own property names.
	if ( ! empty( $GLOBALS['chargenet_mail_alt'] ) ) {
		$mailer->AltBody = (string) $GLOBALS['chargenet_mail_alt'];
	}
	$mailer->Timeout = 15;

	$mode = defined( 'CHARGENET_SMTP_MODE' ) ? (string) CHARGENET_SMTP_MODE : '';
	if ( '' === $mode || ! defined( 'CHARGENET_SMTP_USER' ) ) {
		return; // Not configured (local development: DDEV's Mailpit catches the mail).
	}
	$mailer->isSMTP();
	$mailer->Host       = defined( 'CHARGENET_SMTP_HOST' ) ? (string) CHARGENET_SMTP_HOST : 'smtp.office365.com';
	$mailer->Port       = defined( 'CHARGENET_SMTP_PORT' ) ? (int) CHARGENET_SMTP_PORT : 587;
	$mailer->SMTPSecure = 'tls';
	$mailer->SMTPAuth   = true;
	$mailer->Username   = (string) CHARGENET_SMTP_USER;
	if ( 'oauth' === $mode && defined( 'CHARGENET_MS_TENANT_ID' ) && defined( 'CHARGENET_MS_CLIENT_ID' ) && defined( 'CHARGENET_MS_CLIENT_SECRET' ) ) {
		require_once __DIR__ . '/class-chargenet-mail-oauth.php';
		$mailer->AuthType = 'XOAUTH2';
		$mailer->setOAuth( new Chargenet_Mail_OAuth( (string) CHARGENET_MS_TENANT_ID, (string) CHARGENET_MS_CLIENT_ID, (string) CHARGENET_MS_CLIENT_SECRET, (string) CHARGENET_SMTP_USER ) );
	} elseif ( defined( 'CHARGENET_SMTP_PASS' ) ) {
		$mailer->Password = (string) CHARGENET_SMTP_PASS;
	}
	// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
}
add_action( 'phpmailer_init', 'chargenet_mail_init' );

/**
 * Remember why wp_mail failed.
 *
 * @param WP_Error $error Error.
 */
function chargenet_mail_failed( WP_Error $error ): void {
	$GLOBALS['chargenet_mail_err'] = mb_substr( $error->get_error_message(), 0, 300 );
}
add_action( 'wp_mail_failed', 'chargenet_mail_failed' );

/**
 * Send (or resend) the email of a stored submission and record the result. A failure is retried later, up to five
 * times, with growing waits.
 *
 * @param int $id Submission id.
 * @return bool Sent.
 */
function chargenet_mail_attempt( int $id ): bool {
	if ( CHARGENET_SUBMISSION !== get_post_type( $id ) ) {
		return false;
	}
	$values = array();
	foreach ( array( 'name', 'email', 'company', 'message', 'code' ) as $key ) {
		$values[ $key ] = (string) chargenet_submission_get( $id, $key );
	}
	$attempts = (int) chargenet_submission_get( $id, 'email_attempts' ) + 1;
	$sent     = chargenet_mail_send( (string) chargenet_submission_get( $id, 'form' ), (string) chargenet_submission_get( $id, 'lang' ), $values );

	update_post_meta( $id, '_cn_email_attempts', $attempts );
	if ( $sent ) {
		update_post_meta( $id, '_cn_email_status', 'sent' );
		update_post_meta( $id, '_cn_email_sent_at', time() );
		update_post_meta( $id, '_cn_email_error', '' );
		return true;
	}
	update_post_meta( $id, '_cn_email_error', (string) ( $GLOBALS['chargenet_mail_err'] ?? 'Unknown error' ) );
	if ( $attempts < CHARGENET_MAIL_MAX_ATTEMPTS ) {
		update_post_meta( $id, '_cn_email_status', 'retry' );
		$wait = array( 300, 1800, 7200, 43200 )[ $attempts - 1 ] ?? 86400;
		wp_schedule_single_event( time() + $wait, 'chargenet_mail_retry', array( $id ) );
	} else {
		update_post_meta( $id, '_cn_email_status', 'failed' );
	}
	return false;
}

/**
 * Cron: retry a failed email.
 *
 * @param int $id Submission id.
 */
function chargenet_mail_retry( int $id ): void {
	if ( 'retry' === chargenet_submission_get( $id, 'email_status' ) ) {
		chargenet_mail_attempt( $id );
	}
}
add_action( 'chargenet_mail_retry', 'chargenet_mail_retry' );
