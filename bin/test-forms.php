<?php
/**
 * Checks for the forms (handler, report codes, email templates and headers, retries, privacy tools). Run:
 * `ddev wp eval-file bin/test-forms.php` (npm run test:forms). It uses the local database, creates its own test
 * codes and submissions and removes them again; no email leaves the machine (wp_mail is intercepted).
 */

$GLOBALS['cn_failures'] = 0;
$GLOBALS['cn_checks']   = 0;

/**
 * Assertion.
 *
 * @param bool   $ok   Condition.
 * @param string $what Description.
 */
function cn_check( bool $ok, string $what ): void {
	++$GLOBALS['cn_checks'];
	if ( ! $ok ) {
		++$GLOBALS['cn_failures'];
		echo "FAIL: {$what}\n";
	}
}

/**
 * A valid form token that is old enough.
 */
function cn_old_token(): string {
	$time = (string) ( time() - 20 );
	return $time . '.' . substr( hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) ), 0, 20 );
}

global $wpdb;
$table   = chargenet_codes_table();
// Leftovers of an interrupted run.
$wpdb->query( "DELETE FROM {$table} WHERE code IN ('TEST1','TEST2','ABC12','ZZ999')" ); // phpcs:ignore WordPress.DB
foreach ( get_posts( array( 'post_type' => CHARGENET_SUBMISSION, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ) ) as $leftover ) {
	if ( str_ends_with( (string) chargenet_submission_get( $leftover, 'email' ), '@example.com' ) ) {
		wp_delete_post( $leftover, true );
	}
}
$created = array();
$ip      = '203.0.113.7';
$mails   = array();
add_filter(
	'pre_wp_mail',
	static function ( $short, array $atts ) use ( &$mails ) {
		$mails[] = $atts;
		return $GLOBALS['cn_mail_result'] ?? true;
	},
	10,
	2
);
$base = static fn( array $extra ): array => array_merge(
	array(
		'cn_ts'   => cn_old_token(),
		'cn_lang' => 'en',
		'consent' => '1',
	),
	$extra
);
$reset = static function () use ( $ip ): void {
	foreach ( array( $ip, '203.0.113.99' ) as $address ) {
		foreach ( array( 'codefail', 'submit:contact', 'submit:trend_report', 'submit:newsletter' ) as $bucket ) {
			delete_transient( chargenet_rate_key( $bucket, $address ) );
		}
	}
};
$reset();
add_filter( 'chargenet_mail_bcc_safe', '__return_true' ); // As with SMTP: the BCC checks below look at one message.
$count = static fn(): int => (int) ( new WP_Query( array( 'post_type' => CHARGENET_SUBMISSION, 'post_status' => 'any', 'fields' => 'ids', 'posts_per_page' => -1 ) ) )->found_posts;

// Codes -----------------------------------------------------------------------------------------------------------.
cn_check( 'QGBRUD' === chargenet_code_normalise( ' qg-bR uD ' ), 'code normalising' );
$parsed = chargenet_codes_parse( "\xEF\xBB\xBFcode;company\nAbC12;Acme\nxy;too short\nabc12;dup\nZZ999;Zed\n" );
cn_check( array( 'ABC12' => 'Acme', 'ZZ999' => 'Zed' ) === $parsed['codes'] && 1 === $parsed['invalid'] && 1 === $parsed['duplicates'], 'CSV with header, semicolon, BOM, invalid and duplicate lines' );
$plain = chargenet_codes_parse( "TEST1\r\ntest2\r\n" );
cn_check( array( 'TEST1', 'TEST2' ) === array_keys( $plain['codes'] ), 'CSV without header' );
chargenet_codes_import( array( 'TEST1' => 'Test', 'TEST2' => '' ), 'add' );
cn_check( chargenet_code_exists( 'test-1' ) && ! chargenet_code_exists( 'TEST3' ) && ! chargenet_code_exists( '' ), 'code lookup ignores case and spaces' );

// Handler ---------------------------------------------------------------------------------------------------------.
$before = $count();
$r      = chargenet_form_process( $base( array( 'cn_form' => 'contact', 'website' => 'spam' ) ), $ip );
cn_check( $r['ok'] && $count() === $before, 'honeypot: looks fine to the bot, nothing stored' );

