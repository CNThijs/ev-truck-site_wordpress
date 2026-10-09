<?php
/**
 * Daily server-side numbers for the first 30 days after launch: `wp eval-file monitor-server.php` (over SSH from
 * bin/monitor.sh, or by hand). Counts form submissions and mail failures, lists the most requested 404 addresses of the last
 * 24 hours (Rank Math 404 monitor), consent choices, pending cron events and the last backups. No personal data is printed.
 */

global $wpdb;
$cn_line = static function ( string $label, $value ): void {
	echo str_pad( $label, 44 ) . $value . "\n";
};

echo "Form submissions (all forms: contact, trend_report, newsletter)\n";
foreach ( array( 1 => 'last 24 hours', 7 => 'last 7 days', 30 => 'last 30 days' ) as $days => $label ) {
	$counts = array();
	foreach ( array( 'contact', 'trend_report', 'newsletter' ) as $form ) {
		$counts[] = $form . ' ' . count(
			get_posts(
				array(
					'post_type'      => CHARGENET_SUBMISSION,
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'date_query'     => array( array( 'after' => "-$days days" ) ),
					'meta_key'       => '_cn_form', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => $form, // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			)
		);
	}
	$cn_line( "  $label", implode( ', ', $counts ) );
}
$failed = get_posts(
	array(
		'post_type'      => CHARGENET_SUBMISSION,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_key'       => '_cn_email_status', // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_value'     => array( 'failed', 'retry' ), // phpcs:ignore WordPress.DB.SlowDBQuery
		'meta_compare'   => 'IN',
	)
);
$cn_line( 'Submissions with a mail problem (failed/retry)', count( $failed ) . ( $failed ? '   <-- CHECK docs/integrations.md' : '' ) );

echo "\nMost requested 404 addresses, last 24 hours (Rank Math 404 monitor)\n";
$table = $wpdb->prefix . 'rank_math_404_logs';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table ) { // phpcs:ignore WordPress.DB
	$rows = $wpdb->get_results( "SELECT uri, SUM(times_accessed) AS hits FROM {$table} WHERE accessed > DATE_SUB(NOW(), INTERVAL 1 DAY) GROUP BY uri ORDER BY hits DESC LIMIT 15" ); // phpcs:ignore WordPress.DB
	if ( ! $rows ) {
		echo "  none\n";
	}
	foreach ( (array) $rows as $row ) {
		$cn_line( '  ' . substr( $row->uri, 0, 40 ), $row->hits . ' hit(s)   -> add to docs/redirects.csv if it is an old address' );
	}
} else {
	echo "  404 monitor table not found\n";
}

echo "\nCookie choices (consent record), last 24 hours\n";
$consent = $wpdb->prefix . 'chargenet_consent';
if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $consent ) ) === $consent ) { // phpcs:ignore WordPress.DB
	$row = $wpdb->get_row( "SELECT COUNT(*) AS total, SUM(statistics) AS accepted FROM {$consent} WHERE created_at > DATE_SUB(UTC_TIMESTAMP(), INTERVAL 1 DAY)" ); // phpcs:ignore WordPress.DB
	$cn_line( '  choices / accepted statistics', (int) $row->total . ' / ' . (int) $row->accepted );
}

echo "\nWordPress\n";
$cn_line( '  WP-Cron next forms purge', wp_next_scheduled( 'chargenet_forms_purge_daily' ) ? gmdate( 'Y-m-d H:i', (int) wp_next_scheduled( 'chargenet_forms_purge_daily' ) ) . ' UTC' : 'NOT SCHEDULED' );
$cn_line( '  Environment type', wp_get_environment_type() . ( 'production' === wp_get_environment_type() ? '' : '   <-- should be production after launch' ) );
$cn_line( '  Search engines allowed', get_option( 'blog_public' ) ? 'yes' : 'NO (Settings > Reading)' );
$backups = glob( getenv( 'HOME' ) . '/chargenet-backups/chargenet-*.tar.gz.enc' );
rsort( $backups );
$cn_line( '  Newest server backup', $backups ? gmdate( 'Y-m-d H:i', filemtime( $backups[0] ) ) . ' UTC' : 'none found' );
