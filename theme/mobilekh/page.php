<?php
/** Single page. @package mobilekh */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="page-hero">
	<div class="container">
		<h1><?php the_title(); ?></h1>
		<p class="page-hero-sub"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'خانه', 'mobilekh' ); ?></a> / <?php the_title(); ?></p>
	</div>
</div>
<div class="container section page-wrap">
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class( 'page-article' ); ?>>
			<?php if ( is_page( 9580 ) ) : ?>
				<div class="contact-cards">
					<a class="contact-card" href="tel:02632209919">
						<span class="contact-ico"><?php mobilekh_icon( 'phone' ); ?></span>
						<span class="contact-t"><?php esc_html_e( 'تلفن فروشگاه', 'mobilekh' ); ?></span>
						<span class="contact-v">02632209919</span>
					</a>
					<a class="contact-card" href="tel:09960111224">
						<span class="contact-ico"><?php mobilekh_icon( 'support' ); ?></span>
						<span class="contact-t"><?php esc_html_e( 'پشتیبانی', 'mobilekh' ); ?></span>
						<span class="contact-v">09960111224</span>
					</a>
					<a class="contact-card" href="mailto:nimakhorsandi4040@gmail.com">
						<span class="contact-ico"><?php mobilekh_icon( 'mail' ); ?></span>
						<span class="contact-t"><?php esc_html_e( 'ایمیل', 'mobilekh' ); ?></span>
						<span class="contact-v" dir="ltr">nimakhorsandi4040@gmail.com</span>
					</a>
					<div class="contact-card">
						<span class="contact-ico"><?php mobilekh_icon( 'clock' ); ?></span>
						<span class="contact-t"><?php esc_html_e( 'ساعات پاسخگویی', 'mobilekh' ); ?></span>
						<span class="contact-v"><?php esc_html_e( '۷ روز هفته، ۱۰ صبح تا ۸ شب', 'mobilekh' ); ?></span>
					</div>
				</div>
			<?php endif; ?>
			<?php $is_auth_page = mobilekh_is_auth_page() && ! is_user_logged_in(); ?>
			<?php if ( $is_auth_page ) : ?>
				<?php $otp_mode = shortcode_exists( 'smsir_login' ); ?>
				<div class="login-hero<?php echo $otp_mode ? ' login-otp' : ''; ?>">
					<div class="login-side">
						<span class="login-eyebrow"><?php echo $otp_mode ? esc_html__( 'ورود امن با پیامک', 'mobilekh' ) : esc_html__( 'حساب کاربری', 'mobilekh' ); ?></span>
						<h2><?php esc_html_e( 'به موبایل خرسندی خوش آمدید', 'mobilekh' ); ?></h2>
						<p><?php echo $otp_mode ? esc_html__( 'فقط شماره موبایلت را بزن؛ کد تایید پیامک می‌شود و وارد می‌شوی. بدون رمز، بدون ایمیل.', 'mobilekh' ) : esc_html__( 'وارد حساب خود شوید تا سریع‌تر خرید کنید و سفارش‌هایتان را پیگیری کنید.', 'mobilekh' ); ?></p>
						<?php if ( $otp_mode ) : ?>
							<ol class="otp-steps">
								<li><b>۱</b><span><?php esc_html_e( 'شماره موبایل', 'mobilekh' ); ?></span></li>
								<li><b>۲</b><span><?php esc_html_e( 'کد پیامک', 'mobilekh' ); ?></span></li>
								<li><b>۳</b><span><?php esc_html_e( 'ورود', 'mobilekh' ); ?></span></li>
							</ol>
						<?php else : ?>
							<ul>
								<li><?php esc_html_e( 'پیگیری لحظه‌ای سفارش‌ها', 'mobilekh' ); ?></li>
								<li><?php esc_html_e( 'تسویه سریع‌تر', 'mobilekh' ); ?></li>
								<li><?php esc_html_e( 'لیست علاقه‌مندی‌ها', 'mobilekh' ); ?></li>
							</ul>
						<?php endif; ?>
					</div>
					<div class="login-forms">
						<?php if ( $otp_mode ) : ?>
							<div class="otp-box"><?php echo do_shortcode( '[smsir_login]' ); ?></div>
						<?php else : ?>
							<?php the_content(); ?>
						<?php endif; ?>
					</div>
				</div>
			<?php else : ?>
				<div class="page-content"><?php the_content(); ?></div>
			<?php endif; ?>
		</article>
	<?php endwhile; ?>
</div>
<?php
get_footer();
