<?php
/**
 * Single product — signature layout, standard Woo hooks preserved
 * (gallery, summary, tabs, related, structured data).
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container section">
	<?php do_action( 'woocommerce_before_main_content' ); ?>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php $product = wc_get_product( get_the_ID() ); ?>
		<?php do_action( 'woocommerce_before_single_product' ); ?>

		<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'mk-single', $product ); ?>>
			<div class="mk-single-top">
				<div class="mk-gallery">
					<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
				</div>
				<div class="mk-summary">
					<?php do_action( 'woocommerce_single_product_summary' ); ?>
				</div>
			</div>
			<div class="mk-tabs">
				<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
			</div>
		</div>

		<?php do_action( 'woocommerce_after_single_product' ); ?>
	<?php endwhile; ?>

	<?php
	$rv = array_diff( mobilekh_rv_ids(), array( get_the_ID() ) );
	$rv = array_slice( array_values( $rv ), 0, 4 );
	?>
	<?php if ( ! empty( $rv ) ) : ?>
		<?php $rv_products = wc_get_products( array( 'include' => $rv, 'limit' => 4, 'status' => 'publish' ) ); ?>
		<?php if ( ! empty( $rv_products ) ) : ?>
	<section class="related products">
		<h2><?php esc_html_e( 'بازدید اخیر شما', 'mobilekh' ); ?></h2>
		<div class="p-grid">
			<?php foreach ( $rv_products as $rp ) { mobilekh_product_card( $rp ); } ?>
		</div>
	</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_main_content' ); ?>
</div>

<?php if ( $product && $product->is_in_stock() ) : ?>
<div class="sticky-atc" hidden>
	<div class="sticky-atc-info">
		<strong><?php echo esc_html( $product->get_name() ); ?></strong>
		<span class="sticky-atc-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
	</div>
	<button class="btn sticky-atc-btn" type="button"><?php esc_html_e( 'افزودن به سبد', 'mobilekh' ); ?></button>
</div>
<?php endif; ?>

<?php
get_footer();