$r = chargenet_form_process( array( 'cn_form' => 'contact', 'cn_ts' => chargenet_form_token(), 'consent' => '1', 'name' => 'Ann', 'email' => 'a@example.com', 'message' => 'Hello there, this is long' ), $ip );
cn_check( ! $r['ok'] && $count() === $before, 'minimum time: a form sent at once is refused' );

$r = chargenet_form_process( $base( array( 'cn_form' => 'contact', 'name' => 'A', 'email' => 'bad', 'message' => 'short' ) ), $ip );
cn_check( ! $r['ok'] && isset( $r['errors']['name'], $r['errors']['email'], $r['errors']['message'] ), 'contact: validation errors per field' );

$r = chargenet_form_process( $base( array( 'cn_form' => 'contact', 'consent' => '', 'name' => 'Ann Lee', 'email' => 'ann@example.com', 'message' => 'Hello there, this is long' ) ), $ip );
cn_check( ! $r['ok'] && isset( $r['errors']['consent'] ), 'consent is required' );

$mails = array();
$r     = chargenet_form_process( $base( array( 'cn_form' => 'contact', 'name' => "Ann <b>Lee</b>\r\nBcc: evil@example.com", 'email' => 'ann@example.com', 'message' => "Tom & Jerry <script>alert(1)</script>\nsecond line", 'cn_utm_source' => 'dm', 'cn_utm_term' => 'x<y' ) ), $ip );
$id    = $r['id'];
$created[] = $id;
cn_check( $r['ok'] && $id > 0, 'contact: stored' );
cn_check( 1 === count( $mails ), 'contact: one email' );
$atts = $mails[0] ?? array( 'to' => '', 'headers' => array(), 'message' => '', 'subject' => '' );
$hdrs = implode( "\n", (array) $atts['headers'] );
cn_check( 'info@chargenet.energy' === $atts['to'], 'contact: goes to the team address' );
cn_check( false !== strpos( $hdrs, 'Bcc: ann@example.com' ), 'contact: visitor in BCC' );
cn_check( 1 === preg_match( '/^From: [^"\r\n<]+ <info@chargenet\.energy>$/m', $hdrs ), 'contact: sent from the authenticated address with the visitor name' );
cn_check( 1 === preg_match( '/^Reply-To: [^"\r\n<]+ <ann@example\.com>$/m', $hdrs ), 'contact: visitor address in Reply-To' );
cn_check( 1 === substr_count( strtolower( $hdrs ), 'bcc:' ), 'contact: a name cannot inject a header' );
cn_check( false === strpos( $atts['message'], '<script' ) && false !== strpos( $atts['message'], 'Tom &amp; Jerry' ) && false !== strpos( $atts['message'], '<br' ), 'contact: message is cleaned and escaped in the HTML, line breaks kept' );
cn_check( false !== strpos( $atts['subject'], 'Ann' ), 'contact: subject has the name' );
cn_check( 'sent' === chargenet_submission_get( $id, 'email_status' ) && 1 === (int) chargenet_submission_get( $id, 'email_attempts' ), 'contact: delivery status stored' );
cn_check( 'dm' === ( chargenet_submission_get( $id, 'utm' )['utm_source'] ?? '' ) && 'xy' === ( chargenet_submission_get( $id, 'utm' )['utm_term'] ?? '' ), 'campaign parameters stored and cleaned' );
cn_check( '' !== (string) chargenet_submission_get( $id, 'consent_text' ) && (int) chargenet_submission_get( $id, 'consent_at' ) > 0, 'consent text and time stored' );

$mails = array();
$r     = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'code', 'code' => ' test-1 ', 'email' => 'visitor@example.com' ) ), $ip );
$created[] = $r['id'];
cn_check( $r['ok'], 'trend report: valid code (case and spaces ignored)' );
$atts = $mails[0] ?? array( 'to' => '', 'headers' => array(), 'message' => '' );
$hdrs = implode( "\n", (array) $atts['headers'] );
cn_check( false !== strpos( $atts['to'], 'visitor@example.com' ), 'trend report: goes to the visitor' );
cn_check( false !== strpos( $hdrs, 'Bcc: info@chargenet.energy' ) && false !== strpos( $hdrs, '<noreply@chargenet.energy>' ), 'trend report: from noreply, team in BCC' );
cn_check( false !== strpos( $atts['message'], 'downloads/ChargeNet-TR2027.pdf' ), 'trend report: the email has the report link' );
cn_check( 'TEST1' === chargenet_submission_get( $r['id'], 'code' ), 'trend report: code recorded on the submission' );
$r2 = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'code', 'code' => 'TEST1', 'email' => 'other@example.com' ) ), $ip );
$created[] = $r2['id'];
cn_check( $r2['ok'] && 2 === (int) $wpdb->get_var( "SELECT use_count FROM {$table} WHERE code = 'TEST1'" ), 'trend report: code is reusable and every use is counted' ); // phpcs:ignore WordPress.DB

