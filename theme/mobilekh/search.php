<?php
/** Search results — products as cards, posts as list, designed empty state. @package mobilekh */
defined( 'ABSPATH' ) || exit;
get_header();

$q         = get_search_query();
$is_prods  = isset( $_GET['post_type'] ) && 'product' === $_GET['post_type']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended — public filter.
$has_posts = have_posts();
?>
<div class="container section">
	<header class="shop-head">
		<h1 class="shop-title"><?php printf( esc_html__( 'نتایج جستجو برای: %s', 'mobilekh' ), esc_html( $q ) ); ?></h1>
		<?php if ( $has_posts ) : ?>
			<p class="muted"><?php printf( esc_html__( '%s نتیجه پیدا شد', 'mobilekh' ), esc_html( $GLOBALS['wp_query']->found_posts ) ); ?></p>
		<?php endif; ?>
	</header>

	<?php if ( $has_posts ) : ?>
		<?php if ( $is_prods ) : ?>
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
		<?php else : ?>
			<div class="search-list">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<article <?php post_class( 'search-row' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="search-row-thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'thumbnail' ); ?></a>
						<?php endif; ?>
						<div>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="muted"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="empty-state">
			<div class="empty-ico"><?php mobilekh_icon( 'search' ); ?></div>
			<h2><?php esc_html_e( 'نتیجه‌ای پیدا نشد', 'mobilekh' ); ?></h2>
			<p class="muted"><?php esc_html_e( 'عبارت دیگری را امتحان کنید یا از دسته‌بندی‌های فروشگاه دیدن کنید.', 'mobilekh' ); ?></p>
			<div class="empty-actions">
				<?php $mobilekh_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ); ?>
				<a class="btn" href="<?php echo esc_url( $mobilekh_shop_url ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'mobilekh' ); ?></a>
				<a class="pill" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>"><?php esc_html_e( 'وبلاگ', 'mobilekh' ); ?></a>
			</div>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
