<?php
/**
 * Admin-only code: Settings API page + batched bulk converter.
 *
 * @package mobilekh-webp
 */

defined( 'ABSPATH' ) || exit;

class MobileKH_Webp_Admin {

	const BATCH = 10;

	/**
	 * Register hooks.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_action( 'admin_notices', array( $this, 'support_notice' ) );
		add_action( 'wp_ajax_mobilekh_webp_bulk', array( $this, 'ajax_bulk' ) );
	}

	/**
	 * Page under Media.
	 */
	public function menu() {
		add_media_page(
			__( 'بهینه‌ساز تصویر', 'mobilekh-webp' ),
			__( 'بهینه‌ساز تصویر', 'mobilekh-webp' ),
			'manage_options',
			'mobilekh-webp',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Settings API wiring.
	 */
	public function settings() {
		register_setting(
			'mobilekh_webp',
			'mobilekh_webp_enabled',
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize_bool' ),
				'default'           => 1,
			)
		);
		register_setting(
			'mobilekh_webp',
			'mobilekh_webp_quality',
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize_quality' ),
				'default'           => 82,
			)
		);

		add_settings_section(
			'mobilekh_webp_main',
			__( 'تبدیل هنگام آپلود', 'mobilekh-webp' ),
			array( $this, 'section_text' ),
			'mobilekh-webp'
		);
		add_settings_field(
			'mobilekh_webp_enabled',
			__( 'تبدیل JPEG/PNG به WebP', 'mobilekh-webp' ),
			array( $this, 'field_enabled' ),
			'mobilekh-webp',
			'mobilekh_webp_main'
		);
		add_settings_field(
			'mobilekh_webp_quality',
			__( 'کیفیت WebP (۱ تا ۱۰۰)', 'mobilekh-webp' ),
			array( $this, 'field_quality' ),
			'mobilekh-webp',
			'mobilekh_webp_main'
		);
	}

	/**
	 * @param mixed $v Raw value.
	 * @return int
	 */
	public function sanitize_bool( $v ) {
		return $v ? 1 : 0;
	}

	/**
	 * @param mixed $v Raw value.
	 * @return int
	 */
	public function sanitize_quality( $v ) {
		$q = absint( $v );
		if ( $q < 1 || $q > 100 ) {
			$q = 82;
		}
		return $q;
	}

	public function section_text() {
		echo '<p>' . esc_html__( 'فایل‌های جدید با همین تنظیمات ذخیره می‌شوند؛ نسخه اصلی توسط وردپرس نگه داشته می‌شود. سایزهای واکنش‌گرا (srcset) به‌صورت خودکار برای نمایشگرهای مختلف انتخاب می‌شوند.', 'mobilekh-webp' ) . '</p>';
	}

	public function field_enabled() {
		$v = (bool) get_option( 'mobilekh_webp_enabled', 1 );
		echo '<label><input type="checkbox" name="mobilekh_webp_enabled" value="1"' . checked( 1, $v, false ) . '> ';
		esc_html_e( 'فعال', 'mobilekh-webp' );
		echo '</label>';
	}

	public function field_quality() {
		$v = absint( get_option( 'mobilekh_webp_quality', 82 ) );
		echo '<input type="number" name="mobilekh_webp_quality" value="' . esc_attr( $v ) . '" min="1" max="100" step="1" class="small-text">';
	}