$mails = array();
$r     = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'code', 'code' => 'NOPE1', 'email' => 'v@example.com', 'cn_lang' => 'nl' ) ), $ip );
cn_check( ! $r['ok'] && isset( $r['errors']['code'] ) && false !== strpos( $r['errors']['code'], 'niet geldig' ) && ! $mails, 'invalid code: Dutch error on the code field, no email' );
for ( $i = 0; $i < 5; $i++ ) {
	$r = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'code', 'code' => 'NOPE' . $i, 'email' => 'v@example.com' ) ), $ip );
}
cn_check( ! $r['ok'] && empty( $r['errors'] ) && false !== strpos( $r['message'], 'Too many attempts' ), 'invalid codes: locked after five failures' );
$r = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'code', 'code' => 'TEST1', 'email' => 'v@example.com' ) ), $ip );
cn_check( ! $r['ok'], 'invalid codes: even a valid code waits while locked' );
$r = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'code', 'code' => 'TEST1', 'email' => 'v@example.com' ) ), '203.0.113.99' );
$created[] = $r['id'];
cn_check( $r['ok'], 'invalid codes: another visitor is not affected' );
$reset();

$r = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'nocode', 'name' => 'Bo Berg', 'company' => 'Berg BV', 'email' => 'bo@example.com' ) ), $ip );
$created[] = $r['id'];
cn_check( $r['ok'] && '' === chargenet_submission_get( $r['id'], 'code' ) && 'Berg BV' === chargenet_submission_get( $r['id'], 'company' ), 'trend report without code: name, company and email' );
$r = chargenet_form_process( $base( array( 'cn_form' => 'trend_report', 'cn_mode' => 'nocode', 'name' => '', 'company' => '', 'email' => 'bo@example.com' ) ), $ip );
cn_check( ! $r['ok'] && isset( $r['errors']['name'], $r['errors']['company'] ), 'trend report without code: name and company required' );

for ( $i = 0; $i < 6; $i++ ) {
	chargenet_rate_add( 'submit:contact', $ip, 3600 );
}
$r = chargenet_form_process( $base( array( 'cn_form' => 'contact', 'name' => 'Ann Lee', 'email' => 'ann@example.com', 'message' => 'Hello there, this is long' ) ), $ip );
cn_check( ! $r['ok'] && false !== strpos( $r['message'], 'Too many requests' ), 'rate limit per visitor and form' );
$reset();

$r = chargenet_form_process( $base( array( 'cn_form' => 'contact', 'cn_lang' => 'nl', 'name' => 'Nina Nel', 'email' => 'nina@example.com', 'message' => 'Een bericht dat lang genoeg is' ) ), $ip );
$created[] = $r['id'];
cn_check( $r['ok'] && 0 === strpos( $r['message'], 'Bedankt voor uw bericht' ), 'success message is in the language of the form' );
cn_check( 0 === strpos( (string) chargenet_submission_get( $r['id'], 'consent_text' ), 'Ik ga ermee akkoord' ), 'consent text is stored in the language of the form' );
$reset();

