<?php
/**
 * Live AJAX product search (public, read-only).
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Clean price text for live results: decode entities (&ndash;, &amp;, …)
 * left over by wp_strip_all_tags so JS escaping shows real characters.
 *
 * @param WC_Product $p Product.
 * @return string
 */
function mobilekh_search_price_text( $p ) {
	$html = $p->get_price_html();
	// Prefer the current (sale) price when <del>/<ins> are present.
	if ( preg_match( '/<ins[^>]*>(.*?)<\/ins>/us', $html, $m ) && '' !== trim( wp_strip_all_tags( $m[1] ) ) ) {
		$html = $m[1];
	}
	$text = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES, 'UTF-8' );
	$text = str_replace( array( "\xc2\xa0", '&nbsp;' ), ' ', $text );
	$text = preg_replace( '/\s+/u', ' ', $text );
	$text = is_string( $text ) ? trim( $text ) : '';
	if ( '' !== $text ) {
		// Drop screen-reader duplication ("محدوده قیمت…", "قیمت اصلی…").
		$parts = preg_split( '/(محدوده قیمت|قیمت اصلی|قیمت فعلی)/u', $text );
		if ( is_array( $parts ) && '' !== trim( $parts[0] ) ) {
			$text = trim( $parts[0] );
		}
	}
	return $text;
}

/**
 * GET ?action=mobilekh_search&q=... → JSON list.
 */
function mobilekh_ajax_search() {
	$q = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
	$len = function_exists( 'mb_strlen' ) ? mb_strlen( $q ) : strlen( $q );
	if ( $len < 2 || ! function_exists( 'wc_get_products' ) ) {
		wp_send_json( array() );
	}
	$products = wc_get_products(
		array(
			'limit'   => 6,
			'status'  => 'publish',
			's'       => $q,
		)
	);
	$out = array();
	foreach ( $products as $p ) {
		$out[] = array(
			'id'    => $p->get_id(),
			'name'  => $p->get_name(),
			'url'   => get_permalink( $p->get_id() ),
			'price' => mobilekh_search_price_text( $p ),
			'img'   => wp_get_attachment_image_url( $p->get_image_id(), 'thumbnail' ),
		);
	}
	wp_send_json( $out );
}
add_action( 'wp_ajax_mobilekh_search', 'mobilekh_ajax_search' );
add_action( 'wp_ajax_nopriv_mobilekh_search', 'mobilekh_ajax_search' );
