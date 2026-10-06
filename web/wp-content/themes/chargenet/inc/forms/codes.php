<?php
/**
 * Trend report codes: a private table (code, label, use count, last use), filled by a CSV import in the admin.
 * Codes are checked on the server only and never sent to the browser. They are reusable: a use is recorded but the
 * code stays valid. Matching ignores case and anything that is not a letter or digit.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_DB_VERSION = '1';

/**
 * Name of the codes table.
 */
function chargenet_codes_table(): string {
	global $wpdb;
	return $wpdb->prefix . 'chargenet_codes';
}

/**
 * Create the table (once per database version).
 */
function chargenet_codes_install(): void {
	if ( CHARGENET_DB_VERSION === get_option( 'chargenet_forms_db_version' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table   = chargenet_codes_table();
	$charset = $wpdb->get_charset_collate();
	dbDelta(
		"CREATE TABLE {$table} (
			code varchar(32) NOT NULL,
			label varchar(190) NOT NULL DEFAULT '',
			imported_at datetime NOT NULL,
			use_count int(10) unsigned NOT NULL DEFAULT 0,
			last_used_at datetime DEFAULT NULL,
			PRIMARY KEY  (code)
		) {$charset};"
	);
	update_option( 'chargenet_forms_db_version', CHARGENET_DB_VERSION, true );
}
add_action( 'init', 'chargenet_codes_install', 5 );

/**
 * Normalise a code as typed: letters and digits only, upper case.
 *
 * @param string $code Raw code.
 */
function chargenet_code_normalise( string $code ): string {
	return strtoupper( (string) preg_replace( '/[^A-Za-z0-9]/', '', $code ) );
}

/**
 * Is this code on the list?
 *
 * @param string $code Raw code.
 */
function chargenet_code_exists( string $code ): bool {
	global $wpdb;
	$code = chargenet_code_normalise( $code );
	if ( '' === $code || strlen( $code ) > 32 ) {
		return false;
	}
	$table = chargenet_codes_table();
	return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT 1 FROM {$table} WHERE code = %s", $code ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Record a use of a code (count and time). The code stays valid.
 *
 * @param string $code Raw code.
 */
function chargenet_code_record_use( string $code ): void {
	global $wpdb;
	$table = chargenet_codes_table();
	$wpdb->query( $wpdb->prepare( "UPDATE {$table} SET use_count = use_count + 1, last_used_at = %s WHERE code = %s", current_time( 'mysql', true ), chargenet_code_normalise( $code ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Parse CSV text into codes. The first row may be a header: the column named "code" (or the first column) holds the
 * code; a column named "label", "company" or "name" is kept as the label. Comma, semicolon and tab are understood.
 *
 * @param string $text File contents.
 * @return array{codes: array<string, string>, invalid: int, duplicates: int}
 */
function chargenet_codes_parse( string $text ): array {
	$text  = preg_replace( '/^\xEF\xBB\xBF/', '', $text );
	$lines = preg_split( '/\R/', (string) $text, -1, PREG_SPLIT_NO_EMPTY );
	$delim = ',';
	if ( $lines ) {
		$counts = array(
			','  => substr_count( $lines[0], ',' ),
			';'  => substr_count( $lines[0], ';' ),
			"\t" => substr_count( $lines[0], "\t" ),
		);
		arsort( $counts );
		$delim = (string) array_key_first( $counts );
	}
	$code_col  = 0;
	$label_col = -1;
	$result    = array(
		'codes'      => array(),
		'invalid'    => 0,
		'duplicates' => 0,
	);
	foreach ( $lines as $i => $line ) {
		$row = str_getcsv( $line, $delim, '"', '\\' );
		if ( 0 === $i ) {
			$names = array_map( static fn( $c ) => strtolower( trim( (string) $c ) ), $row );
			if ( in_array( 'code', $names, true ) ) { // A header row.
				$code_col = (int) array_search( 'code', $names, true );
				foreach ( array( 'label', 'company', 'name' ) as $candidate ) {
					if ( in_array( $candidate, $names, true ) ) {
						$label_col = (int) array_search( $candidate, $names, true );
						break;
					}
				}
				continue;
			}
		}
		$code = chargenet_code_normalise( (string) ( $row[ $code_col ] ?? '' ) );
		if ( strlen( $code ) < 3 || strlen( $code ) > 32 ) {
			++$result['invalid'];
			continue;
		}
		if ( isset( $result['codes'][ $code ] ) ) {
			++$result['duplicates'];
			continue;
		}
		$result['codes'][ $code ] = $label_col >= 0 ? mb_substr( sanitize_text_field( (string) ( $row[ $label_col ] ?? '' ) ), 0, 190 ) : '';
	}
	return $result;
}

/**
 * Import codes. Mode "add" keeps existing codes and their use counts; "replace" also removes codes that are not in
 * the file (the use counts of the codes that stay are kept).
 *
 * @param array<string, string> $codes Code => label.
 * @param string                $mode  add or replace.
 * @return array{added: int, removed: int, updated: int}
 */
function chargenet_codes_import( array $codes, string $mode ): array {
	global $wpdb;
	$table  = chargenet_codes_table();
	$now    = current_time( 'mysql', true );
	$result = array(
		'added'   => 0,
		'removed' => 0,
		'updated' => 0,
	);
	$have   = $wpdb->get_col( "SELECT code FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	foreach ( $codes as $code => $label ) {
		if ( in_array( (string) $code, $have, true ) ) {
			$wpdb->update( $table, array( 'label' => $label ), array( 'code' => (string) $code ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			++$result['updated'];
			continue;
		}
		$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table,
			array(
				'code'        => (string) $code,
				'label'       => $label,
				'imported_at' => $now,
			)
		);
		++$result['added'];
	}
	if ( 'replace' === $mode ) {
		foreach ( array_diff( $have, array_map( 'strval', array_keys( $codes ) ) ) as $gone ) {
			$wpdb->delete( $table, array( 'code' => $gone ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			++$result['removed'];
		}
	}
	return $result;
}

// Admin screen --------------------------------------------------------------------------------------------------.

/**
 * Menu entry under the submissions.
 */
function chargenet_codes_menu(): void {
	add_submenu_page(
		'edit.php?post_type=chargenet_submission',
		__( 'Report codes', 'chargenet' ),
		__( 'Report codes', 'chargenet' ),
		'manage_options',
		'chargenet-codes',
		'chargenet_codes_page'
	);
}
add_action( 'admin_menu', 'chargenet_codes_menu' );

/**
 * Handle the CSV upload.
 */
function chargenet_codes_upload(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'chargenet' ), 403 );
	}
	check_admin_referer( 'chargenet_codes_import' );
	$back = admin_url( 'edit.php?post_type=chargenet_submission&page=chargenet-codes' );
	$file = $_FILES['codes'] ?? null; // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- checked above; the file is read, not stored.
	if ( ! is_array( $file ) || UPLOAD_ERR_OK !== (int) $file['error'] || (int) $file['size'] > 2 * MB_IN_BYTES || ! is_uploaded_file( (string) $file['tmp_name'] ) ) {
		wp_safe_redirect( add_query_arg( 'import', 'error', $back ) );
		exit;
	}
	$parsed = chargenet_codes_parse( (string) file_get_contents( (string) $file['tmp_name'] ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$mode   = isset( $_POST['mode'] ) && 'replace' === $_POST['mode'] ? 'replace' : 'add'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$done   = chargenet_codes_import( $parsed['codes'], $mode );
	wp_safe_redirect(
		add_query_arg(
			array(
				'import'     => 'ok',
				'added'      => $done['added'],
				'updated'    => $done['updated'],
				'removed'    => $done['removed'],
				'invalid'    => $parsed['invalid'],
				'duplicates' => $parsed['duplicates'],
			),
			$back
		)
	);
	exit;
}
add_action( 'admin_post_chargenet_codes_import', 'chargenet_codes_upload' );

/**
 * Download the code list with use counts.
 */
function chargenet_codes_download(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'chargenet' ), 403 );
	}
	check_admin_referer( 'chargenet_codes_download' );
	global $wpdb;
	$table = chargenet_codes_table();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="report-codes-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' );
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
	fputcsv( $out, array( 'code', 'label', 'use_count', 'last_used_at_utc' ) );
	foreach ( (array) $wpdb->get_results( "SELECT code, label, use_count, last_used_at FROM {$table} ORDER BY code", ARRAY_A ) as $row ) { // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		fputcsv( $out, array_map( 'chargenet_csv_cell', array_values( $row ) ) );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_chargenet_codes_download', 'chargenet_codes_download' );

/**
 * Codes screen.
 */
function chargenet_codes_page(): void {
	global $wpdb;
	$table  = chargenet_codes_table();
	$total  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$used   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE use_count > 0" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$counts = array();
	foreach ( array( 'added', 'updated', 'removed', 'invalid', 'duplicates' ) as $key ) {
		$counts[ $key ] = isset( $_GET[ $key ] ) ? absint( wp_unslash( $_GET[ $key ] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Report codes', 'chargenet' ); ?></h1>
		<?php if ( isset( $_GET['import'] ) && 'ok' === $_GET['import'] ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success"><p>
				<?php
				printf(
					/* translators: 1: added, 2: updated, 3: removed, 4: skipped invalid lines, 5: skipped duplicate lines. */
					esc_html__( 'Import done: %1$d added, %2$d updated, %3$d removed. Skipped: %4$d invalid and %5$d duplicate lines.', 'chargenet' ),
					(int) $counts['added'],
					(int) $counts['updated'],
					(int) $counts['removed'],
					(int) $counts['invalid'],
					(int) $counts['duplicates']
				);
				?>
			</p></div>
		<?php elseif ( isset( $_GET['import'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'The file could not be read. Use a CSV file of at most 2 MB.', 'chargenet' ); ?></p></div>
		<?php endif; ?>

		<p>
			<?php
			printf(
				/* translators: 1: number of codes, 2: number of codes used at least once. */
				esc_html__( '%1$d codes on the list, %2$d used at least once. Codes are reusable: each use is recorded, the code stays valid.', 'chargenet' ),
				(int) $total,
				(int) $used
			);
			?>
		</p>
		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="chargenet_codes_import">
			<?php wp_nonce_field( 'chargenet_codes_import' ); ?>
			<p><label><?php esc_html_e( 'CSV file', 'chargenet' ); ?> <input type="file" name="codes" accept=".csv,text/csv,text/plain" required></label></p>
			<p class="description"><?php esc_html_e( 'One code per line, or a header row with a column called code (and optionally label, company or name). Case and spaces are ignored.', 'chargenet' ); ?></p>
			<p>
				<label><input type="radio" name="mode" value="add" checked> <?php esc_html_e( 'Add to the list (keep existing codes)', 'chargenet' ); ?></label><br>
				<label><input type="radio" name="mode" value="replace"> <?php esc_html_e( 'Replace the list (remove codes that are not in the file)', 'chargenet' ); ?></label>
			</p>
			<?php submit_button( __( 'Import', 'chargenet' ), 'primary', 'submit', false ); ?>
		</form>
		<p><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=chargenet_codes_download' ), 'chargenet_codes_download' ) ); ?>"><?php esc_html_e( 'Download the list with use counts', 'chargenet' ); ?></a></p>
	</div>
	<?php
}
