<?php
/**
 * Send the form emails to a real inbox, to check delivery (Gmail, Outlook, ...). Run:
 * `wp eval-file bin/test-mail.php you@example.com` (over SSH on the live site, so the real SMTP settings are used).
 * Sends the contact email and the trend report email in English and Dutch, without the team copy, and prints the result.
 */

$to = isset( $args[0] ) ? sanitize_email( (string) $args[0] ) : '';
if ( ! is_email( $to ) ) {
	WP_CLI::error( 'Usage: wp eval-file bin/test-mail.php you@example.com' );
}
foreach ( array( 'contact', 'trend_report' ) as $form ) {
	foreach ( array( 'en', 'nl' ) as $lang ) {
		$ok = chargenet_mail_send(
			$form,
			$lang,
			array(
				'name'    => 'Test Visitor',
				'email'   => $to,
				'company' => 'Test Company',
				'message' => 'This is a test message from bin/test-mail.php.',
				'code'    => 'TEST1',
			),
			true
		);
		echo ( $ok ? 'sent   ' : 'FAILED ' ) . "{$form} ({$lang})" . ( $ok ? '' : ': ' . ( $GLOBALS['chargenet_mail_err'] ?? '' ) ) . "\n";
	}
}
echo "Now check the inbox and the spam folder, and view the original message: SPF, DKIM and DMARC should all say PASS.\n";
