<?php
/**
 * Shop archive — native loop with MobileKh cards. All core hooks preserved
 * for RankMath, Persian Woo and other integrations.
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container section">
	<?php do_action( 'woocommerce_before_main_content' ); ?>

	<header class="shop-head">
		<?php if ( is_product_category() ) : ?>
			<h1 class="shop-title"><?php echo esc_html( single_term_title( '', false ) ); ?></h1>
			<?php the_archive_description( '<p class="muted">', '</p>' ); ?>
		<?php elseif ( is_search() ) : ?>
			<h1 class="shop-title"><?php printf( esc_html__( 'نتایج جستجو برای: %s', 'mobilekh' ), esc_html( get_search_query() ) ); ?></h1>
			<p class="muted"><?php printf( esc_html__( '%s نتیجه پیدا شد', 'mobilekh' ), esc_html( $GLOBALS['wp_query']->found_posts ) ); ?></p>
		<?php else : ?>
			<h1 class="shop-title"><?php esc_html_e( 'فروشگاه', 'mobilekh' ); ?></h1>
		<?php endif; ?>
	</header>

	<div class="shop-toolbar">
		<?php do_action( 'woocommerce_before_shop_loop' ); ?>
		<?php
		$stock_on  = isset( $_GET['stock'] ) && '1' === $_GET['stock']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended — public filter.
		$stock_url = $stock_on ? remove_query_arg( 'stock' ) : add_query_arg( 'stock', '1' );
		?>
		<a class="pill stock-toggle<?php echo $stock_on ? ' pill-active' : ''; ?>" href="<?php echo esc_url( $stock_url ); ?>"><?php esc_html_e( 'فقط کالاهای موجود', 'mobilekh' ); ?></a>
	</div>

	<?php
	$top_cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => true,
			'number'     => 12,
		)
	);
	?>
	<?php if ( ! is_wp_error( $top_cats ) && ! empty( $top_cats ) ) : ?>
	<nav class="cat-pills" aria-label="<?php esc_attr_e( 'دسته‌بندی‌ها', 'mobilekh' ); ?>">
		<?php foreach ( $top_cats as $cat ) : ?>
			<?php $link = get_term_link( $cat ); ?>
			<a class="pill<?php echo is_tax( 'product_cat', $cat->term_id ) ? ' pill-active' : ''; ?>" href="<?php echo esc_url( is_wp_error( $link ) ? '#' : $link ); ?>">
				<?php echo esc_html( $cat->name ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php endif; ?>

	<?php if ( woocommerce_product_loop() ) : ?>
		<div class="p-grid shop-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$mobilekh_p = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
				if ( $mobilekh_p instanceof WC_Product ) {
					mobilekh_product_card( $mobilekh_p );
					continue;
				}
				// Non-product hit (e.g. query altered by another plugin): generic card, never a blank cell.
				?>
				<article <?php post_class( 'p-card' ); ?>>
					<div class="p-card-body">
						<h3 class="p-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
					</div>
				</article>
				<?php
			endwhile;
			?>
		</div>
		<?php do_action( 'woocommerce_after_shop_loop' ); ?>
	<?php else : ?>
		<?php do_action( 'woocommerce_no_products_found' ); ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_main_content' ); ?>
</div>

<?php
get_footer();
