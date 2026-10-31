<?php
/**
 * Lightweight wishlist: cookie-based, no account needed.
 * Toggle: ?mkh_wl=<id>&_wpnonce=...  Shortcode: [mobilekh_wishlist]
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

define( 'MOBILEKH_WL_COOKIE', 'mobilekh_wl' );

/**
 * Current wishlist IDs (sanitized).
 *
 * @return int[]
 */
function mobilekh_wl_ids() {
	if ( empty( $_COOKIE[ MOBILEKH_WL_COOKIE ] ) ) {
		return array();
	}
	$raw = explode( ',', wp_unslash( $_COOKIE[ MOBILEKH_WL_COOKIE ] ) );
	$ids = array_filter( array_map( 'absint', $raw ) );
	return array_values( array_unique( array_slice( $ids, 0, 200 ) ) );
}

function mobilekh_wl_has( $pid ) {
	return in_array( absint( $pid ), mobilekh_wl_ids(), true );
}

/**
 * Handle toggle early (before headers sent).
 */
function mobilekh_wl_toggle() {
	if ( empty( $_GET['mkh_wl'] ) ) {
		return;
	}
	$pid = absint( $_GET['mkh_wl'] );
	if ( $pid <= 0 || 'product' !== get_post_type( $pid ) ) {
		return;
	}
	check_admin_referer( 'mkh_wl_' . $pid );
	$ids = mobilekh_wl_ids();
	if ( in_array( $pid, $ids, true ) ) {
		$ids = array_values( array_diff( $ids, array( $pid ) ) );
	} else {
		$ids[] = $pid;
	}
	setcookie( MOBILEKH_WL_COOKIE, implode( ',', $ids ), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	$_COOKIE[ MOBILEKH_WL_COOKIE ] = implode( ',', $ids );
	wp_safe_redirect( remove_query_arg( array( 'mkh_wl', '_wpnonce' ) ) );
	exit;
}
add_action( 'template_redirect', 'mobilekh_wl_toggle', 1 );

/**
 * Heart button.
 *
 * @param int $pid Product ID.
 */
function mobilekh_wl_button( $pid ) {
	$pid = absint( $pid );
	$url = wp_nonce_url( add_query_arg( 'mkh_wl', $pid ), 'mkh_wl_' . $pid );
	$on  = mobilekh_wl_has( $pid );
	echo '<a class="' . ( $on ? 'wl-btn wl-on' : 'wl-btn' ) . '" href="' . esc_url( $url ) . '" aria-label="' . esc_attr__( 'علاقه‌مندی', 'mobilekh' ) . '" aria-pressed="' . ( $on ? 'true' : 'false' ) . '">';
	mobilekh_icon( 'heart' );
	echo '</a>';
}

/**
 * Wishlist page shortcode.
 */
function mobilekh_wl_shortcode() {
	$ids = mobilekh_wl_ids();
	if ( empty( $ids ) ) {
		return '<p class="muted">' . esc_html__( 'لیست علاقه‌مندی شما خالی است.', 'mobilekh' ) . '</p>';
	}
	$products = wc_get_products(
		array(
			'include' => $ids,
			'limit'   => 200,
			'status'  => 'publish',
		)
	);
	ob_start();
	echo '<div class="p-grid">';
	foreach ( $products as $p ) {
		mobilekh_product_card( $p );
	}
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'mobilekh_wishlist', 'mobilekh_wl_shortcode' );
