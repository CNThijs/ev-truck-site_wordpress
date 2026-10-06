<?php
/**
 * Settings of the forms: who receives and sends the emails, the retention period, spam protection and the report
 * link. Secrets (Turnstile secret, Microsoft 365 credentials) are never in the repository: define them as constants
 * in wp-config.php (see docs/integrations.md). Email texts are edited on the same screen.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CHARGENET_FORMS_OPTION     = 'chargenet_forms';
const CHARGENET_TEMPLATES_OPTION = 'chargenet_forms_templates';

/**
 * Default settings.
 *
 * @return array<string, mixed>
 */
function chargenet_forms_defaults(): array {
	return array(
		'retention_months' => 12,
		'contact_to'       => 'info@chargenet.energy',
		'contact_from'     => 'info@chargenet.energy',
		'trend_from'       => 'noreply@chargenet.energy',
		'trend_bcc'        => 'info@chargenet.energy',
		'report_url'       => '',
		'turnstile_site'   => '',
	);
}

/**
 * One setting. The Turnstile secret only comes from the constant CHARGENET_TURNSTILE_SECRET.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function chargenet_forms_setting( string $key ) {
	$settings = array_merge( chargenet_forms_defaults(), (array) get_option( CHARGENET_FORMS_OPTION, array() ) );
	if ( 'report_url' === $key && '' === (string) $settings['report_url'] ) {
		return home_url( '/downloads/ChargeNet-TR2027.pdf' );
	}
	if ( 'turnstile_secret' === $key ) {
		return defined( 'CHARGENET_TURNSTILE_SECRET' ) ? (string) CHARGENET_TURNSTILE_SECRET : '';
	}
	return $settings[ $key ] ?? '';
}

/**
 * Turnstile is on when both keys exist.
 */
function chargenet_turnstile_enabled(): bool {
	return '' !== (string) chargenet_forms_setting( 'turnstile_site' ) && '' !== (string) chargenet_forms_setting( 'turnstile_secret' );
}

/**
 * Admin menu: the submissions list is the parent, settings and codes sit under it.
 */
function chargenet_forms_settings_menu(): void {
	add_submenu_page(
		'edit.php?post_type=chargenet_submission',
		__( 'Form settings and emails', 'chargenet' ),
		__( 'Settings and emails', 'chargenet' ),
		'manage_options',
		'chargenet-forms-settings',
		'chargenet_forms_settings_page'
	);
}
add_action( 'admin_menu', 'chargenet_forms_settings_menu' );

/**
 * Save the settings and the email templates.
 */
function chargenet_forms_settings_save(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'chargenet' ), 403 );
	}
	check_admin_referer( 'chargenet_forms_settings' );
	$post = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- checked above.

	$settings = array(
		'retention_months' => max( 1, min( 120, (int) ( $post['retention_months'] ?? 12 ) ) ),
		'turnstile_site'   => sanitize_text_field( (string) ( $post['turnstile_site'] ?? '' ) ),
		'report_url'       => esc_url_raw( (string) ( $post['report_url'] ?? '' ) ),
	);
	foreach ( array( 'contact_to', 'contact_from', 'trend_from', 'trend_bcc' ) as $key ) {
		$mail             = sanitize_email( (string) ( $post[ $key ] ?? '' ) );
		$settings[ $key ] = '' !== $mail ? $mail : chargenet_forms_defaults()[ $key ];
	}
	update_option( CHARGENET_FORMS_OPTION, $settings, false );

	$templates = array();
	foreach ( array( 'contact', 'trend_report' ) as $form ) {
		foreach ( array( 'en', 'nl' ) as $lang ) {
			$subject = sanitize_text_field( (string) ( $post['tpl'][ $form ][ $lang ]['subject'] ?? '' ) );
			$body    = wp_kses_post( (string) ( $post['tpl'][ $form ][ $lang ]['body'] ?? '' ) );
			// An empty field means "use the default text".
			$templates[ $form ][ $lang ] = array(
				'subject' => $subject,
				'body'    => $body,
			);
		}
	}
	update_option( CHARGENET_TEMPLATES_OPTION, $templates, false );

	wp_safe_redirect( add_query_arg( 'saved', '1', admin_url( 'edit.php?post_type=chargenet_submission&page=chargenet-forms-settings' ) ) );
	exit;
}
add_action( 'admin_post_chargenet_forms_settings', 'chargenet_forms_settings_save' );