// Newsletter sign-up.
$mails = array();
remove_all_filters( 'pre_wp_mail' );
add_filter( 'pre_wp_mail', static function ( $short, array $atts ) use ( &$mails ) {
	$mails[] = $atts;
	return true;
}, 10, 2 );
$r = chargenet_form_process( $base( array( 'cn_form' => 'newsletter', 'email' => 'sub@example.com', 'cn_lang' => 'nl' ) ), $ip );
$created[] = $r['id'];
cn_check( $r['ok'] && $r['id'] > 0 && 'newsletter' === chargenet_submission_get( $r['id'], 'form' ), 'newsletter: stored' );
cn_check( 1 === count( $mails ) && false !== strpos( implode( "\n", (array) $mails[0]['headers'] ), 'noreply@chargenet.energy' ) && false === stripos( implode( "\n", (array) $mails[0]['headers'] ), 'bcc' ), 'newsletter: one confirmation from noreply, no team copy' );
cn_check( 0 === strpos( $r['message'], 'Bedankt voor uw aanmelding' ), 'newsletter: its own Dutch message' );
cn_check( false !== strpos( (string) chargenet_submission_get( $r['id'], 'consent_text' ), 'nieuwsbrief' ), 'newsletter: its own consent sentence' );
$mails = array();
$r2    = chargenet_form_process( $base( array( 'cn_form' => 'newsletter', 'email' => 'sub@example.com' ) ), $ip );
cn_check( $r2['ok'] && 0 === $r2['id'] && ! $mails && 1 === count( chargenet_submissions_by_email( 'sub@example.com', 'newsletter' ) ), 'newsletter: subscribing twice stores and sends nothing again' );
$r3 = chargenet_form_process( $base( array( 'cn_form' => 'newsletter', 'email' => 'nope', 'consent' => '' ) ), $ip );
cn_check( ! $r3['ok'] && isset( $r3['errors']['email'], $r3['errors']['consent'] ), 'newsletter: email and consent are required' );
$old_sub = $r['id'];
wp_update_post( array( 'ID' => $old_sub, 'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( '-3 years' ) ), 'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( '-3 years' ) ) ) );
chargenet_forms_purge();
cn_check( null !== get_post( $old_sub ), 'newsletter: subscriptions are kept when other submissions expire' );
remove_all_filters( 'pre_wp_mail' );
$reset();

// Emails ----------------------------------------------------------------------------------------------------------.
foreach ( array( 'contact', 'trend_report' ) as $form ) {
	foreach ( array( 'en', 'nl' ) as $lang ) {
		$m = chargenet_mail_render( $form, $lang, array( 'name' => 'Ann', 'email' => 'ann@example.com', 'message' => 'Hi', 'code' => 'TEST1', 'company' => 'Acme' ) );
		cn_check( 1 !== preg_match( '/\{[a-z_]+\}/', $m['subject'] . $m['html'] . $m['text'] ), "{$form}/{$lang}: no placeholder left over" );
		cn_check( false !== strpos( $m['html'], '<html lang="' . $lang . '"' ) && '' !== trim( $m['text'] ) && false === strpos( $m['text'], '<' ), "{$form}/{$lang}: HTML and plain text" );
	}
}
$m = chargenet_mail_render( 'trend_report', 'nl', array( 'name' => '', 'email' => 'ann@example.com' ) );
cn_check( false !== strpos( $m['text'], 'Goedendag,' ) && false !== strpos( $m['text'], 'ChargeNet-TR2027.pdf' ), 'trend report NL without a name: neutral greeting, link in the text version' );
update_option( CHARGENET_TEMPLATES_OPTION, array( 'contact' => array( 'en' => array( 'subject' => 'Custom {name}', 'body' => '<p>Custom body for {name}</p>' ) ) ) );
$m = chargenet_mail_render( 'contact', 'en', array( 'name' => 'Ann', 'email' => 'a@example.com', 'message' => 'x' ) );
cn_check( 'Custom Ann' === $m['subject'] && false !== strpos( $m['html'], 'Custom body for Ann' ), 'an edited template replaces the default' );
delete_option( CHARGENET_TEMPLATES_OPTION );

// The real message: no BCC header with SMTP, plain text part present.
$captured = '';
$hook     = static function ( $mailer ) use ( &$captured ) {
	$mailer->Mailer = 'smtp';
	$mailer->preSend();
	$captured = $mailer->getSentMIMEMessage();
	$mailer->Host    = '127.0.0.1'; // Nothing listens here: the send fails at once and nothing leaves the machine.
	$mailer->Port    = 1;
	$mailer->Timeout = 2;
};
remove_all_filters( 'pre_wp_mail' );
add_action( 'phpmailer_init', $hook, 99 );
chargenet_mail_send( 'contact', 'en', array( 'name' => 'Ann', 'email' => 'ann@example.com', 'message' => 'Hello there, this is long' ) );
remove_action( 'phpmailer_init', $hook, 99 );
cn_check( '' !== $captured && 1 !== preg_match( '/^Bcc:/mi', $captured ), 'MIME message has no visible Bcc header (SMTP)' );
cn_check( false !== strpos( $captured, 'multipart/alternative' ) && false !== stripos( $captured, 'text/plain' ) && false !== stripos( $captured, 'text/html' ), 'MIME message has a plain text and an HTML part' );
cn_check( 1 === preg_match( '/^To: .*info@chargenet\.energy/mi', $captured ), 'MIME message is addressed to the team' );

