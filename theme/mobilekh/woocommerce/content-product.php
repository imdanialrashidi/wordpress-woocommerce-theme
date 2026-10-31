<?php
/**
 * Loop item — signature product card (related, upsells, shortcodes).
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;
global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
mobilekh_product_card( $product );
