<?php
/** 404. @package mobilekh */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="container section">
	<h1><?php esc_html_e( 'صفحه پیدا نشد', 'mobilekh' ); ?></h1>
	<p class="muted"><?php esc_html_e( 'آدرس وارد شده معتبر نیست.', 'mobilekh' ); ?></p>
	<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'بازگشت به خانه', 'mobilekh' ); ?></a>
</div>
<?php
get_footer();
