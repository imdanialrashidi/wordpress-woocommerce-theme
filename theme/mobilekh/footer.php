</main>
<footer class="site-footer">
	<div class="container footer-grid">
		<div class="footer-col footer-brand">
			<span class="brand-logo logo-light"><?php mobilekh_logo( 'light' ); ?></span>
			<span class="brand-logo logo-dark"><?php mobilekh_logo( 'dark' ); ?></span>
			<p class="muted"><?php esc_html_e( 'ارزان بخرید، باکیفیت استفاده کنید.', 'mobilekh' ); ?></p>
			<div class="social-row">
				<?php $ig = get_theme_mod( 'mobilekh_social_instagram', '' ); ?>
				<?php if ( '' !== $ig ) : ?><a class="social-btn" href="<?php echo esc_url( $ig ); ?>" target="_blank" rel="noopener" aria-label="Instagram"><?php mobilekh_icon( 'instagram' ); ?></a><?php endif; ?>
				<?php $tg = get_theme_mod( 'mobilekh_social_telegram', '' ); ?>
				<?php if ( '' !== $tg ) : ?><a class="social-btn" href="<?php echo esc_url( $tg ); ?>" target="_blank" rel="noopener" aria-label="Telegram"><?php mobilekh_icon( 'telegram' ); ?></a><?php endif; ?>
				<?php $wa = get_theme_mod( 'mobilekh_social_whatsapp', '' ); ?>
				<?php if ( '' !== $wa ) : ?><a class="social-btn" href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?php mobilekh_icon( 'whatsapp' ); ?></a><?php endif; ?>
			</div>
		</div>
		<div class="footer-col">
			<h3 class="footer-title"><?php esc_html_e( 'دسترسی سریع', 'mobilekh' ); ?></h3>
			<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'menu-stack', 'fallback_cb' => false ) ); ?>
		</div>
		<div class="footer-col">
			<h3 class="footer-title"><?php esc_html_e( 'دسته‌بندی کالاها', 'mobilekh' ); ?></h3>
			<?php
			$foot_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'parent'     => 0,
					'hide_empty' => true,
					'number'     => 6,
				)
			);
			?>
			<?php if ( ! is_wp_error( $foot_cats ) && ! empty( $foot_cats ) ) : ?>
			<ul class="menu-stack">
				<?php foreach ( $foot_cats as $fc ) : ?>
					<?php $flink = get_term_link( $fc ); ?>
					<li><a href="<?php echo esc_url( is_wp_error( $flink ) ? '#' : $flink ); ?>"><?php echo esc_html( $fc->name ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
		<div class="footer-col footer-contact">
			<h3 class="footer-title"><?php esc_html_e( 'راه‌های ارتباطی', 'mobilekh' ); ?></h3>
			<?php $addr = get_theme_mod( 'mobilekh_address', '' ); ?>
			<?php if ( '' !== $addr ) : ?><p><?php mobilekh_icon( 'pin' ); ?><?php echo esc_html( $addr ); ?></p><?php endif; ?>
			<?php $phone = get_theme_mod( 'mobilekh_phone', '' ); ?>
			<?php if ( '' !== $phone ) : ?><p><a href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $phone ) ); ?>"><?php mobilekh_icon( 'phone' ); ?><?php echo esc_html( $phone ); ?></a></p><?php endif; ?>
			<?php $hours = get_theme_mod( 'mobilekh_hours', '' ); ?>
			<?php if ( '' !== $hours ) : ?><p class="muted"><?php mobilekh_icon( 'clock' ); ?><?php echo esc_html( $hours ); ?></p><?php endif; ?>
			<?php $eid = get_theme_mod( 'mobilekh_enamad_id', '505880' ); $ecode = get_theme_mod( 'mobilekh_enamad_code', 'tSrnqxtKD6nS4i9gvC0Oh7Fv6uYkLKWY' ); ?>
			<?php if ( '' !== $eid && '' !== $ecode ) : ?>
				<a class="enamad" href="<?php echo esc_url( 'https://trustseal.enamad.ir/?id=' . $eid . '&Code=' . $ecode ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'اینماد', 'mobilekh' ); ?>">
					<?php $enamad_img = absint( get_theme_mod( 'mobilekh_enamad_img', 0 ) ); ?>
					<?php if ( $enamad_img > 0 ) : ?>
						<?php echo wp_get_attachment_image( $enamad_img, 'medium', false, array( 'alt' => esc_attr__( 'نشان اعتماد الکترونیکی', 'mobilekh' ), 'loading' => 'lazy' ) ); ?>
					<?php else : ?>
					<img src="<?php echo esc_url( 'https://trustseal.enamad.ir/logo.aspx?id=' . $eid . '&Code=' . $ecode ); ?>" alt="<?php esc_attr_e( 'نشان اعتماد الکترونیکی', 'mobilekh' ); ?>" loading="lazy" width="125" height="136" onerror="this.closest('.enamad').style.display='none'">
					<?php endif; ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<div class="footer-bottom">
		<div class="container footer-bottom-inner">
			<span><?php esc_html_e( 'تمامی حقوق مادی و معنوی این سایت متعلق به موبایل خرسندی می باشد.', 'mobilekh' ); ?></span>
			<?php if ( function_exists( 'WC' ) ) : ?>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ) ); ?>"><?php esc_html_e( 'پیگیری سفارش', 'mobilekh' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</footer>

