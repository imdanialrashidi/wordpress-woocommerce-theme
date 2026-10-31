<?php
/**
 * MobileKh theme bootstrap.
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

define( 'MOBILEKH_VERSION', '0.1.0' );
define( 'MOBILEKH_DIR', get_template_directory() );
define( 'MOBILEKH_URI', get_template_directory_uri() );

require MOBILEKH_DIR . '/inc/customizer.php';
require MOBILEKH_DIR . '/inc/icons.php';
require MOBILEKH_DIR . '/inc/woocommerce.php';
require MOBILEKH_DIR . '/inc/auth-phone.php';
require MOBILEKH_DIR . '/inc/wishlist.php';
require MOBILEKH_DIR . '/inc/search-ajax.php';

/**
 * Theme setup: supports, menus, image sizes.
 */
function mobilekh_setup() {
	load_theme_textdomain( 'mobilekh', MOBILEKH_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus(
		array(
			'header'       => __( 'منو هدر', 'mobilekh' ),
			'under-header' => __( 'منو زیر هدر', 'mobilekh' ),
			'product-cats' => __( 'دسته بندی کالاها', 'mobilekh' ),
			'footer'       => __( 'منو فوتر', 'mobilekh' ),
		)
	);

	add_image_size( 'mobilekh-card', 600, 600, true );
}
add_action( 'after_setup_theme', 'mobilekh_setup' );

/**
 * Frontend assets.
 */
function mobilekh_assets() {
	// Filemtime busting: every theme edit yields fresh asset URLs (and fresh optimizer bundles).
	$css_ver = (string) filemtime( MOBILEKH_DIR . '/assets/css/main.css' );
	$js_ver  = (string) filemtime( MOBILEKH_DIR . '/assets/js/app.js' );
	wp_enqueue_style( 'mobilekh-tokens', MOBILEKH_URI . '/assets/css/tokens.css', array(), $css_ver );
	wp_enqueue_style( 'mobilekh-main', MOBILEKH_URI . '/assets/css/main.css', array( 'mobilekh-tokens' ), $css_ver );
	wp_enqueue_script( 'mobilekh-app', MOBILEKH_URI . '/assets/js/app.js', array(), $js_ver, true );
	wp_localize_script(
		'mobilekh-app',
		'mobilekhConfig',
		array(
			'popup' => array(
				'enabled' => (bool) get_theme_mod( 'mobilekh_popup_on', false ),
				'days'    => absint( get_theme_mod( 'mobilekh_popup_days', 7 ) ),
			),
			'ajax'   => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
		)
	);

	$css = get_theme_mod( 'mobilekh_custom_css', '' );
	if ( is_string( $css ) && '' !== trim( $css ) ) {
		wp_add_inline_style( 'mobilekh-main', wp_strip_all_tags( $css ) );
	}
}
add_action( 'wp_enqueue_scripts', 'mobilekh_assets' );

/**
 * Custom JS from Customizer (edit_theme_options capability only).
 */
function mobilekh_custom_js() {
	$js = get_theme_mod( 'mobilekh_custom_js', '' );
	if ( is_string( $js ) && '' !== trim( $js ) ) {
		echo '<script>' . wp_strip_all_tags( $js ) . '</script>' . "\n";
	}
}
add_action( 'wp_footer', 'mobilekh_custom_js', 100 );

/**
 * Google Search Console verification tag from Customizer.
 * Accepts the bare token or a full pasted meta tag (token is extracted).
 * Tokens never contain brackets or whitespace — anything else is dropped.
 */
function mobilekh_gsc_verification_tag() {
	$raw = trim( (string) get_theme_mod( 'mobilekh_gsc_verification', '' ) );
	if ( '' === $raw ) {
		return;
	}
	$code = $raw;
	if ( preg_match( '/content\s*=\s*["\']([^"\']+)/i', $raw, $m ) ) {
		$code = trim( $m[1] );
	}
	if ( '' === $code || preg_match( '/[<>"]|\s/', $code ) ) {
		return;
	}
	printf( '<meta name="google-site-verification" content="%s">' . "\n", esc_attr( $code ) );
}
add_action( 'wp_head', 'mobilekh_gsc_verification_tag', 1 );

/**
 * Shop columns / per-page from Customizer.
 */
function mobilekh_shop_columns() {
	return absint( get_theme_mod( 'mobilekh_shop_cols', 4 ) );
}
add_filter( 'loop_shop_columns', 'mobilekh_shop_columns' );

function mobilekh_shop_per_page( $per_page ) {
	$n = absint( get_theme_mod( 'mobilekh_shop_per_page', 12 ) );
	return $n > 0 ? $n : $per_page;
}
add_filter( 'loop_shop_per_page', 'mobilekh_shop_per_page', 20 );

/**
 * Body classes: theme mode marker.
 */
function mobilekh_body_classes( $classes ) {
	$classes[] = 'mobilekh';
	return $classes;
}
add_filter( 'body_class', 'mobilekh_body_classes' );

/**
 * Escape-safe logo helper (light/dark aware).
 */
function mobilekh_logo( $which = 'light' ) {
	$id = absint( get_theme_mod( $which === 'dark' ? 'mobilekh_logo_dark' : 'mobilekh_logo', 0 ) );
	if ( $id > 0 ) {
		echo wp_get_attachment_image( $id, 'medium', false, array( 'class' => 'site-logo site-logo-' . esc_attr( $which ), 'alt' => esc_attr( get_bloginfo( 'name' ) ), 'fetchpriority' => 'high' ) );
		return;
	}
	// Text fallback keeps header functional without a logo upload.
	echo '<span class="site-title">' . esc_html( get_bloginfo( 'name' ) ) . '</span>';
}
