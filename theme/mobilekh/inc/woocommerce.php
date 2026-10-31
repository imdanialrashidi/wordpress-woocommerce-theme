<?php
/**
 * WooCommerce integration helpers.
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pillar categories for the shop mega menu (قطعات موبایل / لوازم جانبی).
 *
 * @return array[] Each: {term, children[]}.
 */
function mobilekh_mega_cats() {
	$pillars = array();
	foreach ( array( 'قطعات موبایل', 'لوازم جانبی' ) as $name ) {
		$term = get_term_by( 'name', $name, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) ) {
			continue;
		}
		$children = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => $term->term_id,
				'hide_empty' => true,
				'number'     => 10,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		$pillars[] = array(
			'term'     => $term,
			'children' => is_wp_error( $children ) ? array() : $children,
		);
	}
	return $pillars;
}

/**
 * Mini-cart count for the header (works with fragments).
 */
function mobilekh_cart_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}
	return absint( WC()->cart->get_cart_contents_count() );
}

/**
 * SMSIRApp enqueues its admin table scripts site-wide (missing is_admin guard).
 * They fatal on the frontend ($().DataTable) while order-SMS is server-side.
 * Remove them from the frontend only.
 */
function mobilekh_smsir_frontend_cleanup() {
	wp_dequeue_script( 'smsIr' );
	wp_deregister_script( 'smsIr' );
	wp_dequeue_script( 'dataTable' );
	wp_deregister_script( 'dataTable' );
	wp_dequeue_style( 'smsIr' );
	wp_deregister_style( 'smsIr' );
	wp_dequeue_style( 'dataTable' );
	wp_deregister_style( 'dataTable' );
}
add_action( 'wp_enqueue_scripts', 'mobilekh_smsir_frontend_cleanup', 1000 );
// SMSIR enqueues its admin script again during product rendering
// (woocommerce_product_meta_start), i.e. after wp_enqueue_scripts ran.
// Catch it right before footer printing too.
add_action( 'wp_print_footer_scripts', 'mobilekh_smsir_frontend_cleanup', 1 );
add_action( 'wp_print_styles', 'mobilekh_smsir_frontend_cleanup', 1 );

/**
 * Brand line + badges after the single-product title.
 */
function mobilekh_single_brand_badges() {
	global $product;
	if ( ! $product ) {
		return;
	}
	$brands = wc_get_product_terms( $product->get_id(), 'pa_brand' );
	if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
		$links = array();
		foreach ( $brands as $b ) {
			$url = add_query_arg( 'filter_brand', $b->slug, get_permalink( wc_get_page_id( 'shop' ) ) );
			$links[] = '<a href="' . esc_url( $url ) . '">' . esc_html( $b->name ) . '</a>';
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — escaped above.
		echo '<div class="mk-brand">' . esc_html__( 'برند:', 'mobilekh' ) . ' ' . implode( '، ', $links ) . '</div>';
	}
	$badges = array();
	if ( $product->is_on_sale() ) {
		$badges[] = '<span class="mk-flag mk-flag-sale">' . esc_html__( 'تخفیف', 'mobilekh' ) . '</span>';
	}
	$created = get_post_time( 'U', false, $product->get_id() );
	if ( $created && ( time() - $created ) < 90 * DAY_IN_SECONDS ) {
		$badges[] = '<span class="mk-flag mk-flag-new">' . esc_html__( 'جدید', 'mobilekh' ) . '</span>';
	}
	if ( $product->get_total_sales() >= 5 ) {
		$badges[] = '<span class="mk-flag mk-flag-best">' . esc_html__( 'پرفروش', 'mobilekh' ) . '</span>';
	}
	if ( $badges ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static strings above.
		echo '<div class="mk-flags">' . implode( '', $badges ) . '</div>';
	}
}
add_action( 'woocommerce_single_product_summary', 'mobilekh_single_brand_badges', 6 );

/**
 * Recently viewed (cookie, max 12).
 */
