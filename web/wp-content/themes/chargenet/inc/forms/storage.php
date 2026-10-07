<?php
/**
 * Storage of form submissions: a private custom post type (no public URL, not in the REST API, only administrators),
 * with an admin list (filters, search, CSV export, delete, resend). A post type was chosen over a custom table
 * because the admin list, bulk delete and the privacy tools come with it; the volume is small. The report codes,
 * which need lookups and a unique key, live in their own table (codes.php).
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_SUBMISSION = 'chargenet_submission';

/**
 * Register the post type. Every capability maps to manage_options, so only administrators can see or change it.
 */
function chargenet_register_submission_type(): void {
	register_post_type(
		CHARGENET_SUBMISSION,
		array(
			'labels'              => array(
				'name'          => __( 'Form submissions', 'chargenet' ),
				'singular_name' => __( 'Form submission', 'chargenet' ),
				'menu_name'     => __( 'Form submissions', 'chargenet' ),
				'search_items'  => __( 'Search submissions', 'chargenet' ),
				'not_found'     => __( 'No submissions yet.', 'chargenet' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 80,
			'supports'            => array(),
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'map_meta_cap'        => false,
			'capabilities'        => array(
				'edit_post'              => 'manage_options',
				'read_post'              => 'manage_options',
				'delete_post'            => 'manage_options',
				'edit_posts'             => 'manage_options',
				'edit_others_posts'      => 'manage_options',
				'delete_posts'           => 'manage_options',
				'delete_others_posts'    => 'manage_options',
				'publish_posts'          => 'manage_options',
				'read_private_posts'     => 'manage_options',
				'delete_private_posts'   => 'manage_options',
				'delete_published_posts' => 'manage_options',
				'edit_private_posts'     => 'manage_options',
				'edit_published_posts'   => 'manage_options',
				'create_posts'           => 'do_not_allow', // Submissions only come from the forms.
			),
		)
	);
}
add_action( 'init', 'chargenet_register_submission_type' );

/**
 * Store a submission.
 *
 * @param array<string, mixed> $data form, lang, name, email, company, message, code, consent_text, utm (array).
 * @return int Post id, 0 on failure.
 */
function chargenet_submission_create( array $data ): int {
	$label = '' !== (string) ( $data['name'] ?? '' ) ? (string) $data['name'] : (string) $data['email'];
	$id    = wp_insert_post(
		array(
			'post_type'   => CHARGENET_SUBMISSION,
			'post_status' => 'publish',
			'post_title'  => wp_strip_all_tags( $label ),
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return 0;
	}
	$meta = array(
		'form'           => (string) $data['form'],
		'lang'           => (string) $data['lang'],
		'name'           => (string) ( $data['name'] ?? '' ),
		'email'          => (string) $data['email'],
		'company'        => (string) ( $data['company'] ?? '' ),
		'message'        => (string) ( $data['message'] ?? '' ),
		'code'           => (string) ( $data['code'] ?? '' ),
		'consent'        => 1,
		'consent_text'   => (string) ( $data['consent_text'] ?? '' ),
		'consent_at'     => time(),
		'utm'            => (array) ( $data['utm'] ?? array() ),
		'email_status'   => 'pending',
		'email_attempts' => 0,
	);
	foreach ( $meta as $key => $value ) {
		update_post_meta( $id, '_cn_' . $key, $value );
	}
	return (int) $id;
}

/**
 * One meta value of a submission.
 *
 * @param int    $id  Submission id.
 * @param string $key Key without prefix.
 * @return mixed
 */
function chargenet_submission_get( int $id, string $key ) {
	return get_post_meta( $id, '_cn_' . $key, true );
}

/**
 * Submissions of an email address (for the privacy tools).
 *
 * @param string $email Address.
 * @param string $form  Only this form (default: all).
 * @return int[]
 */
function chargenet_submissions_by_email( string $email, string $form = '' ): array {
	$meta = array(
		array(
			'key'   => '_cn_email',
			'value' => $email,
		),
	);
	if ( '' !== $form ) {
		$meta[] = array(
			'key'   => '_cn_form',
			'value' => $form,
		);
	}
	return array_map(
		'intval',
		get_posts(
			array(
				'post_type'      => CHARGENET_SUBMISSION,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			)
		)
	);
}

// Admin list ---------------------------------------------------------------------------------------------------.

/**
 * List columns.
 *
 * @param array<string, string> $columns Columns.
 * @return array<string, string>
 */
function chargenet_submission_columns( array $columns ): array {
	return array(
		'cb'     => $columns['cb'],
		'title'  => __( 'Name', 'chargenet' ),
		'email'  => __( 'Email', 'chargenet' ),
		'form'   => __( 'Form', 'chargenet' ),
		'lang'   => __( 'Language', 'chargenet' ),
		'code'   => __( 'Code', 'chargenet' ),
		'status' => __( 'Email', 'chargenet' ),
		'date'   => __( 'Date', 'chargenet' ),
	);
}
add_filter( 'manage_' . CHARGENET_SUBMISSION . '_posts_columns', 'chargenet_submission_columns' );

/**
 * Human label of a form id.
 *
 * @param string $form Form id.
 */
function chargenet_form_label( string $form ): string {
	$labels = array(
		'contact'      => __( 'Contact', 'chargenet' ),
		'newsletter'   => __( 'Newsletter', 'chargenet' ),
		'trend_report' => __( 'Trend report', 'chargenet' ),
	);
	return $labels[ $form ] ?? $form;
}

/**
 * Column contents.
 *
 * @param string $column Column.
 * @param int    $id     Post id.
 */
function chargenet_submission_column( string $column, int $id ): void {
	switch ( $column ) {
		case 'email':
			echo esc_html( (string) chargenet_submission_get( $id, 'email' ) );
			break;
		case 'form':
			echo esc_html( chargenet_form_label( (string) chargenet_submission_get( $id, 'form' ) ) );
			break;
		case 'lang':
			echo esc_html( strtoupper( (string) chargenet_submission_get( $id, 'lang' ) ) );
			break;
		case 'code':
			$code = (string) chargenet_submission_get( $id, 'code' );
			echo '' !== $code ? esc_html( $code ) : '&mdash;';
			break;
		case 'status':
			$status = (string) chargenet_submission_get( $id, 'email_status' );
			echo esc_html( chargenet_email_status_label( $status ) );
			break;
	}
}
add_action( 'manage_' . CHARGENET_SUBMISSION . '_posts_custom_column', 'chargenet_submission_column', 10, 2 );

/**
 * Label of an email status.
 *
 * @param string $status pending, sent, retry or failed.
 */
function chargenet_email_status_label( string $status ): string {
	$labels = array(
		'pending' => __( 'Pending', 'chargenet' ),
		'sent'    => __( 'Sent', 'chargenet' ),
		'retry'   => __( 'Failed, will retry', 'chargenet' ),
		'failed'  => __( 'Failed', 'chargenet' ),
	);
	return $labels[ $status ] ?? $status;
}

/**
 * Filter dropdowns and the export link above the list.
 *
 * @param string $post_type Post type.
 */
function chargenet_submission_filters( string $post_type ): void {
	if ( CHARGENET_SUBMISSION !== $post_type ) {
		return;
	}
	$form   = isset( $_GET['cn_form'] ) ? sanitize_key( wp_unslash( $_GET['cn_form'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$status = isset( $_GET['cn_status'] ) ? sanitize_key( wp_unslash( $_GET['cn_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	echo '<select name="cn_form"><option value="">' . esc_html__( 'All forms', 'chargenet' ) . '</option>';
	foreach ( array( 'contact', 'trend_report', 'newsletter' ) as $value ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $value ), selected( $form, $value, false ), esc_html( chargenet_form_label( $value ) ) );
	}
	echo '</select><select name="cn_status"><option value="">' . esc_html__( 'All email statuses', 'chargenet' ) . '</option>';
	foreach ( array( 'pending', 'sent', 'retry', 'failed' ) as $value ) {
		printf( '<option value="%s"%s>%s</option>', esc_attr( $value ), selected( $status, $value, false ), esc_html( chargenet_email_status_label( $value ) ) );
	}
	echo '</select>';
}
add_action( 'restrict_manage_posts', 'chargenet_submission_filters' );

/**
 * Apply the filters and the search to the admin query. The search looks at the stored fields.
 *
 * @param WP_Query $query Query.
 */
function chargenet_submission_query( WP_Query $query ): void {
	if ( ! is_admin() || ! $query->is_main_query() || CHARGENET_SUBMISSION !== $query->get( 'post_type' ) ) {
		return;
	}
	$meta = chargenet_submission_meta_query( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- filter values are sanitised inside.
	if ( $meta ) {
		$query->set( 'meta_query', $meta );
	}
	$search = (string) $query->get( 's' );
	if ( '' !== $search ) {
		$query->set( 's', '' );
	}
}
add_action( 'pre_get_posts', 'chargenet_submission_query' );

/**
 * Meta query for the filters and the search (shared by the list and the export).
 *
 * @param array<string, mixed> $request Request values (cn_form, cn_status, s).
 * @return array<int|string, mixed>
 */
function chargenet_submission_meta_query( array $request ): array {
	$meta = array();
	$form = isset( $request['cn_form'] ) ? sanitize_key( wp_unslash( (string) $request['cn_form'] ) ) : '';
	if ( '' !== $form ) {
		$meta[] = array(
			'key'   => '_cn_form',
			'value' => $form,
		);
	}
	$status = isset( $request['cn_status'] ) ? sanitize_key( wp_unslash( (string) $request['cn_status'] ) ) : '';
	if ( '' !== $status ) {
		$meta[] = array(
			'key'   => '_cn_email_status',
			'value' => $status,
		);
	}
	$search = isset( $request['s'] ) ? sanitize_text_field( wp_unslash( (string) $request['s'] ) ) : '';
	if ( '' !== $search ) {
		$or = array( 'relation' => 'OR' );
		foreach ( array( 'name', 'email', 'company', 'code', 'message' ) as $key ) {
			$or[] = array(
				'key'     => '_cn_' . $key,
				'value'   => $search,
				'compare' => 'LIKE',
			);
		}
		$meta[] = $or;
	}
	return $meta;
}

/**
 * Export link next to the list actions.
 *
 * @param string $which top or bottom.
 */
function chargenet_submission_export_link( string $which ): void {
	if ( 'top' !== $which || ! isset( $GLOBALS['typenow'] ) || CHARGENET_SUBMISSION !== $GLOBALS['typenow'] ) {
		return;
	}
	$args = array(
		'action'   => 'chargenet_export_submissions',
		'_wpnonce' => wp_create_nonce( 'chargenet_export' ),
	);
	foreach ( array( 'cn_form', 'cn_status', 's' ) as $key ) {
		if ( ! empty( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$args[ $key ] = sanitize_text_field( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}
	printf( '<a class="button" href="%s">%s</a>', esc_url( add_query_arg( $args, admin_url( 'admin-post.php' ) ) ), esc_html__( 'Export CSV', 'chargenet' ) );
}
add_action( 'manage_posts_extra_tablenav', 'chargenet_submission_export_link' );

/**
 * A cell of a CSV file, safe against spreadsheet formulas (a leading = + - @ would be run by Excel).
 *
 * @param mixed $value Value.
 */
function chargenet_csv_cell( $value ): string {
	$text = (string) $value;
	return '' !== $text && false !== strpos( "=+-@\t\r", $text[0] ) ? "'" . $text : $text;
}

/**
 * Stream the (filtered) submissions as CSV.
 */
function chargenet_export_submissions(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'chargenet' ), 403 );
	}
	check_admin_referer( 'chargenet_export' );
	$args = array(
		'post_type'      => CHARGENET_SUBMISSION,
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => chargenet_submission_meta_query( $_GET ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- checked above; a small table of posts.
	);
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename="submissions-' . gmdate( 'Y-m-d' ) . '.csv"' );
	$out = fopen( 'php://output', 'w' );
	// Byte order mark: Excel opens the file as UTF-8.
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
	fputcsv( $out, array( 'date', 'form', 'language', 'name', 'email', 'company', 'message', 'code', 'consent', 'consent_at', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'email_status', 'email_attempts', 'email_error' ) );
	foreach ( get_posts( $args ) as $id ) {
		$utm = (array) chargenet_submission_get( $id, 'utm' );
		fputcsv(
			$out,
			array_map(
				'chargenet_csv_cell',
				array(
					get_post_time( 'Y-m-d H:i:s', true, $id ),
					chargenet_submission_get( $id, 'form' ),
					chargenet_submission_get( $id, 'lang' ),
					chargenet_submission_get( $id, 'name' ),
					chargenet_submission_get( $id, 'email' ),
					chargenet_submission_get( $id, 'company' ),
					chargenet_submission_get( $id, 'message' ),
					chargenet_submission_get( $id, 'code' ),
					chargenet_submission_get( $id, 'consent' ) ? 'yes' : 'no',
					gmdate( 'Y-m-d H:i:s', (int) chargenet_submission_get( $id, 'consent_at' ) ),
					$utm['utm_source'] ?? '',
					$utm['utm_medium'] ?? '',
					$utm['utm_campaign'] ?? '',
					$utm['utm_term'] ?? '',
					chargenet_submission_get( $id, 'email_status' ),
					chargenet_submission_get( $id, 'email_attempts' ),
					chargenet_submission_get( $id, 'email_error' ),
				)
			)
		);
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_chargenet_export_submissions', 'chargenet_export_submissions' );

// Detail screen: read-only box with the stored data and a resend button. -----------------------------------------.

/**
 * Add the details box.
 */
function chargenet_submission_metabox(): void {
	add_meta_box( 'chargenet-submission', __( 'Submission', 'chargenet' ), 'chargenet_submission_metabox_render', CHARGENET_SUBMISSION, 'normal', 'high' );
}
add_action( 'add_meta_boxes_' . CHARGENET_SUBMISSION, 'chargenet_submission_metabox' );

/**
 * Details box.
 *
 * @param WP_Post $post Submission.
 */
function chargenet_submission_metabox_render( WP_Post $post ): void {
	$id   = $post->ID;
	$utm  = (array) chargenet_submission_get( $id, 'utm' );
	$code = (string) chargenet_submission_get( $id, 'code' );
	$rows = array(
		__( 'Form', 'chargenet' )         => chargenet_form_label( (string) chargenet_submission_get( $id, 'form' ) ),
		__( 'Language', 'chargenet' )     => strtoupper( (string) chargenet_submission_get( $id, 'lang' ) ),
		__( 'Name', 'chargenet' )         => (string) chargenet_submission_get( $id, 'name' ),
		__( 'Email', 'chargenet' )        => (string) chargenet_submission_get( $id, 'email' ),
		__( 'Company', 'chargenet' )      => (string) chargenet_submission_get( $id, 'company' ),
		__( 'Message', 'chargenet' )      => (string) chargenet_submission_get( $id, 'message' ),
		__( 'Code used', 'chargenet' )    => '' !== $code ? $code : ( 'trend_report' === chargenet_submission_get( $id, 'form' ) ? __( 'No code (name and company given)', 'chargenet' ) : '' ),
		__( 'Consent', 'chargenet' )      => gmdate( 'Y-m-d H:i', (int) chargenet_submission_get( $id, 'consent_at' ) ) . ' UTC: ' . (string) chargenet_submission_get( $id, 'consent_text' ),
		__( 'Campaign', 'chargenet' )     => implode( ', ', array_map( static fn( $k, $v ) => "{$k}={$v}", array_keys( $utm ), $utm ) ),
		__( 'Email status', 'chargenet' ) => chargenet_email_status_label( (string) chargenet_submission_get( $id, 'email_status' ) ) . ' (' . (int) chargenet_submission_get( $id, 'email_attempts' ) . ' ' . __( 'attempts', 'chargenet' ) . ')',
		__( 'Last error', 'chargenet' )   => (string) chargenet_submission_get( $id, 'email_error' ),
	);
	echo '<table class="widefat striped"><tbody>';
	foreach ( $rows as $label => $value ) {
		if ( '' !== $value ) {
			printf( '<tr><th scope="row" style="width:12rem">%s</th><td>%s</td></tr>', esc_html( $label ), nl2br( esc_html( $value ) ) );
		}
	}
	echo '</tbody></table>';
	printf(
		'<p><a class="button" href="%s">%s</a></p>',
		esc_url(
			wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'chargenet_resend_submission',
						'id'     => $id,
					),
					admin_url( 'admin-post.php' )
				),
				'chargenet_resend_' . $id
			)
		),
		esc_html__( 'Send the email again', 'chargenet' )
	);
}

/**
 * Send the email of a submission again.
 */
function chargenet_resend_submission(): void {
	$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;
	if ( ! current_user_can( 'manage_options' ) || CHARGENET_SUBMISSION !== get_post_type( $id ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'chargenet' ), 403 );
	}
	check_admin_referer( 'chargenet_resend_' . $id );
	chargenet_mail_attempt( $id );
	wp_safe_redirect( (string) get_edit_post_link( $id, 'raw' ) );
	exit;
}
add_action( 'admin_post_chargenet_resend_submission', 'chargenet_resend_submission' );
