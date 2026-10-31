<?php
/**
 * Phone-based identity (Iran mobile) + digit normalization.
 *
 * Scope: registration with mobile number, login with mobile or email,
 * Latin-digit normalization for phone inputs (checkout, register, login).
 * Password handling stays in WordPress/WooCommerce core; no role or
 * capability is touched here. Placeholder emails are documented below.
 *
 * NOTE: true passwordless/OTP login needs an SMS/OTP plugin. Without one,
 * accounts still have passwords (generated or user-chosen); the phone is
 * the username and login handle. Mechanism: the register form keeps Woo's
 * email field (optional) plus a required phone field; JS fills a placeholder
 * email ({phone}@{shop-host}) when it is empty, the server validates the
 * phone and falls back to the same placeholder. Placeholder addresses cannot
 * receive mail — install an OTP/SMS plugin for production phone-only auth.
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Convert Persian/Arabic-Indic digits to Latin digits.
 *
 * @param string $s Input.
 * @return string
 */
function mobilekh_normalize_digits( $s ) {
	$s = (string) $s;
	$s = str_replace(
		array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ),
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		$s
	);
	$s = str_replace(
		array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ),
		array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
		$s
	);
	return $s;
}

/**
 * Normalize a raw phone value to 09xxxxxxxxx form.
 *
 * @param string $raw Raw input.
 * @return string Normalized digits (may be invalid; check with mobilekh_is_iran_mobile()).
 */
function mobilekh_normalize_phone( $raw ) {
	$p = mobilekh_normalize_digits( $raw );
	$p = preg_replace( '/[\s\-()+]/', '', $p );
	if ( 0 === strpos( $p, '0098' ) ) {
		$p = '0' . substr( $p, 4 );
	} elseif ( 0 === strpos( $p, '+98' ) ) {
		$p = '0' . substr( $p, 3 );
	} elseif ( 0 === strpos( $p, '98' ) && 12 === strlen( $p ) ) {
		$p = '0' . substr( $p, 2 );
	}
	return $p;
}

/**
 * Check Iran mobile format: 09 + 9 digits.
 *
 * @param string $phone Normalized phone.
 * @return bool
 */
function mobilekh_is_iran_mobile( $phone ) {
	return (bool) preg_match( '/^09\d{9}$/', (string) $phone );
}

/**
 * Read and normalize the posted registration/checkout phone field.
 *
 * @return string Normalized phone or empty string.
 */