define( 'MOBILEKH_RV_COOKIE', 'mobilekh_rv' );
function mobilekh_rv_track() {
	if ( ! is_product() ) {
		return;
	}
	$pid = absint( get_queried_object_id() );
	if ( $pid <= 0 ) {
		return;
	}
	$ids = array();
	if ( ! empty( $_COOKIE[ MOBILEKH_RV_COOKIE ] ) ) {
		$ids = array_filter( array_map( 'absint', explode( ',', wp_unslash( $_COOKIE[ MOBILEKH_RV_COOKIE ] ) ) ) );
	}
	$ids = array_values( array_unique( array_merge( array( $pid ), $ids ) ) );
	$ids = array_slice( $ids, 0, 12 );
	setcookie( MOBILEKH_RV_COOKIE, implode( ',', $ids ), time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
	$_COOKIE[ MOBILEKH_RV_COOKIE ] = implode( ',', $ids );
}
add_action( 'template_redirect', 'mobilekh_rv_track', 2 );

function mobilekh_rv_ids() {
	if ( empty( $_COOKIE[ MOBILEKH_RV_COOKIE ] ) ) {
		return array();
	}
	return array_values( array_unique( array_filter( array_map( 'absint', explode( ',', wp_unslash( $_COOKIE[ MOBILEKH_RV_COOKIE ] ) ) ) ) ) );
}

/**
 * Shop "only in stock" filter (?stock=1).
 */
function mobilekh_stock_query( $q ) {
	if ( isset( $_GET['stock'] ) && '1' === $_GET['stock'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended — public filter.
		$meta = (array) $q->get( 'meta_query' );
		$meta[] = array(
			'key'     => '_stock_status',
			'value'   => 'instock',
			'compare' => '=',
		);
		$q->set( 'meta_query', $meta );
	}
}
add_action( 'woocommerce_product_query', 'mobilekh_stock_query' );

/**
 * Checkout progress steps (cart → details → done).
 */
function mobilekh_checkout_steps() {
	if ( ! function_exists( 'is_cart' ) ) {
		return;
	}
	$step = 0;
	if ( is_cart() ) {
		$step = 1;
	} elseif ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
		$step = 2;
	} elseif ( is_wc_endpoint_url( 'order-received' ) || is_wc_endpoint_url( 'order-pay' ) ) {
		$step = 3;
	} else {
		return;
	}
	$steps = array(
		1 => array( __( 'سبد خرید', 'mobilekh' ), wc_get_cart_url() ),
		2 => array( __( 'اطلاعات و پرداخت', 'mobilekh' ), wc_get_checkout_url() ),
		3 => array( __( 'تکمیل سفارش', 'mobilekh' ), '' ),
	);
	echo '<ol class="checkout-steps">';
	foreach ( $steps as $n => $s ) {
		$cls = $n < $step ? 'done' : ( $n === $step ? 'now' : '' );
		echo '<li class="' . esc_attr( $cls ) . '">';
		if ( '' !== $s[1] && $n <= $step ) {
			echo '<a href="' . esc_url( $s[1] ) . '"><span class="step-n">' . esc_html( $n ) . '</span>' . esc_html( $s[0] ) . '</a>';
		} else {
			echo '<span><span class="step-n">' . esc_html( $n ) . '</span>' . esc_html( $s[0] ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ol>';
}
add_action( 'woocommerce_before_cart', 'mobilekh_checkout_steps', 5 );
add_action( 'woocommerce_cart_is_empty', 'mobilekh_checkout_steps', 5 );
add_action( 'woocommerce_before_checkout_form', 'mobilekh_checkout_steps', 5 );
add_action( 'woocommerce_thankyou', 'mobilekh_checkout_steps', 5 );

/**
 * Single-product buy box wrapper + trust badges.
 */
function mobilekh_buybox_open() {
	if ( is_product() ) {
		echo '<div class="buy-box">';
	}
}
add_action( 'woocommerce_single_product_summary', 'mobilekh_buybox_open', 9 );
function mobilekh_buybox_close() {
	if ( is_product() ) {
		echo '</div>';
	}
}
add_action( 'woocommerce_single_product_summary', 'mobilekh_buybox_close', 35 );
function mobilekh_trust_row() {
	if ( ! is_product() ) {
		return;
	}
	?>
	<div class="trust-row">
		<div class="trust-item"><?php mobilekh_icon( 'support' ); ?><span><?php esc_html_e( '۷ روز ضمانت بازگشت', 'mobilekh' ); ?></span></div>
			<div class="trust-item"><?php mobilekh_icon( 'truck' ); ?><span><?php esc_html_e( 'ارسال سریع به سراسر کشور', 'mobilekh' ); ?></span></div>
		<div class="trust-item"><?php mobilekh_icon( 'shield' ); ?><span><?php esc_html_e( 'پرداخت امن', 'mobilekh' ); ?></span></div>
	</div>
	<?php
}
add_action( 'woocommerce_single_product_summary', 'mobilekh_trust_row', 36 );

/**
 * Stock availability as a pill.
 */
function mobilekh_stock_pill( $html ) {
	$html = str_replace( 'class="stock ', 'class="stock stock-pill ', $html );
	$html = str_replace( 'in-stock', 'in', $html );
	$html = str_replace( 'out-of-stock', 'out', $html );
	return $html;
}
add_filter( 'woocommerce_stock_html', 'mobilekh_stock_pill', 20 );

/**
 * Helpful button when shop search finds nothing.
 */
function mobilekh_no_products_cta() {
	echo '<p class="no-products-cta"><a class="btn" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'مشاهده همه محصولات', 'mobilekh' ) . '</a></p>';
}
add_action( 'woocommerce_no_products_found', 'mobilekh_no_products_cta', 20 );

/**
 * Friendlier pagination labels (shop + blog).
 */
function mobilekh_pagination_args( $args ) {
	$args['prev_text'] = __( 'قبلی', 'mobilekh' );
	$args['next_text'] = __( 'بعدی', 'mobilekh' );
	$args['end_size']  = 1;
	$args['mid_size']   = 1;
	return $args;
}
add_filter( 'woocommerce_pagination_args', 'mobilekh_pagination_args' );

/**
 * Heart button after the single-product add-to-cart.
 */
function mobilekh_single_wl() {
	global $product;
	if ( $product ) {
		mobilekh_wl_button( $product->get_id() );
	}
}
add_action( 'woocommerce_after_add_to_cart_button', 'mobilekh_single_wl' );

/**
 * Empty-cart notice without emoji, on-voice.
 */
function mobilekh_empty_cart_message() {
	return __( 'سبد خرید شما خالی است.', 'mobilekh' );
}
add_filter( 'wc_empty_cart_message', 'mobilekh_empty_cart_message' );

/**
 * Translate cryptic Zibal result codes into actionable Persian hints.
 * Code 115 = the server IP is not whitelisted in the Zibal merchant panel.
 * Uses the gateway's own filters, so plugin updates cannot break it.
 *
 * @param string $notice  Notice text from the gateway.
 * @param int    $order_id Order ID.
 * @return string
 */
function mobilekh_zibal_error_hint( $notice, $order_id ) {
	if ( false !== strpos( $notice, '115' ) && false !== strpos( $notice, 'تراکنش ناموفق' ) ) {
		$notice .= '<br>' . __( 'راهنما: کد ۱۱۵ یعنی آی‌پی سرور فروشگاه در پنل زیبال مجاز نیست. در پنل زیبال (تنظیمات درگاه ← محدودیت IP) آی‌پی سرور را اضافه کنید یا محدودیت را بردارید.', 'mobilekh' );
	}
	return $notice;
}
add_filter( 'WC_Zibal_Send_to_Gateway_Failed_Note', 'mobilekh_zibal_error_hint', 10, 2 );
add_filter( 'WC_Zibal_Send_to_Gateway_Failed_Notice', 'mobilekh_zibal_error_hint', 10, 2 );

/**
 * Failed Zibal returns leave orders pending forever (neither gateway marks
 * them failed, so stock stays held). Both gateways fire this same action.
 *
 * @param int $order_id Order ID.
 */
function mobilekh_zibal_mark_failed( $order_id ) {
	if ( ! function_exists( 'wc_get_order' ) ) {
		return;
	}
	$order = wc_get_order( absint( $order_id ) );
	if ( ! $order instanceof WC_Order ) {
		return;
	}
	if ( $order->needs_payment() && ! $order->has_status( 'failed' ) ) {
		$order->update_status( 'failed', __( 'پرداخت توسط درگاه ناموفق اعلام شد.', 'mobilekh' ) );
	}
}
add_action( 'WC_Zibal_Return_from_Gateway_Failed', 'mobilekh_zibal_mark_failed', 10, 1 );

/**
 * Lightweight product card used by homepage sections and archives.
 *
 * @param WC_Product $product Product object.
 */
function mobilekh_product_card( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}
	$pid   = $product->get_id();
	$url   = get_permalink( $pid );
	$name  = $product->get_name();
	$price = $product->get_price_html();
	$sale  = $product->is_on_sale();
	$stock = $product->is_in_stock();
	?>
	<article class="p-card<?php echo $stock ? '' : ' p-card-out'; ?>">
		<a class="p-card-media" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $name ); ?>">
			<?php echo $product->get_image( 'mobilekh-card', array( 'class' => 'p-card-img', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $sale ) : ?>
				<span class="p-badge p-badge-sale"><?php esc_html_e( 'تخفیف', 'mobilekh' ); ?></span>
			<?php endif; ?>
			<?php if ( ! $stock ) : ?>
				<span class="p-badge p-badge-out"><?php esc_html_e( 'ناموجود', 'mobilekh' ); ?></span>
			<?php endif; ?>
		</a>
		<?php mobilekh_wl_button( $pid ); ?>
		<div class="p-card-body">
			<h3 class="p-card-title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
			<div class="p-card-price"><?php echo wp_kses_post( $price ); ?></div>
			<div class="p-card-actions">
				<?php if ( $stock ) : ?>
				<a class="btn btn-sm" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-product-id="<?php echo esc_attr( $pid ); ?>">
					<?php esc_html_e( 'افزودن به سبد', 'mobilekh' ); ?>
				</a>
				<?php else : ?>
				<button class="btn btn-sm" type="button" disabled><?php esc_html_e( 'ناموجود', 'mobilekh' ); ?></button>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
}
