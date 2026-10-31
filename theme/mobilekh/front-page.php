<?php
/**
 * Front page — homepage sections driven by Customizer + real store data.
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;
get_header();

$slides = array();
for ( $i = 1; $i <= 3; $i++ ) {
	$img = absint( get_theme_mod( "mobilekh_slide_{$i}_img", 0 ) );
	if ( $img > 0 ) {
		$slides[] = array(
			'img'   => $img,
			'title' => get_theme_mod( "mobilekh_slide_{$i}_title", '' ),
			'sub'   => get_theme_mod( "mobilekh_slide_{$i}_sub", '' ),
			'url'   => get_theme_mod( "mobilekh_slide_{$i}_url", '' ),
		);
	}
}
?>

<div class="container">

	<?php if ( get_theme_mod( 'mobilekh_sec_hero', true ) ) : ?>
	<section class="hero-slider" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'پیشنهادهای ویژه', 'mobilekh' ); ?>">
		<div class="hero-track">
			<?php if ( ! empty( $slides ) ) : ?>
				<?php foreach ( $slides as $s ) : ?>
				<div class="hero-slide">
					<?php echo wp_get_attachment_image( $s['img'], 'large', false, array( 'class' => 'hero-slide-img', 'fetchpriority' => 'high', 'data-no-lazy' => '1' ) ); ?>
					<div class="hero-slide-cap">
						<?php if ( '' !== $s['title'] ) : ?><h2><?php echo esc_html( $s['title'] ); ?></h2><?php endif; ?>
						<?php if ( '' !== $s['sub'] ) : ?><p><?php echo esc_html( $s['sub'] ); ?></p><?php endif; ?>
						<?php if ( '' !== $s['url'] ) : ?><a class="btn" href="<?php echo esc_url( $s['url'] ); ?>"><?php esc_html_e( 'مشاهده', 'mobilekh' ); ?></a><?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
			<?php else : ?>
			<div class="hero-slide hero-fallback">
				<div class="hero-inner">
					<div class="hero-copy">
						<h1 class="hero-h1"><?php echo esc_html( get_theme_mod( 'mobilekh_hero_title', get_bloginfo( 'name' ) ) ); ?></h1>
						<?php $sub = get_theme_mod( 'mobilekh_hero_sub', '' ); ?>
						<?php if ( '' !== $sub ) : ?><p class="hero-sub"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
						<div class="hero-cta">
							<?php $cta = get_theme_mod( 'mobilekh_hero_cta_text', '' ); $curl = get_theme_mod( 'mobilekh_hero_cta_url', '' ); ?>
							<?php if ( '' !== $cta && '' !== $curl ) : ?>
								<a class="btn" href="<?php echo esc_url( $curl ); ?>"><?php echo esc_html( $cta ); ?></a>
							<?php endif; ?>
							<a class="btn btn-ghost" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'mobilekh' ); ?></a>
						</div>
						<ul class="hero-badges">
							<li><?php mobilekh_icon( 'shield' ); ?><?php esc_html_e( 'ضمانت اصل بودن کالا', 'mobilekh' ); ?></li>
							<li><?php mobilekh_icon( 'truck' ); ?><?php esc_html_e( 'ارسال با پست پیشتاز', 'mobilekh' ); ?></li>
							<li><?php mobilekh_icon( 'support' ); ?><?php esc_html_e( 'پشتیبانی پاسخگو', 'mobilekh' ); ?></li>
						</ul>
					</div>
					<div class="hero-visual" aria-hidden="true">
						<div class="hero-ring"><?php mobilekh_icon( 'shop' ); ?></div>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php if ( count( $slides ) > 1 ) : ?>
		<button class="hero-arrow hero-prev" aria-label="<?php esc_attr_e( 'قبلی', 'mobilekh' ); ?>">‹</button>
		<button class="hero-arrow hero-next" aria-label="<?php esc_attr_e( 'بعدی', 'mobilekh' ); ?>">›</button>
		<div class="hero-dots" role="tablist">
			<?php foreach ( $slides as $k => $s ) : ?>
				<button role="tab" aria-label="<?php printf( esc_attr__( 'اسلاید %d', 'mobilekh' ), absint( $k + 1 ) ); ?>"></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</section>
	<?php endif; ?>

	<div class="marquee" aria-hidden="true">
		<div class="marquee-track">
			<?php for ( $r = 0; $r < 2; $r++ ) : ?>
			<span><?php esc_html_e( 'ارسال پیشتاز به سراسر کشور', 'mobilekh' ); ?> • </span>
			<span><?php esc_html_e( 'ضمانت اصالت کالا', 'mobilekh' ); ?> • </span>
			<span><?php esc_html_e( 'پشتیبانی پاسخگو', 'mobilekh' ); ?> • </span>
			<span><?php esc_html_e( 'قیمت منصفانه', 'mobilekh' ); ?> • </span>
			<?php endfor; ?>
		</div>
	</div>

	<?php
	$story_cats = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
			'number'     => 10,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	?>
	<?php if ( get_theme_mod( 'mobilekh_sec_cats', true ) && ! is_wp_error( $story_cats ) && ! empty( $story_cats ) ) : ?>
	<section class="section reveal">
		<div class="stories">
			<?php foreach ( $story_cats as $cat ) : ?>
				<?php
				$thumb_id = absint( get_term_meta( $cat->term_id, 'thumbnail_id', true ) );
				$link     = get_term_link( $cat );
				?>
				<a class="story" href="<?php echo esc_url( is_wp_error( $link ) ? '#' : $link ); ?>">
					<span class="story-ring">
						<?php if ( $thumb_id ) : ?>
							<?php echo wp_get_attachment_image( $thumb_id, 'thumbnail', false, array( 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<?php mobilekh_icon( 'shop' ); ?>
						<?php endif; ?>
					</span>
					<span class="story-name"><?php echo esc_html( $cat->name ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'mobilekh_sec_deals', true ) && function_exists( 'wc_get_products' ) ) : ?>
		<?php $deals = wc_get_products( array( 'limit' => 4, 'status' => 'publish', 'on_sale' => true ) ); ?>
		<?php if ( ! empty( $deals ) ) : ?>
	<section class="section reveal">
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e( 'پیشنهادهای تخفیف‌دار', 'mobilekh' ); ?></h2>
			<?php $until = get_theme_mod( 'mobilekh_deals_until', '' ); ?>
			<?php if ( '' !== $until ) : ?>
			<div class="countdown" data-until="<?php echo esc_attr( $until ); ?>" role="timer">
				<span><b data-cd="d">0</b><?php esc_html_e( 'روز', 'mobilekh' ); ?></span>:
				<span><b data-cd="h">0</b><?php esc_html_e( 'ساعت', 'mobilekh' ); ?></span>:
				<span><b data-cd="m">0</b><?php esc_html_e( 'دقیقه', 'mobilekh' ); ?></span>:
				<span><b data-cd="s">0</b><?php esc_html_e( 'ثانیه', 'mobilekh' ); ?></span>
			</div>
			<?php endif; ?>
		</div>
		<div class="p-grid">
			<?php foreach ( $deals as $p ) { mobilekh_product_card( $p ); } ?>
		</div>
	</section>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'mobilekh_sec_best', true ) && function_exists( 'wc_get_products' ) ) : ?>
	<section class="section reveal">
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e( 'پرفروش‌ها', 'mobilekh' ); ?></h2>
			<a class="link-more" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ); ?>"><?php esc_html_e( 'همه', 'mobilekh' ); ?><?php mobilekh_icon( 'arrow' ); ?></a>
		</div>
		<div class="p-grid">
			<?php
			$best = wc_get_products( array( 'limit' => 8, 'status' => 'publish', 'orderby' => 'popularity' ) );
			foreach ( $best as $p ) {
				mobilekh_product_card( $p );
			}
			?>
		</div>
	</section>
	<?php endif; ?>

	<?php
	$brands = get_terms(
		array(
			'taxonomy'   => 'pa_brand',
			'hide_empty' => true,
			'number'     => 12,
			'orderby'    => 'count',
			'order'      => 'DESC',
		)
	);
	?>
	<?php if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) : ?>
	<section class="section reveal">
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e( 'برندها', 'mobilekh' ); ?></h2>
		</div>
		<div class="brand-strip">
			<?php foreach ( $brands as $b ) : ?>
				<?php $blink = get_term_link( $b ); ?>
				<a class="brand-chip" href="<?php echo esc_url( is_wp_error( $blink ) ? '#' : $blink ); ?>"><?php echo esc_html( $b->name ); ?></a>
			<?php endforeach; ?>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'mobilekh_sec_benefits', true ) ) : ?>
	<section class="section reveal">
		<div class="benefit-grid">
			<div class="benefit">
				<span class="benefit-ico"><?php mobilekh_icon( 'shield' ); ?></span>
				<h3><?php esc_html_e( 'ضمانت اصل بودن کالا', 'mobilekh' ); ?></h3>
				<p><?php esc_html_e( 'کالای اورجینال با گارانتی معتبر', 'mobilekh' ); ?></p>
			</div>
			<div class="benefit">
				<span class="benefit-ico"><?php mobilekh_icon( 'truck' ); ?></span>
				<h3><?php esc_html_e( 'ارسال با پست پیشتاز', 'mobilekh' ); ?></h3>
				<p><?php esc_html_e( 'تحویل ۱ تا ۳ روزه در سراسر کشور', 'mobilekh' ); ?></p>
			</div>
			<div class="benefit">
				<span class="benefit-ico"><?php mobilekh_icon( 'support' ); ?></span>
				<h3><?php esc_html_e( 'پشتیبانی پاسخگو', 'mobilekh' ); ?></h3>
				<p><?php esc_html_e( 'همراه شما از خرید تا تحویل', 'mobilekh' ); ?></p>
			</div>
			<div class="benefit">
				<span class="benefit-ico"><?php mobilekh_icon( 'wallet' ); ?></span>
				<h3><?php esc_html_e( 'پرداخت امن', 'mobilekh' ); ?></h3>
				<p><?php esc_html_e( 'پرداخت آنلاین با درگاه معتبر', 'mobilekh' ); ?></p>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php if ( get_theme_mod( 'mobilekh_sec_blog', true ) ) : ?>
		<?php $posts = get_posts( array( 'numberposts' => 3, 'post_status' => 'publish' ) ); ?>
		<?php if ( ! empty( $posts ) ) : ?>
	<section class="section reveal">
		<div class="section-head">
			<h2 class="section-title"><?php esc_html_e( 'آخرین مطالب بلاگ', 'mobilekh' ); ?></h2>
			<a class="link-more" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ? get_option( 'page_for_posts' ) : 0 ) ); ?>"><?php esc_html_e( 'همه', 'mobilekh' ); ?><?php mobilekh_icon( 'arrow' ); ?></a>
		</div>
		<div class="blog-grid">
			<?php foreach ( $posts as $post ) : setup_postdata( $post ); ?>
			<article class="blog-card">
				<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium', array( 'loading' => 'lazy' ) ); ?></a>
				<div class="blog-card-body">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				</div>
			</article>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
	</section>
		<?php endif; ?>
	<?php endif; ?>

</div>

<?php
get_footer();