	/**
	 * Warn when the server cannot write WebP.
	 */
	public function support_notice() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'media_page_mobilekh-webp' !== $screen->id ) {
			return;
		}
		require_once MOBILEKH_WEBP_DIR . 'includes/class-mobilekh-webp-core.php';
		$core = new MobileKH_Webp_Core();
		if ( $core->webp_supported() ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>';
		esc_html_e( 'هشدار: ویرایشگر تصویر سرور (GD/Imagick) از WebP پشتیبانی نمی‌کند. تبدیل تا نصب پشتیبانی انجام نمی‌شود.', 'mobilekh-webp' );
		echo '</p></div>';
	}

	/**
	 * Settings page + bulk tool.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'mobilekh-webp' ) );
		}
		$nonce = wp_create_nonce( 'mobilekh-webp-bulk' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'بهینه‌ساز تصویر', 'mobilekh-webp' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'mobilekh_webp' );
				do_settings_sections( 'mobilekh-webp' );
				submit_button();
				?>
			</form>
			<hr>
			<h2><?php esc_html_e( 'تبدیل گروهی رسانه‌های قدیمی', 'mobilekh-webp' ); ?></h2>
			<p class="description"><?php esc_html_e( 'تصاویر JPEG/PNG موجود کتابخانه را بچ‌بچ به WebP تبدیل می‌کند. عملیات قابل توقف و ادامه است.', 'mobilekh-webp' ); ?></p>
			<p>
				<button type="button" class="button button-primary" id="mkh-webp-start"><?php esc_html_e( 'شروع تبدیل گروهی', 'mobilekh-webp' ); ?></button>
				<span id="mkh-webp-status" style="margin-inline-start:10px"></span>
			</p>
			<script>
			(function () {
				var btn = document.getElementById('mkh-webp-start');
				var status = document.getElementById('mkh-webp-status');
				var running = false;
				function step(offset) {
					if (!running) return;
					var data = new FormData();
					data.append('action', 'mobilekh_webp_bulk');
					data.append('_ajax_nonce', '<?php echo esc_js( $nonce ); ?>');
					data.append('offset', offset);
					fetch(ajaxurl, { method: 'POST', body: data, credentials: 'same-origin' })
						.then(function (r) { return r.json(); })
						.then(function (res) {
							if (!res || !res.success) {
								running = false;
								status.textContent = '<?php echo esc_js( __( 'خطا در تبدیل.', 'mobilekh-webp' ) ); ?>';
								return;
							}
							status.textContent = res.data.done + ' / ' + res.data.total;
							if (res.data.finished) {
								running = false;
								status.textContent += ' — <?php echo esc_js( __( 'تمام شد.', 'mobilekh-webp' ) ); ?>';
								return;
							}
							step(res.data.offset);
						})
						.catch(function () {
							running = false;
							status.textContent = '<?php echo esc_js( __( 'خطای ارتباط.', 'mobilekh-webp' ) ); ?>';
						});
				}
				btn.addEventListener('click', function () {
					if (running) { running = false; return; }
					running = true;
					status.textContent = '…';
					step(0);
				});
			})();
			</script>
		</div>
		<?php
	}

	/**
	 * One AJAX batch: regenerate metadata (WebP subsizes) for old JPEG/PNG items.
	 */
	public function ajax_bulk() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'forbidden' ), 403 );
		}
		check_ajax_referer( 'mobilekh-webp-bulk' );

		$offset = isset( $_POST['offset'] ) ? absint( wp_unslash( $_POST['offset'] ) ) : 0;

		$count_q = new WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => array( 'image/jpeg', 'image/png' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$total = is_array( $count_q->posts ) ? count( $count_q->posts ) : 0;

		$q = new WP_Query(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'post_mime_type' => array( 'image/jpeg', 'image/png' ),
				'posts_per_page' => self::BATCH,
				'offset'         => $offset,
				'fields'         => 'ids',
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'no_found_rows'  => true,
			)
		);

		$done = 0;
		if ( is_array( $q->posts ) ) {
			foreach ( $q->posts as $att_id ) {
				$file = get_attached_file( $att_id );
				if ( $file && file_exists( $file ) ) {
					$meta = wp_generate_attachment_metadata( $att_id, $file );
					if ( ! is_wp_error( $meta ) && is_array( $meta ) ) {
						wp_update_attachment_metadata( $att_id, $meta );
					}
				}
				$done++;
			}
		}

		$next     = $offset + $done;
		$finished = ( 0 === $done ) || ( $next >= $total );
		update_option( 'mobilekh_webp_bulk', array( 'offset' => $next, 'total' => $total ), false );

		wp_send_json_success(
			array(
				'done'     => $next,
				'total'    => $total,
				'offset'   => $next,
				'finished' => $finished,
			)
		);
	}
}
