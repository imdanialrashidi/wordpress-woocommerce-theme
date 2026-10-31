<?php
/**
 * Plugin Name:       بهینه‌ساز تصویر موبایل خرسندی
 * Plugin URI:        https://mobile-khorsandi.local/
 * Description:       تبدیل خودکار JPEG و PNG به WebP هنگام آپلود، سایزهای واکنش‌گرا برای نمایشگرهای مختلف و ابزار تبدیل گروهی رسانه‌های قدیمی.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.4
 * Author:            موبایل خرسندی
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       mobilekh-webp
 *
 * @package mobilekh-webp
 */

defined( 'ABSPATH' ) || exit;

define( 'MOBILEKH_WEBP_VERSION', '1.0.0' );
define( 'MOBILEKH_WEBP_FILE', __FILE__ );
define( 'MOBILEKH_WEBP_DIR', plugin_dir_path( __FILE__ ) );

require_once MOBILEKH_WEBP_DIR . 'includes/class-mobilekh-webp-core.php';

$core = new MobileKH_Webp_Core();
$core->init();

if ( is_admin() ) {
	require_once MOBILEKH_WEBP_DIR . 'includes/class-mobilekh-webp-admin.php';
	$admin = new MobileKH_Webp_Admin();
	$admin->init();
}

/**
 * Activation: sane defaults, nothing destructive.
 */
function mobilekh_webp_activate() {
	add_option( 'mobilekh_webp_enabled', 1 );
	add_option( 'mobilekh_webp_quality', 82 );
}
register_activation_hook( __FILE__, 'mobilekh_webp_activate' );

/**
 * Deactivation: drop transient bulk progress only. Converted images stay valid.
 */
function mobilekh_webp_deactivate() {
	delete_option( 'mobilekh_webp_bulk' );
}
register_deactivation_hook( __FILE__, 'mobilekh_webp_deactivate' );
