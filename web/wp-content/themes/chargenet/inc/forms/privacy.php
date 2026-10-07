<?php
/**
 * Privacy: the WordPress personal data exporter and eraser for form submissions, and the retention period (older
 * submissions are deleted every day). What is stored is listed in docs/integrations.md.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the exporter.
 *
 * @param array<string, array<string, mixed>> $exporters Exporters.
 * @return array<string, array<string, mixed>>
 */
function chargenet_privacy_register_exporter( array $exporters ): array {
	$exporters['chargenet-forms'] = array(
		'exporter_friendly_name' => __( 'ChargeNet form submissions', 'chargenet' ),
		'callback'               => 'chargenet_privacy_export',
	);
	return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'chargenet_privacy_register_exporter' );

/**
 * Register the eraser.
 *
 * @param array<string, array<string, mixed>> $erasers Erasers.
 * @return array<string, array<string, mixed>>
 */
function chargenet_privacy_register_eraser( array $erasers ): array {
	$erasers['chargenet-forms'] = array(
		'eraser_friendly_name' => __( 'ChargeNet form submissions', 'chargenet' ),
		'callback'             => 'chargenet_privacy_erase',
	);
	return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'chargenet_privacy_register_eraser' );

/**
 * Export the submissions of an email address.
 *
 * @param string $email Address.
 * @return array{data: array<int, array<string, mixed>>, done: bool}
 */
function chargenet_privacy_export( string $email ): array {
	$data = array();
	foreach ( chargenet_submissions_by_email( $email ) as $id ) {
		$fields = array();
		foreach ( array(
			'form'         => __( 'Form', 'chargenet' ),
			'lang'         => __( 'Language', 'chargenet' ),
			'name'         => __( 'Name', 'chargenet' ),
			'email'        => __( 'Email', 'chargenet' ),
			'company'      => __( 'Company', 'chargenet' ),
			'message'      => __( 'Message', 'chargenet' ),
			'code'         => __( 'Code used', 'chargenet' ),
			'consent_text' => __( 'Consent given to', 'chargenet' ),
		) as $key => $label ) {
			$value = (string) chargenet_submission_get( $id, $key );
			if ( '' !== $value ) {
				$fields[] = array(
					'name'  => $label,
					'value' => $value,
				);
			}
		}
		$fields[] = array(
			'name'  => __( 'Date', 'chargenet' ),
			'value' => get_post_time( 'Y-m-d H:i:s', true, $id ) . ' UTC',
		);
		$data[]   = array(
			'group_id'    => 'chargenet-forms',
			'group_label' => __( 'Form submissions', 'chargenet' ),
			'item_id'     => 'submission-' . $id,
			'data'        => $fields,
		);
	}
	return array(
		'data' => $data,
		'done' => true,
	);
}

/**
 * Erase the submissions of an email address. The use count of a report code stays (it holds no personal data).
 *
 * @param string $email Address.
 * @return array{items_removed: bool, items_retained: bool, messages: string[], done: bool}
 */
function chargenet_privacy_erase( string $email ): array {
	$removed = false;
	foreach ( chargenet_submissions_by_email( $email ) as $id ) {
		$removed = (bool) wp_delete_post( $id, true ) || $removed;
	}
	return array(
		'items_removed'  => $removed,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => true,
	);
}

/**
 * Delete submissions older than the retention period (not newsletter subscriptions).
 *
 * @return int Number deleted.
 */
function chargenet_forms_purge(): int {
	$months = max( 1, (int) chargenet_forms_setting( 'retention_months' ) );
	$old    = get_posts(
		array(
			'post_type'      => CHARGENET_SUBMISSION,
			'post_status'    => 'any',
			'posts_per_page' => 500, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page -- a daily job.
			'fields'         => 'ids',
			// Newsletter subscriptions stay until the person unsubscribes (or is erased).
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_cn_form',
					'value'   => 'newsletter',
					'compare' => '!=',
				),
			),
			'date_query'     => array(
				array(
					'before' => gmdate( 'Y-m-d H:i:s', strtotime( "-{$months} months" ) ),
					'column' => 'post_date_gmt',
				),
			),
		)
	);
	foreach ( $old as $id ) {
		wp_delete_post( (int) $id, true );
	}
	return count( $old );
}
add_action( 'chargenet_forms_purge_daily', 'chargenet_forms_purge' );

/**
 * Schedule the daily purge.
 */
function chargenet_forms_schedule(): void {
	if ( ! wp_next_scheduled( 'chargenet_forms_purge_daily' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'chargenet_forms_purge_daily' );
	}
}
add_action( 'init', 'chargenet_forms_schedule' );