function mobilekh_posted_phone() {
	if ( empty( $_POST['billing_phone'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing — validated by Woo core nonce.
		return '';
	}
	return mobilekh_normalize_phone( wp_unslash( $_POST['billing_phone'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing — validated by Woo core nonce.
}

/**
 * Find user IDs by stored billing phone.
 *
 * @param string $phone Normalized phone.
 * @return int[]
 */
function mobilekh_user_ids_by_phone( $phone ) {
	if ( ! mobilekh_is_iran_mobile( $phone ) ) {
		return array();
	}
	// smsir stores a 98-prefixed billing_phone variant; both spellings must match.
	$intl = '98' . substr( $phone, 1 );
	$users = get_users(
		array(
			'number'     => 2,
			'fields'     => 'ID',
			'meta_query' => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query — login-time lookup only.
				'relation' => 'OR',
				array(
					'key'     => 'billing_phone',
					'value'   => $phone,
					'compare' => '=',
				),
				array(
					'key'     => 'billing_phone',
					'value'   => $intl,
					'compare' => '=',
				),
				array(
					'key'     => 'smsir_phone',
					'value'   => $phone,
					'compare' => '=',
				),
			),
		)
	);
	return array_map( 'absint', (array) $users );
}

/**
 * Phone field on the WooCommerce registration form.
 */
function mobilekh_register_phone_field() {
	$value = mobilekh_posted_phone();
	?>
	<p class="form-row form-row-wide form-row-phone">
		<label for="reg_billing_phone"><?php esc_html_e( 'شماره موبایل', 'mobilekh' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span></label>
		<input type="tel" class="input-text" name="billing_phone" id="reg_billing_phone" inputmode="numeric" autocomplete="tel" dir="ltr" placeholder="09123456789" value="<?php echo esc_attr( $value ); ?>" />
	</p>
	<?php
}
add_action( 'woocommerce_register_form', 'mobilekh_register_phone_field' );

/**
 * Validate the phone field on registration. Hooked to both the modern and
 * the legacy error filters (exactly-once guard) for version compatibility.
 *
 * @param WP_Error $errors Errors object.
 * @return WP_Error
 */
function mobilekh_validate_register_phone( $errors ) {
	static $done = false;
	if ( $done ) {
		return $errors;
	}
	$done  = true;
	$phone = mobilekh_posted_phone();
	if ( '' === $phone ) {
		$errors->add( 'phone_required', __( 'لطفاً شماره موبایل را وارد کنید.', 'mobilekh' ) );
		return $errors;
	}
	if ( ! mobilekh_is_iran_mobile( $phone ) ) {
		$errors->add( 'phone_invalid', __( 'شماره موبایل معتبر نیست (مثل 09123456789).', 'mobilekh' ) );
		return $errors;
	}
	if ( username_exists( $phone ) || ! empty( mobilekh_user_ids_by_phone( $phone ) ) ) {
		$errors->add( 'phone_exists', __( 'این شماره موبایل قبلاً ثبت شده است؛ وارد شوید.', 'mobilekh' ) );
	}
	return $errors;
}
add_filter( 'woocommerce_process_registration_errors', 'mobilekh_validate_register_phone', 10, 1 );
add_filter( 'woocommerce_registration_errors', 'mobilekh_validate_register_phone', 10, 1 );

/**
 * Use the phone number as the account username when it is free.
 *
 * @param string $username Generated username.
 * @param string $email    Email.
 * @param array  $args     Customer args.
 * @return string
 */
function mobilekh_username_from_phone( $username, $email, $args ) {
	$phone = mobilekh_posted_phone();
	if ( mobilekh_is_iran_mobile( $phone ) && ! username_exists( $phone ) ) {
		return $phone;
	}
	return $username;
}
add_filter( 'woocommerce_new_customer_username', 'mobilekh_username_from_phone', 10, 3 );

/**
 * Fill username + placeholder email when the register form has no email.
 *
 * Placeholder emails ({phone}@{shop-domain}) keep Woo core happy for
 * phone-only registration. Consequence: password recovery and order emails
 * cannot reach these addresses — install an OTP/SMS plugin for production
 * phone-only auth instead of relying on placeholders.
 *
 * @param array $customer_data New user data.
 * @return array
 */
function mobilekh_customer_data_phone_fallback( $customer_data ) {
	$phone = mobilekh_posted_phone();
	if ( ! mobilekh_is_iran_mobile( $phone ) ) {
		return $customer_data;
	}
	if ( empty( $customer_data['user_login'] ) && ! username_exists( $phone ) ) {
		$customer_data['user_login'] = $phone;
	}
	if ( empty( $customer_data['user_email'] ) || ! is_email( $customer_data['user_email'] ) ) {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		if ( ! is_string( $host ) || '' === $host ) {
			$host = 'site.local';
		}
		$customer_data['user_email'] = $phone . '@' . $host;
	}
	return $customer_data;
}
add_filter( 'woocommerce_new_customer_data', 'mobilekh_customer_data_phone_fallback', 10, 1 );

/**
 * Persist the normalized phone on the user for phone login lookup.
 *
 * @param int $customer_id New user ID.
 */
function mobilekh_save_register_phone( $customer_id ) {
	$phone = mobilekh_posted_phone();
	if ( mobilekh_is_iran_mobile( $phone ) ) {
		update_user_meta( $customer_id, 'billing_phone', $phone );
		// Bridge for the smsir OTP flow (its lookup key): prevents duplicate accounts.
		update_user_meta( $customer_id, 'smsir_phone', $phone );
	}
}
add_action( 'woocommerce_created_customer', 'mobilekh_save_register_phone' );

/**
 * Allow login with the mobile number (username or stored billing phone).
 * Password verification stays in core (wp_authenticate_username_password).
 *
 * @param WP_User|WP_Error|null $user     Auth result so far.
 * @param string                $username Username or phone.
 * @param string                $password Password.
 * @return WP_User|WP_Error|null
 */
function mobilekh_authenticate_with_phone( $user, $username, $password ) {
	if ( $user instanceof WP_User ) {
		return $user;
	}
	if ( '' === (string) $username || '' === (string) $password ) {
		return $user;
	}
	if ( is_email( $username ) ) {
		return $user;
	}
	$phone = mobilekh_normalize_phone( $username );
	if ( ! mobilekh_is_iran_mobile( $phone ) ) {
		return $user;
	}
	$login = $phone;
	if ( ! username_exists( $phone ) ) {
		$ids = mobilekh_user_ids_by_phone( $phone );
		if ( empty( $ids ) ) {
			return $user; // Keep core's error (invalid username).
		}
		$found = get_userdata( $ids[0] );
		if ( ! $found ) {
			return $user;
		}
		$login = $found->user_login;
	}
	return wp_authenticate_username_password( null, $login, $password );
}
add_filter( 'authenticate', 'mobilekh_authenticate_with_phone', 25, 3 );

/**
 * Friendlier login label: phone or email.
 *
 * @param string $translated Translated text.
 * @param string $text       Source text.
 * @param string $domain     Text domain.
 * @return string
 */
function mobilekh_login_label_phone( $translated, $text, $domain ) {
	if ( 'woocommerce' === $domain && 'Username or email address' === $text ) {
		return __( 'شماره موبایل', 'mobilekh' );
	}
	return $translated;
}
add_filter( 'gettext', 'mobilekh_login_label_phone', 10, 3 );

/**
 * Normalize checkout phone digits server-side (gateways reject Persian digits).
 *
 * @param array $data Posted checkout data.
 * @return array
 */
function mobilekh_normalize_checkout_phone( $data ) {
	if ( isset( $data['billing_phone'] ) && is_string( $data['billing_phone'] ) ) {
		$data['billing_phone'] = mobilekh_normalize_phone( $data['billing_phone'] );
	}
	return $data;
}
add_filter( 'woocommerce_checkout_posted_data', 'mobilekh_normalize_checkout_phone' );

/**
 * Server-side safety net: pre-fill an empty register email with the
 * phone placeholder BEFORE Woo reads it (wp_loaded runs first).
 * The email input itself is hidden by CSS; JS fills it too.
 */
function mobilekh_prefill_register_email() {
	if ( ! isset( $_POST['register'], $_POST['email'] ) ) {
		return;
	}
	if ( '' !== trim( (string) wp_unslash( $_POST['email'] ) ) ) {
		return;
	}
	$phone = mobilekh_posted_phone();
	if ( ! mobilekh_is_iran_mobile( $phone ) ) {
		return;
	}
	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	if ( '' === $host ) {
		return;
	}
	$_POST['email'] = $phone . '@' . $host; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — derived, sanitized below by Woo core.
}
add_action( 'wp_loaded', 'mobilekh_prefill_register_email', 5 );

/**
 * Feed smsir what it demands: its checkout validator only accepts
 * 98-prefixed numbers (/^98[0-9]{10}$/) and reads raw $_POST.
 * Rewrite 09… → 98… in $_POST before anyone reads it; Woo itself keeps
 * the 09… spelling via mobilekh_normalize_checkout_phone.
 */
function mobilekh_prefill_smsir_phone() {
	if ( ! isset( $_POST['billing_phone'] ) || ! is_string( $_POST['billing_phone'] ) ) {
		return;
	}
	$phone = mobilekh_normalize_phone( wp_unslash( $_POST['billing_phone'] ) );
	if ( mobilekh_is_iran_mobile( $phone ) ) {
		$_POST['billing_phone'] = '98' . substr( $phone, 1 ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized — normalized digits only.
	}
}
add_action( 'wp_loaded', 'mobilekh_prefill_smsir_phone', 5 );

/**
 * Is the smsir phone-OTP login flow enabled and configured?
 * (smsir settings → login: active + OTP + pattern template.)
 *
 * @return bool
 */
function mobilekh_smsir_otp_active() {
	$s     = get_option( 'smsir_settings', array() );
	$login = isset( $s['settings']['login'] ) && is_array( $s['settings']['login'] ) ? $s['settings']['login'] : array();
	return ! empty( $login['is_active'] ) && ! empty( $login['enable_otp'] ) && '' !== trim( (string) ( isset( $login['template_id'] ) ? $login['template_id'] : '' ) );
}

/**
 * Is the current page the account/login page? Uses the live WooCommerce
 * setting instead of hardcoded page IDs.
 *
 * @return bool
 */
function mobilekh_is_auth_page() {
	$myaccount = absint( get_option( 'woocommerce_myaccount_page_id', 0 ) );
	if ( $myaccount > 0 && is_page( $myaccount ) ) {
		return true;
	}
	// Legacy hardcoded IDs (backward compatibility with existing content).
	if ( is_page( 9797 ) || is_page( 11 ) ) {
		return true;
	}
	return false;
}

/**
 * Account URL with home fallback (never empty).
 *
 * @return string
 */
function mobilekh_account_url() {
	$myaccount = absint( get_option( 'woocommerce_myaccount_page_id', 0 ) );
	$url       = $myaccount > 0 ? get_permalink( $myaccount ) : false;
	if ( ! $url ) {
		$url = home_url( '/' );
	}
	return $url;
}
