<?php
/**
 * Customizer: store identity, popup, topbar, homepage sections, shop, custom code.
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

function mobilekh_sanitize_checkbox( $v ) {
	return $v ? true : false;
}

function mobilekh_sanitize_absint( $v ) {
	return absint( $v );
}

function mobilekh_customize_register( $wp_customize ) {

	/* ---------- 1. Store identity ---------- */
	$wp_customize->add_section(
		'mobilekh_identity',
		array(
			'title'    => __( 'هویت فروشگاه', 'mobilekh' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting( 'mobilekh_logo', array( 'default' => 0, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'mobilekh_logo',
			array(
				'label'    => __( 'لوگو (تم روشن)', 'mobilekh' ),
				'section'  => 'mobilekh_identity',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting( 'mobilekh_logo_dark', array( 'default' => 0, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'mobilekh_logo_dark',
			array(
				'label'    => __( 'لوگو (تم تیره)', 'mobilekh' ),
				'section'  => 'mobilekh_identity',
				'mime_type' => 'image',
			)
		)
	);

	foreach ( array(
		'mobilekh_phone'   => __( 'شماره تلفن', 'mobilekh' ),
		'mobilekh_email'   => __( 'ایمیل', 'mobilekh' ),
		'mobilekh_address' => __( 'آدرس', 'mobilekh' ),
		'mobilekh_hours'   => __( 'ساعات کاری', 'mobilekh' ),
	) as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'mobilekh_identity', 'type' => 'text' ) );
	}

	foreach ( array(
		'mobilekh_social_instagram' => __( 'اینستاگرام', 'mobilekh' ),
		'mobilekh_social_telegram'  => __( 'تلگرام', 'mobilekh' ),
		'mobilekh_social_whatsapp'  => __( 'واتساپ', 'mobilekh' ),
	) as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'mobilekh_identity', 'type' => 'url' ) );
	}

	$wp_customize->add_setting( 'mobilekh_enamad_id', array( 'default' => '505880', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_enamad_id', array( 'label' => __( 'شناسه اینماد', 'mobilekh' ), 'section' => 'mobilekh_identity', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_enamad_code', array( 'default' => 'tSrnqxtKD6nS4i9gvC0Oh7Fv6uYkLKWY', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_enamad_code', array( 'label' => __( 'کد اینماد', 'mobilekh' ), 'section' => 'mobilekh_identity', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_enamad_img', array( 'default' => 0, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'mobilekh_enamad_img',
			array(
				'label'       => __( 'عکس دلخواه اینماد', 'mobilekh' ),
				'description' => __( 'اگر بگذاری، به‌جای لوگوی رسمی نمایش داده می‌شود (لینک اعتبارسنجی سر جایش می‌ماند).', 'mobilekh' ),
				'section'     => 'mobilekh_identity',
				'mime_type'   => 'image',
			)
		)
	);
	$wp_customize->add_setting( 'mobilekh_support_float', array( 'default' => true, 'sanitize_callback' => 'mobilekh_sanitize_checkbox' ) );
	$wp_customize->add_control( 'mobilekh_support_float', array( 'label' => __( 'دکمه شناور پشتیبانی', 'mobilekh' ), 'section' => 'mobilekh_identity', 'type' => 'checkbox' ) );

	/* ---------- 2. Topbar notice ---------- */
	$wp_customize->add_section(
		'mobilekh_topbar',
		array(
			'title'    => __( 'نوار اطلاع‌رسانی', 'mobilekh' ),
			'priority' => 31,
		)
	);
	$wp_customize->add_setting( 'mobilekh_topbar_on', array( 'default' => false, 'sanitize_callback' => 'mobilekh_sanitize_checkbox' ) );
	$wp_customize->add_control( 'mobilekh_topbar_on', array( 'label' => __( 'نمایش نوار', 'mobilekh' ), 'section' => 'mobilekh_topbar', 'type' => 'checkbox' ) );
	$wp_customize->add_setting( 'mobilekh_topbar_text', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_topbar_text', array( 'label' => __( 'متن نوار', 'mobilekh' ), 'section' => 'mobilekh_topbar', 'type' => 'text' ) );

	/* ---------- 3. Popup ---------- */
	$wp_customize->add_section(
		'mobilekh_popup',
		array(
			'title'    => __( 'پاپ‌آپ', 'mobilekh' ),
			'priority' => 32,
		)
	);
	$wp_customize->add_setting( 'mobilekh_popup_on', array( 'default' => false, 'sanitize_callback' => 'mobilekh_sanitize_checkbox' ) );
	$wp_customize->add_control( 'mobilekh_popup_on', array( 'label' => __( 'فعال‌سازی پاپ‌آپ', 'mobilekh' ), 'section' => 'mobilekh_popup', 'type' => 'checkbox' ) );
	$wp_customize->add_setting( 'mobilekh_popup_title', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_popup_title', array( 'label' => __( 'عنوان', 'mobilekh' ), 'section' => 'mobilekh_popup', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_popup_text', array( 'default' => '', 'sanitize_callback' => 'wp_kses_post' ) );
	$wp_customize->add_control( 'mobilekh_popup_text', array( 'label' => __( 'متن', 'mobilekh' ), 'section' => 'mobilekh_popup', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'mobilekh_popup_btn_text', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_popup_btn_text', array( 'label' => __( 'متن دکمه', 'mobilekh' ), 'section' => 'mobilekh_popup', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_popup_btn_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'mobilekh_popup_btn_url', array( 'label' => __( 'لینک دکمه', 'mobilekh' ), 'section' => 'mobilekh_popup', 'type' => 'url' ) );
	$wp_customize->add_setting( 'mobilekh_popup_days', array( 'default' => 7, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
	$wp_customize->add_control( 'mobilekh_popup_days', array( 'label' => __( 'نمایش مجدد بعد از (روز)', 'mobilekh' ), 'section' => 'mobilekh_popup', 'type' => 'number', 'input_attrs' => array( 'min' => 0, 'max' => 365 ) ) );

	/* ---------- 4. Homepage sections ---------- */
	$wp_customize->add_section(
		'mobilekh_home',
		array(
			'title'    => __( 'سکشن‌های صفحه اصلی', 'mobilekh' ),
			'priority' => 33,
		)
	);
	$sections = array(
		'mobilekh_sec_hero'     => __( 'هیرو', 'mobilekh' ),
		'mobilekh_sec_cats'     => __( 'دسته‌بندی‌ها', 'mobilekh' ),
		'mobilekh_sec_deals'    => __( 'پیشنهادها', 'mobilekh' ),
		'mobilekh_sec_best'     => __( 'پرفروش‌ها', 'mobilekh' ),
		'mobilekh_sec_benefits' => __( 'مزایا', 'mobilekh' ),
		'mobilekh_sec_blog'     => __( 'بلاگ', 'mobilekh' ),
	);
	foreach ( $sections as $key => $label ) {
		$wp_customize->add_setting( $key, array( 'default' => true, 'sanitize_callback' => 'mobilekh_sanitize_checkbox' ) );
		$wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'mobilekh_home', 'type' => 'checkbox' ) );
	}
	$wp_customize->add_setting( 'mobilekh_hero_title', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_hero_title', array( 'label' => __( 'تیتر هیرو', 'mobilekh' ), 'section' => 'mobilekh_home', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_hero_sub', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_hero_sub', array( 'label' => __( 'زیرتیتر هیرو', 'mobilekh' ), 'section' => 'mobilekh_home', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_hero_cta_text', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_hero_cta_text', array( 'label' => __( 'متن دکمه هیرو', 'mobilekh' ), 'section' => 'mobilekh_home', 'type' => 'text' ) );
	$wp_customize->add_setting( 'mobilekh_hero_cta_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
	$wp_customize->add_control( 'mobilekh_hero_cta_url', array( 'label' => __( 'لینک دکمه هیرو', 'mobilekh' ), 'section' => 'mobilekh_home', 'type' => 'url' ) );

	/* Hero slider: 3 slides. */
	for ( $i = 1; $i <= 3; $i++ ) {
		$wp_customize->add_setting( "mobilekh_slide_{$i}_img", array( 'default' => 0, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				"mobilekh_slide_{$i}_img",
				array(
					'label'     => sprintf( __( 'اسلاید %d: عکس', 'mobilekh' ), $i ),
					'section'   => 'mobilekh_home',
					'mime_type' => 'image',
				)
			)
		);
		$wp_customize->add_setting( "mobilekh_slide_{$i}_title", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "mobilekh_slide_{$i}_title", array( 'label' => sprintf( __( 'اسلاید %d: تیتر', 'mobilekh' ), $i ), 'section' => 'mobilekh_home', 'type' => 'text' ) );
		$wp_customize->add_setting( "mobilekh_slide_{$i}_sub", array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
		$wp_customize->add_control( "mobilekh_slide_{$i}_sub", array( 'label' => sprintf( __( 'اسلاید %d: زیرتیتر', 'mobilekh' ), $i ), 'section' => 'mobilekh_home', 'type' => 'text' ) );
		$wp_customize->add_setting( "mobilekh_slide_{$i}_url", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( "mobilekh_slide_{$i}_url", array( 'label' => sprintf( __( 'اسلاید %d: لینک', 'mobilekh' ), $i ), 'section' => 'mobilekh_home', 'type' => 'url' ) );
	}
	$wp_customize->add_setting( 'mobilekh_deals_until', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'mobilekh_deals_until', array( 'label' => __( 'پایان تخفیف‌ها (میلادی: 2026-12-31 23:59)', 'mobilekh' ), 'section' => 'mobilekh_home', 'type' => 'text' ) );

	/* ---------- 5. Shop ---------- */
	$wp_customize->add_section(
		'mobilekh_shop',
		array(
			'title'    => __( 'فروشگاه', 'mobilekh' ),
			'priority' => 34,
		)
	);
	$wp_customize->add_setting( 'mobilekh_shop_cols', array( 'default' => 4, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
	$wp_customize->add_control(
		'mobilekh_shop_cols',
		array(
			'label'   => __( 'ستون دسکتاپ', 'mobilekh' ),
			'section' => 'mobilekh_shop',
			'type'    => 'select',
			'choices' => array( 3 => '3', 4 => '4', 5 => '5' ),
		)
	);
	$wp_customize->add_setting( 'mobilekh_shop_per_page', array( 'default' => 12, 'sanitize_callback' => 'mobilekh_sanitize_absint' ) );
	$wp_customize->add_control( 'mobilekh_shop_per_page', array( 'label' => __( 'تعداد محصول در صفحه', 'mobilekh' ), 'section' => 'mobilekh_shop', 'type' => 'number', 'input_attrs' => array( 'min' => 4, 'max' => 48 ) ) );

	/* ---------- 6. Custom code ---------- */
	$wp_customize->add_section(
		'mobilekh_code',
		array(
			'title'    => __( 'کد سفارشی', 'mobilekh' ),
			'priority' => 200,
		)
	);
	$wp_customize->add_setting( 'mobilekh_custom_css', array( 'default' => '', 'sanitize_callback' => 'wp_strip_all_tags' ) );
	$wp_customize->add_control( 'mobilekh_custom_css', array( 'label' => __( 'CSS اضافه', 'mobilekh' ), 'section' => 'mobilekh_code', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'mobilekh_custom_js', array( 'default' => '', 'sanitize_callback' => 'wp_strip_all_tags' ) );
	$wp_customize->add_control( 'mobilekh_custom_js', array( 'label' => __( 'JS اضافه (فوتر)', 'mobilekh' ), 'section' => 'mobilekh_code', 'type' => 'textarea' ) );
	$wp_customize->add_setting( 'mobilekh_gsc_verification', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control(
		'mobilekh_gsc_verification',
		array(
			'label'       => __( 'کد تایید Google Search Console', 'mobilekh' ),
			'description' => __( 'فقط کد داخل content را بگذارید (اگر کل متاتگ را بچسبانید، کد به‌صورت خودکار استخراج می‌شود). اگر تایید را در رنک‌مث انجام داده‌اید، اینجا را خالی بگذارید.', 'mobilekh' ),
			'section'     => 'mobilekh_code',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'mobilekh_customize_register' );
