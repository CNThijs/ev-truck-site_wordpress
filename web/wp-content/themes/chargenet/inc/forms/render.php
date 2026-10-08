<?php
/**
 * Markup of the two form sections (blocks contact-form and trend-report-form): plain HTML forms that work without
 * JavaScript (a normal POST and a redirect back) and that view.js enhances (fetch, inline errors, success message,
 * dataLayer events). Errors are linked to their fields (aria-invalid, aria-describedby) and announced in a live region.
 *
 * @package chargenet
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Language of the page being rendered: en or nl.
 */
function chargenet_current_lang(): string {
	$lang = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : substr( get_locale(), 0, 2 );
	return 'nl' === $lang ? 'nl' : 'en';
}

/**
 * Address of the page with a seed key (for example privacy or contact) in the current language, or ''.
 *
 * @param string $key Seed key.
 */
function chargenet_seeded_page_url( string $key ): string {
	$ids = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_chargenet_seed_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $ids ? (string) get_permalink( (int) $ids[0] ) : '';
}

/**
 * Open a form: the form tag and all hidden fields (nonce, signed time, language, return address, campaign
 * parameters, honeypot, Turnstile).
 *
 * @param string               $form  contact or trend_report.
 * @param string               $mode  code or nocode (trend report) or ''.
 * @param array<string, mixed> $state Result of an earlier submission (chargenet_form_state()) for this form, or empty.
 */
function chargenet_form_open( string $form, string $mode, array $state ): void {
	$id     = 'cn-' . $form . ( '' !== $mode ? '-' . $mode : '' );
	$return = home_url( wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ) ); // No query string: the page may come from the cache.
	printf(
		'<form id="%1$s" class="cn-form" method="post" action="%2$s" data-cn-form="%3$s" data-cn-mode="%4$s" data-cn-lang="%5$s" data-cn-network="%6$s" data-cn-token="%7$s" novalidate>',
		esc_attr( $id ),
		esc_url( admin_url( 'admin-post.php', 'relative' ) ), // Relative: always the origin of the page itself.
		esc_attr( $form ),
		esc_attr( $mode ),
		esc_attr( chargenet_current_lang() ),
		esc_attr__( 'The form could not be sent. Check your connection and try again.', 'chargenet' ),
		esc_url( rest_url( 'chargenet/v1/form-token' ) )
	);
	echo '<input type="hidden" name="action" value="chargenet_form">';
	wp_nonce_field( 'chargenet_form', '_wpnonce', false );
	printf( '<input type="hidden" name="cn_form" value="%s">', esc_attr( $form ) );
	printf( '<input type="hidden" name="cn_mode" value="%s">', esc_attr( $mode ) );
	printf( '<input type="hidden" name="cn_lang" value="%s">', esc_attr( chargenet_current_lang() ) );
	printf( '<input type="hidden" name="cn_ts" value="%s">', esc_attr( chargenet_form_token() ) );
	printf( '<input type="hidden" name="cn_return" value="%s">', esc_url( $return ) );
	// Campaign parameters are filled in by JavaScript (URL, else the cn_campaign cookie): the HTML must be the same for
	// every visitor so that the page cache can serve it.
	foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term' ) as $param ) {
		printf( '<input type="hidden" name="cn_%s" value="">', esc_attr( $param ) );
	}
	// Honeypot: invisible to people (and to assistive technology), filled in by bots.
	echo '<div class="cn-form__trap" aria-hidden="true"><label>Website <input type="text" name="website" value="" tabindex="-1" autocomplete="off"></label></div>';
	if ( ! empty( $state['message'] ) && empty( $state['ok'] ) ) {
		printf( '<p class="cn-form__alert" role="alert" data-cn-alert>%s</p>', esc_html( (string) $state['message'] ) );
	} else {
		echo '<p class="cn-form__alert" role="alert" data-cn-alert hidden></p>';
	}
}

/**
 * One field: label, input or textarea, hint and error.
 *
 * @param array<string, mixed> $f    name, label, type, required, autocomplete, hint, rows (textarea), extra (attributes).
 * @param array<string, mixed> $state Earlier result for this form.
 * @param string               $form_id Id of the form (makes the field ids unique).
 */
