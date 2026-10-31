<?php
/**
 * Uninstall: remove plugin options only.
 * Converted WebP images stay valid and in use; originals kept by core are untouched.
 *
 * @package mobilekh-webp
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'mobilekh_webp_enabled' );
delete_option( 'mobilekh_webp_quality' );
delete_option( 'mobilekh_webp_bulk' );
