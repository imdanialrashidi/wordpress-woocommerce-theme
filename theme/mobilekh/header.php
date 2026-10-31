<!DOCTYPE html>
<?php $mobilekh_theme = ( isset( $_COOKIE['mobilekh-theme'] ) && 'dark' === $_COOKIE['mobilekh-theme'] ) ? 'dark' : 'light'; ?>
<html <?php language_attributes(); ?> data-theme="<?php echo esc_attr( $mobilekh_theme ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script data-no-optimize="1">try{var t=localStorage.getItem('mobilekh-theme');if(t)document.documentElement.dataset.theme=t;}catch(e){}</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if ( get_theme_mod( 'mobilekh_topbar_on', false ) && get_theme_mod( 'mobilekh_topbar_text', '' ) !== '' ) : ?>
<div class="topbar" role="note"><?php echo esc_html( get_theme_mod( 'mobilekh_topbar_text' ) ); ?></div>
<?php endif; ?>

<header class="site-header">
	<div class="container header-inner">
		<button class="icon-btn nav-toggle" aria-label="<?php esc_attr_e( 'منو', 'mobilekh' ); ?>" aria-expanded="false">
			<span></span><span></span><span></span>
		</button>
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<span class="brand-logo logo-light"><?php mobilekh_logo( 'light' ); ?></span>
			<span class="brand-logo logo-dark"><?php mobilekh_logo( 'dark' ); ?></span>
		</a>
		<form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو در فروشگاه…', 'mobilekh' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
			<input type="hidden" name="post_type" value="product">
			<button type="submit" aria-label="<?php esc_attr_e( 'جستجو', 'mobilekh' ); ?>"><?php mobilekh_icon( 'search' ); ?></button>
			<div class="search-results" hidden></div>
		</form>
		<div class="header-actions">
			<?php $phone = get_theme_mod( 'mobilekh_phone', '' ); ?>
			<?php if ( '' !== $phone ) : ?>
				<a class="header-phone" href="tel:<?php echo esc_attr( preg_replace( '/[^+\d]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a>
			<?php endif; ?>
			<button class="icon-btn theme-toggle" aria-label="<?php esc_attr_e( 'تغییر تم', 'mobilekh' ); ?>"><span class="ico-sun"><?php mobilekh_icon( 'sun' ); ?></span><span class="ico-moon"><?php mobilekh_icon( 'moon' ); ?></span></button>
			<?php if ( is_user_logged_in() ) : ?>
				<a class="icon-btn" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'mobilekh' ); ?>"><?php mobilekh_icon( 'user' ); ?></a>
			<?php else : ?>
				<?php $login_url = function_exists( 'mobilekh_account_url' ) ? mobilekh_account_url() : home_url( '/' ); ?>
				<a class="btn btn-sm login-btn" href="<?php echo esc_url( $login_url ); ?>"><?php mobilekh_icon( 'user' ); ?><?php esc_html_e( 'ورود', 'mobilekh' ); ?></a>
			<?php endif; ?>
			<?php if ( function_exists( 'WC' ) ) : ?>
			<a class="icon-btn cart-btn" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'mobilekh' ); ?>">
				<?php mobilekh_icon( 'cart' ); ?><span class="cart-count"><?php echo esc_html( mobilekh_cart_count() ); ?></span>
			</a>
			<?php endif; ?>
		</div>
	</div>
	<nav class="under-header" aria-label="<?php esc_attr_e( 'ناوبری اصلی', 'mobilekh' ); ?>">
		<div class="container under-header-inner">
			<?php $mega = mobilekh_mega_cats(); ?>
			<?php if ( ! empty( $mega ) ) : ?>
			<div class="mega-wrap">
				<button class="btn btn-shop mega-toggle" aria-expanded="false"><?php mobilekh_icon( 'menu' ); ?><?php esc_html_e( 'فروشگاه', 'mobilekh' ); ?></button>
				<div class="mega-panel" hidden>
					<?php foreach ( $mega as $pillar ) : ?>
						<?php $plink = get_term_link( $pillar['term'] ); ?>
						<div class="mega-col">
							<a class="mega-head" href="<?php echo esc_url( is_wp_error( $plink ) ? '#' : $plink ); ?>"><?php echo esc_html( $pillar['term']->name ); ?></a>
							<ul>
								<?php foreach ( $pillar['children'] as $child ) : ?>
									<?php $clink = get_term_link( $child ); ?>
									<li><a href="<?php echo esc_url( is_wp_error( $clink ) ? '#' : $clink ); ?>"><?php echo esc_html( $child->name ); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'under-header',
					'container'      => false,
					'menu_class'     => 'menu-inline',
					'fallback_cb'    => false,
				)
			);
			wp_nav_menu(
				array(
					'theme_location' => 'header',
					'container'      => false,
					'menu_class'     => 'menu-inline menu-main',
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</nav>
	<nav class="mobile-drawer" aria-label="<?php esc_attr_e( 'منوی موبایل', 'mobilekh' ); ?>" hidden>
		<form class="drawer-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'جستجو…', 'mobilekh' ); ?>" autocomplete="off">
			<input type="hidden" name="post_type" value="product">
			<button type="submit" aria-label="<?php esc_attr_e( 'جستجو', 'mobilekh' ); ?>"><?php mobilekh_icon( 'search' ); ?></button>
			<div class="search-results" hidden></div>
		</form>
		<?php
		wp_nav_menu( array( 'theme_location' => 'header', 'container' => false, 'menu_class' => 'menu-stack', 'fallback_cb' => false ) );
		$drawer_cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'hide_empty' => true,
				'number'     => 8,
			)
		);
		?>
		<?php if ( ! is_wp_error( $drawer_cats ) && ! empty( $drawer_cats ) ) : ?>
		<ul class="menu-stack">
			<?php foreach ( $drawer_cats as $dc ) : ?>
				<?php $dlink = get_term_link( $dc ); ?>
				<li><a href="<?php echo esc_url( is_wp_error( $dlink ) ? '#' : $dlink ); ?>"><?php echo esc_html( $dc->name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
	</nav>
</header>

<main id="content" class="site-main">