// Without SMTP no BCC header is written: the team copy is a second message.
remove_all_filters( 'chargenet_mail_bcc_safe' );
$mails = array();
add_filter( 'pre_wp_mail', static function ( $short, array $atts ) use ( &$mails ) {
	$mails[] = $atts;
	return true;
}, 10, 2 );
chargenet_mail_send( 'trend_report', 'en', array( 'name' => 'Ann', 'email' => 'ann@example.com' ) );
cn_check( 2 === count( $mails ) && 'info@chargenet.energy' === $mails[1]['to'] && false === stripos( implode( "\n", array_merge( (array) $mails[0]['headers'], (array) $mails[1]['headers'] ) ), 'bcc' ), 'no SMTP: the BCC is a second message, no BCC header' );
remove_all_filters( 'pre_wp_mail' );

// Failure and retry -----------------------------------------------------------------------------------------------.
add_filter(
	'pre_wp_mail',
	static function () {
		return false;
	}
);
$mailer_fail = chargenet_submission_create( array( 'form' => 'contact', 'lang' => 'en', 'name' => 'Fail Test', 'email' => 'fail@example.com', 'message' => 'Hello there, long enough', 'consent_text' => 'x' ) );
$created[]   = $mailer_fail;
cn_check( false === chargenet_mail_attempt( $mailer_fail ) && 'retry' === chargenet_submission_get( $mailer_fail, 'email_status' ), 'failed email: status retry' );
cn_check( false !== wp_next_scheduled( 'chargenet_mail_retry', array( $mailer_fail ) ), 'failed email: a retry is scheduled' );
for ( $i = 0; $i < 4; $i++ ) {
	chargenet_mail_attempt( $mailer_fail );
}
cn_check( 'failed' === chargenet_submission_get( $mailer_fail, 'email_status' ) && 5 === (int) chargenet_submission_get( $mailer_fail, 'email_attempts' ), 'failed email: gives up after five attempts' );
wp_clear_scheduled_hook( 'chargenet_mail_retry', array( $mailer_fail ) );

// Storage and privacy ---------------------------------------------------------------------------------------------.
$type = get_post_type_object( CHARGENET_SUBMISSION );
cn_check( ! $type->public && ! $type->show_in_rest && 'manage_options' === $type->cap->edit_posts && 'do_not_allow' === $type->cap->create_posts, 'submissions: private, not in the REST API, administrators only' );
cn_check( "'=1+1" === chargenet_csv_cell( '=1+1' ) && 'plain' === chargenet_csv_cell( 'plain' ), 'CSV export neutralises spreadsheet formulas' );
$export = chargenet_privacy_export( 'ann@example.com' );
cn_check( ! empty( $export['data'] ), 'privacy exporter finds the submissions of an address' );
$erase = chargenet_privacy_erase( 'ann@example.com' );
cn_check( $erase['items_removed'] && ! chargenet_submissions_by_email( 'ann@example.com' ), 'privacy eraser deletes them' );
$old = chargenet_submission_create( array( 'form' => 'contact', 'lang' => 'en', 'name' => 'Old', 'email' => 'old@example.com', 'message' => 'Hello there, long enough', 'consent_text' => 'x' ) );
wp_update_post( array( 'ID' => $old, 'post_date' => gmdate( 'Y-m-d H:i:s', strtotime( '-2 years' ) ), 'post_date_gmt' => gmdate( 'Y-m-d H:i:s', strtotime( '-2 years' ) ) ) );
chargenet_forms_purge();
cn_check( null === get_post( $old ), 'retention: submissions older than the retention period are deleted' );

// Clean up --------------------------------------------------------------------------------------------------------.
foreach ( $created as $id ) {
	if ( $id ) {
		wp_delete_post( (int) $id, true );
	}
}
$wpdb->query( "DELETE FROM {$table} WHERE code IN ('TEST1','TEST2','ABC12','ZZ999')" ); // phpcs:ignore WordPress.DB

echo "{$GLOBALS['cn_checks']} checks, {$GLOBALS['cn_failures']} failed.\n";
if ( $GLOBALS['cn_failures'] ) {
	WP_CLI::halt( 1 );
}