<nav class="mobile-bottomnav" aria-label="<?php esc_attr_e( 'ناوبری موبایل', 'mobilekh' ); ?>">
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php mobilekh_icon( 'home' ); ?><span><?php esc_html_e( 'خانه', 'mobilekh' ); ?></span></a>
	<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_shop_page_id' ) ) ); ?>"><?php mobilekh_icon( 'shop' ); ?><span><?php esc_html_e( 'فروشگاه', 'mobilekh' ); ?></span></a>
	<?php if ( function_exists( 'WC' ) ) : ?>
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php mobilekh_icon( 'cart' ); ?><span><?php esc_html_e( 'سبد', 'mobilekh' ); ?></span></a>
	<?php endif; ?>
	<a href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>"><?php mobilekh_icon( 'user' ); ?><span><?php esc_html_e( 'حساب', 'mobilekh' ); ?></span></a>
</nav>

<?php if ( get_theme_mod( 'mobilekh_support_float', true ) ) : ?>
	<?php $sphone = get_theme_mod( 'mobilekh_phone', '' ); $swa = get_theme_mod( 'mobilekh_social_whatsapp', '' ); ?>
	<?php if ( '' !== $sphone || '' !== $swa ) : ?>
<div class="support-float">
	<?php if ( '' !== $swa ) : ?><a class="support-btn support-wa" href="<?php echo esc_url( $swa ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?php mobilekh_icon( 'whatsapp' ); ?></a><?php endif; ?>
	<?php if ( '' !== $sphone ) : ?><a class="support-btn support-tel" href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $sphone ) ); ?>" aria-label="<?php esc_attr_e( 'تماس', 'mobilekh' ); ?>"><?php mobilekh_icon( 'phone' ); ?></a><?php endif; ?>
</div>
	<?php endif; ?>
<?php endif; ?>

<?php if ( get_theme_mod( 'mobilekh_popup_on', false ) ) : ?>
<div class="popup" id="mobilekh-popup" hidden role="dialog" aria-modal="true" aria-labelledby="mobilekh-popup-title">
	<div class="popup-card">
		<button class="icon-btn popup-close" aria-label="<?php esc_attr_e( 'بستن', 'mobilekh' ); ?>"><?php mobilekh_icon( 'close' ); ?></button>
		<?php $pt = get_theme_mod( 'mobilekh_popup_title', '' ); ?>
		<?php if ( '' !== $pt ) : ?><h2 id="mobilekh-popup-title"><?php echo esc_html( $pt ); ?></h2><?php endif; ?>
		<div class="popup-text"><?php echo wp_kses_post( get_theme_mod( 'mobilekh_popup_text', '' ) ); ?></div>
		<?php $bt = get_theme_mod( 'mobilekh_popup_btn_text', '' ); $bu = get_theme_mod( 'mobilekh_popup_btn_url', '' ); ?>
		<?php if ( '' !== $bt && '' !== $bu ) : ?>
			<a class="btn" href="<?php echo esc_url( $bu ); ?>"><?php echo esc_html( $bt ); ?></a>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
