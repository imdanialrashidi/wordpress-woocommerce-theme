<?php
/**
 * Frontend/runtime behaviour: WebP output, quality, responsive sizes.
 *
 * @package mobilekh-webp
 */

defined( 'ABSPATH' ) || exit;

class MobileKH_Webp_Core {

	/**
	 * Register hooks.
	 */
	public function init() {
		add_filter( 'image_editor_output_format', array( $this, 'output_format' ) );
		add_filter( 'wp_editor_set_quality', array( $this, 'webp_quality' ), 10, 2 );
		add_action( 'init', array( $this, 'register_sizes' ) );
	}

	/**
	 * Whether the active image editor can write WebP.
	 *
	 * @return bool
	 */
	public function webp_supported() {
		if ( ! function_exists( 'wp_image_editor_supports' ) ) {
			return false;
		}
		return (bool) wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
	}

	/**
	 * Map JPEG/PNG uploads to WebP (WP 6.5+ API). Originals are kept by core as backup.
	 *
	 * @param array $formats Mime map.
	 * @return array
	 */
	public function output_format( $formats ) {
		if ( ! $this->webp_supported() ) {
			return $formats;
		}
		if ( ! (bool) get_option( 'mobilekh_webp_enabled', 1 ) ) {
			return $formats;
		}
		if ( ! is_array( $formats ) ) {
			$formats = array();
		}
		$formats['image/jpeg'] = 'image/webp';
		$formats['image/png']  = 'image/webp';
		return $formats;
	}

	/**
	 * WebP quality from settings (default 82). Other mimes untouched.
	 *
	 * @param int    $quality   Default quality.
	 * @param string $mime_type Target mime.
	 * @return int
	 */
	public function webp_quality( $quality, $mime_type ) {
		if ( 'image/webp' !== $mime_type ) {
			return $quality;
		}
		$q = absint( get_option( 'mobilekh_webp_quality', 82 ) );
		if ( $q < 1 || $q > 100 ) {
			$q = 82;
		}
		return $q;
	}

	/**
	 * Theme-aware responsive sizes so browsers pick per-display files via srcset.
	 * WordPress emits srcset/sizes natively for these on new uploads.
	 */
	public function register_sizes() {
		$sizes = apply_filters(
			'mobilekh_webp_sizes',
			array(
				'mobilekh-card' => array( 640, 640, false ),
				'mobilekh-wide' => array( 1280, 720, false ),
			)
		);
		if ( ! is_array( $sizes ) ) {
			return;
		}
		foreach ( $sizes as $name => $args ) {
			if ( ! is_string( $name ) || ! is_array( $args ) || count( $args ) < 2 ) {
				continue;
			}
			add_image_size( $name, absint( $args[0] ), absint( $args[1] ), ! empty( $args[2] ) );
		}
	}
}