function chargenet_form_field( array $f, array $state, string $form_id ): void {
	$name  = (string) $f['name'];
	$id    = $form_id . '-' . $name;
	$error = (string) ( $state['errors'][ $name ] ?? '' );
	$value = (string) ( $state['old'][ $name ] ?? '' );
	$desc  = array();
	$attrs = ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"';
	if ( ! empty( $f['required'] ) ) {
		$attrs .= ' required aria-required="true"';
	}
	if ( ! empty( $f['autocomplete'] ) ) {
		$attrs .= ' autocomplete="' . esc_attr( (string) $f['autocomplete'] ) . '"';
	}
	if ( ! empty( $f['hint'] ) ) {
		$desc[] = $id . '-hint';
	}
	$desc[] = $id . '-error';
	$attrs .= ' aria-describedby="' . esc_attr( implode( ' ', $desc ) ) . '"';
	if ( '' !== $error ) {
		$attrs .= ' aria-invalid="true"';
	}
	$attrs .= (string) ( $f['extra'] ?? '' );
	echo '<div class="field cn-field' . ( '' !== $error ? ' has-error' : '' ) . '">';
	printf( '<label class="field__label" for="%s">%s%s</label>', esc_attr( $id ), esc_html( (string) $f['label'] ), ! empty( $f['required'] ) ? ' <span class="cn-field__req" aria-hidden="true">*</span>' : '' );
	if ( ! empty( $f['rows'] ) ) {
		printf( '<textarea class="textarea" rows="%d"%s>%s</textarea>', (int) $f['rows'], $attrs, esc_textarea( $value ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
	} else {
		printf( '<input class="input" type="%s" value="%s"%s>', esc_attr( (string) ( $f['type'] ?? 'text' ) ), esc_attr( $value ), $attrs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped above.
	}
	if ( ! empty( $f['hint'] ) ) {
		printf( '<p class="field__hint" id="%s-hint">%s</p>', esc_attr( $id ), esc_html( (string) $f['hint'] ) );
	}
	printf( '<p class="field__error" id="%s-error"%s>%s</p>', esc_attr( $id ), '' === $error ? ' hidden' : '', esc_html( $error ) );
	echo '</div>';
}

/**
 * The consent checkbox, with the link to the privacy policy.
 *
 * @param array<string, mixed> $state   Earlier result.
 * @param string               $form_id Id of the form.
 * @param string               $form    Form (the newsletter has its own sentence).
 */
function chargenet_form_consent( array $state, string $form_id, string $form = '' ): void {
	$id      = $form_id . '-consent';
	$error   = (string) ( $state['errors']['consent'] ?? '' );
	$privacy = chargenet_seeded_page_url( 'privacy' );
	echo '<div class="field cn-field cn-field--check' . ( '' !== $error ? ' has-error' : '' ) . '"><div class="choice">';
	printf( '<input type="checkbox" id="%1$s" name="consent" value="1" required aria-required="true" aria-describedby="%1$s-error"%2$s>', esc_attr( $id ), '' !== $error ? ' aria-invalid="true"' : '' );
	printf( '<label for="%s">%s', esc_attr( $id ), esc_html( chargenet_form_consent_text( chargenet_current_lang(), $form ) ) );
	if ( '' !== $privacy ) {
		printf( ' <a href="%s">%s</a>', esc_url( $privacy ), esc_html__( 'Read the privacy policy', 'chargenet' ) );
	}
	echo ' <span class="cn-field__req" aria-hidden="true">*</span></label></div>';
	printf( '<p class="field__error" id="%s-error"%s>%s</p>', esc_attr( $id ), '' === $error ? ' hidden' : '', esc_html( $error ) );
	echo '</div>';
}

/**
 * Close a form: Turnstile (when on), the submit button and the live region for the success message.
 *
 * @param string $label   Button label.
 * @param string $variant primary or secondary.
 */
function chargenet_form_close( string $label, string $variant = 'primary' ): void {
	if ( chargenet_turnstile_enabled() ) {
		wp_enqueue_script( 'cf-turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'strategy' => 'defer' ) ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external, versioned by Cloudflare.
		printf( '<div class="cf-turnstile" data-sitekey="%s" data-language="%s"></div>', esc_attr( (string) chargenet_forms_setting( 'turnstile_site' ) ), esc_attr( chargenet_current_lang() ) );
		echo '<noscript><p class="cn-form__alert">' . esc_html__( 'Please turn on JavaScript to send this form.', 'chargenet' ) . '</p></noscript>';
	}
	printf( '<p class="cn-form__actions"><button type="submit" class="btn btn--%s">%s</button></p>', esc_attr( $variant ), esc_html( $label ) );
	echo '<p class="cn-form__note">' . esc_html__( 'Fields marked * are required.', 'chargenet' ) . '</p>';
	echo '</form>';
}

/**
 * Success message (replaces the form).
 *
 * @param string $message Message.
 * @param string $id      Id (focus target).
 */
function chargenet_form_success( string $message, string $id ): void {
	printf( '<div class="cn-form__success" id="%s" role="status" tabindex="-1"><p>%s</p></div>', esc_attr( $id ), esc_html( $message ) );
}

/**
 * Print the contact form.
 */
function chargenet_render_contact_form(): void {
	$state = chargenet_form_state();
	$state = is_array( $state ) && 'contact' === ( $state['form'] ?? '' ) ? $state : array();
	$id    = 'cn-contact';
	if ( ! empty( $state['ok'] ) ) {
		chargenet_form_success( (string) $state['message'], $id );
		return;
	}
	chargenet_form_open( 'contact', '', $state );
	chargenet_form_field(
		array(
			'name'         => 'name',
			'label'        => __( 'Name', 'chargenet' ),
			'required'     => true,
			'autocomplete' => 'name',
		),
		$state,
		$id
	);
	chargenet_form_field(
		array(
			'name'         => 'email',
			'label'        => __( 'Email address', 'chargenet' ),
			'type'         => 'email',
			'required'     => true,
			'autocomplete' => 'email',
		),
		$state,
		$id
	);
	chargenet_form_field(
		array(
			'name'     => 'message',
			'label'    => __( 'Message', 'chargenet' ),
			'required' => true,
			'rows'     => 6,
			'hint'     => __( 'At least 10 characters.', 'chargenet' ),
		),
		$state,
		$id
	);
	chargenet_form_consent( $state, $id );
	chargenet_form_close( __( 'Send message', 'chargenet' ) );
}

/**
 * Print the trend report form: code and email (primary), and below it, in a disclosure that works without
 * JavaScript, a second form for people without a code (name, company, email).
 */
function chargenet_render_trend_report_form(): void {
	$state = chargenet_form_state();
	$state = is_array( $state ) && 'trend_report' === ( $state['form'] ?? '' ) ? $state : array();
	if ( ! empty( $state['ok'] ) ) {
		chargenet_form_success( (string) $state['message'], 'cn-trend_report-' . ( $state['mode'] ?? 'code' ) );
		return;
	}
	$code_state   = 'code' === ( $state['mode'] ?? 'code' ) ? $state : array();
	$nocode_state = 'nocode' === ( $state['mode'] ?? '' ) ? $state : array();

	$id = 'cn-trend_report-code';
	chargenet_form_open( 'trend_report', 'code', $code_state );
	chargenet_form_field(
		array(
			'name'     => 'code',
			'label'    => __( 'Your unique code', 'chargenet' ),
			'required' => true,
			'hint'     => __( 'You find the code on the card you received from us.', 'chargenet' ),
			'extra'    => ' autocapitalize="characters" autocorrect="off" spellcheck="false" autocomplete="off" maxlength="40"',
		),
		$code_state,
		$id
	);
	chargenet_form_field(
		array(
			'name'         => 'email',
			'label'        => __( 'Business email address', 'chargenet' ),
			'type'         => 'email',
			'required'     => true,
			'autocomplete' => 'email',
		),
		$code_state,
		$id
	);
	chargenet_form_consent( $code_state, $id );
	chargenet_form_close( __( 'Send me the report', 'chargenet' ) );

	$id = 'cn-trend_report-nocode';
	echo '<details class="cn-nocode"' . ( $nocode_state ? ' open' : '' ) . '>';
	echo '<summary class="btn btn--secondary">' . esc_html__( 'I do not have a code', 'chargenet' ) . '</summary>';
	chargenet_form_open( 'trend_report', 'nocode', $nocode_state );
	chargenet_form_field(
		array(
			'name'         => 'name',
			'label'        => __( 'Name', 'chargenet' ),
			'required'     => true,
			'autocomplete' => 'name',
		),
		$nocode_state,
		$id
	);
	chargenet_form_field(
		array(
			'name'         => 'company',
			'label'        => __( 'Company name', 'chargenet' ),
			'required'     => true,
			'autocomplete' => 'organization',
		),
		$nocode_state,
		$id
	);
	chargenet_form_field(
		array(
			'name'         => 'email',
			'label'        => __( 'Business email address', 'chargenet' ),
			'type'         => 'email',
			'required'     => true,
			'autocomplete' => 'email',
		),
		$nocode_state,
		$id
	);
	chargenet_form_consent( $nocode_state, $id );
	chargenet_form_close( __( 'Send me the report', 'chargenet' ), 'secondary' );
	echo '</details>';
}

/**
 * Print the newsletter sign-up: email and consent. Subscriptions are stored in WordPress (Form submissions) until a
 * newsletter platform is connected.
 */
function chargenet_render_newsletter_form(): void {
	$state = chargenet_form_state();
	$state = is_array( $state ) && 'newsletter' === ( $state['form'] ?? '' ) ? $state : array();
	$id    = 'cn-newsletter';
	if ( ! empty( $state['ok'] ) ) {
		chargenet_form_success( (string) $state['message'], $id );
		return;
	}
	chargenet_form_open( 'newsletter', '', $state );
	chargenet_form_field(
		array(
			'name'         => 'email',
			'label'        => __( 'Email address', 'chargenet' ),
			'type'         => 'email',
			'required'     => true,
			'autocomplete' => 'email',
		),
		$state,
		$id
	);
	chargenet_form_consent( $state, $id, 'newsletter' );
	chargenet_form_close( __( 'Subscribe', 'chargenet' ) );
}