/**
 * Send a test email with sample data (admin only).
 */
function chargenet_forms_send_test(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do this.', 'chargenet' ), 403 );
	}
	check_admin_referer( 'chargenet_forms_test' );
	$form  = isset( $_POST['form'] ) && 'contact' === $_POST['form'] ? 'contact' : 'trend_report';
	$lang  = isset( $_POST['lang'] ) && 'nl' === $_POST['lang'] ? 'nl' : 'en';
	$email = isset( $_POST['to'] ) ? sanitize_email( wp_unslash( $_POST['to'] ) ) : '';
	$ok    = is_email( $email ) && chargenet_mail_send(
		$form,
		$lang,
		array(
			'name'    => 'Test Visitor',
			'email'   => $email,
			'company' => 'Test Company',
			'message' => "This is a test message.\nSecond line.",
			'code'    => 'TEST1',
		),
		true
	);
	wp_safe_redirect( add_query_arg( 'test', $ok ? 'sent' : 'failed', admin_url( 'edit.php?post_type=chargenet_submission&page=chargenet-forms-settings' ) ) );
	exit;
}
add_action( 'admin_post_chargenet_forms_test', 'chargenet_forms_send_test' );

/**
 * Settings screen.
 */
function chargenet_forms_settings_page(): void {
	$settings  = array_merge( chargenet_forms_defaults(), (array) get_option( CHARGENET_FORMS_OPTION, array() ) );
	$templates = (array) get_option( CHARGENET_TEMPLATES_OPTION, array() );
	$forms     = array(
		'contact'      => __( 'Contact form email (to the team, the visitor in BCC)', 'chargenet' ),
		'trend_report' => __( 'Trend report email (to the visitor, the team in BCC)', 'chargenet' ),
	);
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Form settings and emails', 'chargenet' ); ?></h1>
		<?php if ( isset( $_GET['saved'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Saved.', 'chargenet' ); ?></p></div>
		<?php endif; ?>
		<?php if ( isset( $_GET['test'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
			<div class="notice <?php echo 'sent' === $_GET['test'] ? 'notice-success' : 'notice-error'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>"><p><?php echo 'sent' === $_GET['test'] ? esc_html__( 'Test email sent.', 'chargenet' ) : esc_html__( 'Test email failed. Check the mail settings (docs/integrations.md).', 'chargenet' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?></p></div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="chargenet_forms_settings">
			<?php wp_nonce_field( 'chargenet_forms_settings' ); ?>

			<h2><?php esc_html_e( 'Addresses', 'chargenet' ); ?></h2>
			<table class="form-table" role="presentation">
				<?php
				$fields = array(
					'contact_to'   => __( 'Contact form: team address (receives the message)', 'chargenet' ),
					'contact_from' => __( 'Contact form: sending address (must be an authenticated chargenet.energy mailbox)', 'chargenet' ),
					'trend_from'   => __( 'Trend report email: sending address', 'chargenet' ),
					'trend_bcc'    => __( 'Trend report email: team copy (BCC)', 'chargenet' ),
				);
				foreach ( $fields as $key => $label ) :
					?>
					<tr><th scope="row"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
					<td><input class="regular-text" type="email" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( (string) $settings[ $key ] ); ?>"></td></tr>
				<?php endforeach; ?>
				<tr><th scope="row"><label for="report_url"><?php esc_html_e( 'Report link in the trend report email', 'chargenet' ); ?></label></th>
				<td><input class="regular-text" type="url" id="report_url" name="report_url" value="<?php echo esc_attr( (string) $settings['report_url'] ); ?>" placeholder="<?php echo esc_attr( home_url( '/downloads/ChargeNet-TR2027.pdf' ) ); ?>">
				<p class="description"><?php esc_html_e( 'Empty: the fixed address of the report on this site.', 'chargenet' ); ?></p></td></tr>
			</table>

			<h2><?php esc_html_e( 'Privacy and spam protection', 'chargenet' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="retention_months"><?php esc_html_e( 'Keep submissions (months)', 'chargenet' ); ?></label></th>
				<td><input type="number" min="1" max="120" id="retention_months" name="retention_months" value="<?php echo esc_attr( (string) $settings['retention_months'] ); ?>">
				<p class="description"><?php esc_html_e( 'Older submissions are deleted every day.', 'chargenet' ); ?></p></td></tr>
				<tr><th scope="row"><label for="turnstile_site"><?php esc_html_e( 'Cloudflare Turnstile site key', 'chargenet' ); ?></label></th>
				<td><input class="regular-text" type="text" id="turnstile_site" name="turnstile_site" value="<?php echo esc_attr( (string) $settings['turnstile_site'] ); ?>">
				<p class="description">
					<?php
					echo esc_html(
						chargenet_turnstile_enabled()
							? __( 'Turnstile is on. The secret key comes from the constant CHARGENET_TURNSTILE_SECRET in wp-config.php.', 'chargenet' )
							: __( 'Turnstile is off. Turn it on by entering the site key here and defining CHARGENET_TURNSTILE_SECRET in wp-config.php.', 'chargenet' )
					);
					?>
				</p></td></tr>
			</table>

			<h2><?php esc_html_e( 'Email texts', 'chargenet' ); ?></h2>
			<p><?php esc_html_e( 'Leave a field empty to use the default text. Placeholders: {name} {email} {company} {message} {code} {report_url} {site_name} {date}. The plain-text version is made from the same text.', 'chargenet' ); ?></p>
			<?php foreach ( $forms as $form => $title ) : ?>
				<h3><?php echo esc_html( $title ); ?></h3>
				<?php
				foreach ( array(
					'en' => 'English',
					'nl' => 'Nederlands',
				) as $lang => $lang_label ) :
					$stored  = $templates[ $form ][ $lang ] ?? array();
					$default = chargenet_mail_default_template( $form, $lang );
					?>
					<h4><?php echo esc_html( $lang_label ); ?></h4>
					<p><label><?php esc_html_e( 'Subject', 'chargenet' ); ?><br>
					<input class="large-text" type="text" name="tpl[<?php echo esc_attr( $form ); ?>][<?php echo esc_attr( $lang ); ?>][subject]" value="<?php echo esc_attr( (string) ( $stored['subject'] ?? '' ) ); ?>" placeholder="<?php echo esc_attr( $default['subject'] ); ?>"></label></p>
					<?php
					wp_editor(
						'' !== trim( (string) ( $stored['body'] ?? '' ) ) ? (string) $stored['body'] : $default['body'],
						'tpl_' . $form . '_' . $lang,
						array(
							'textarea_name' => 'tpl[' . $form . '][' . $lang . '][body]',
							'textarea_rows' => 9,
							'media_buttons' => false,
							'teeny'         => true,
						)
					);
				endforeach;
			endforeach;
			?>
			<?php submit_button( __( 'Save', 'chargenet' ) ); ?>
		</form>

		<h2><?php esc_html_e( 'Send a test email', 'chargenet' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="chargenet_forms_test">
			<?php wp_nonce_field( 'chargenet_forms_test' ); ?>
			<p>
				<select name="form"><option value="trend_report"><?php esc_html_e( 'Trend report email', 'chargenet' ); ?></option><option value="contact"><?php esc_html_e( 'Contact form email', 'chargenet' ); ?></option></select>
				<select name="lang"><option value="en">English</option><option value="nl">Nederlands</option></select>
				<input type="email" name="to" required placeholder="you@example.com">
				<button class="button"><?php esc_html_e( 'Send test', 'chargenet' ); ?></button>
			</p>
			<p class="description"><?php esc_html_e( 'Sends the email with sample data to this address only (no team copy).', 'chargenet' ); ?></p>
		</form>
	</div>
	<?php
}
