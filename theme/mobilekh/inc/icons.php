<?php
/**
 * Inline SVG icon set (no icon font, no extra requests).
 *
 * @package mobilekh
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print an inline SVG icon.
 *
 * @param string $name Icon name.
 * @param string $class Extra class.
 */
function mobilekh_icon( $name, $class = '' ) {
	$icons = array(
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>',
		'cart'     => '<path d="M3 4h2l2.4 12.2A1.5 1.5 0 0 0 8.9 17.5H18a1.5 1.5 0 0 0 1.5-1.2L21.5 8H6"/><circle cx="9.5" cy="20.5" r="1.3"/><circle cx="17" cy="20.5" r="1.3"/>',
		'sun'      => '<circle cx="12" cy="12" r="4.5"/><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"/>',
		'moon'     => '<path d="M20 14.5A8 8 0 0 1 9.5 4 8 8 0 1 0 20 14.5Z"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'    => '<path d="m6 6 12 12M18 6 6 18"/>',
		'home'     => '<path d="m4 11 8-7 8 7v9a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1Z"/>',
		'shop'     => '<path d="M4 7 5.5 3h13L20 7M4 7h16M4 7v13a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1V7M10 11a2 2 0 0 0 4 0"/>',
		'phone'    => '<path d="M7 3h4l2 5-2.5 1.5a12 12 0 0 0 5 5L17 12l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 5 5a2 2 0 0 1 2-2Z"/>',
		'chev'     => '<path d="m9 6 6 6-6 6"/>',
		'shield'   => '<path d="M12 3 5 6v5c0 5 3 8.5 7 10 4-1.5 7-5 7-10V6Z"/><path d="m9.5 12 2 2 3.5-4"/>',
		'truck'    => '<path d="M2 6h12v10H2zM14 10h4l4 4v2h-8Z"/><circle cx="6.5" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/>',
		'support'  => '<path d="M4 13a8 8 0 0 1 16 0"/><rect x="3" y="13" width="4" height="7" rx="1.5"/><rect x="17" y="13" width="4" height="7" rx="1.5"/><path d="M19 20a4 4 0 0 1-4 2h-2"/>',
		'wallet'   => '<rect x="3" y="6" width="18" height="14" rx="2.5"/><path d="M3 10h18M16 15h2"/>',
		'arrow'    => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'telegram' => '<path d="M21 4 3 11.2l6.3 2.3L11.5 20l3.9-4.6 4.8 3.5Z"/><path d="m9.3 13.5 9.3-8.2"/>',
		'whatsapp' => '<path d="M12 3a9 9 0 0 0-7.8 13.5L3 21l4.6-1.2A9 9 0 1 0 12 3Z"/><path d="M9 9.5c.5 2.5 3 5 5.5 5.5l1-1.5 2 1c-.5 1.5-1.5 2-3 1.5-3-1-6-4-7-7-.5-1.5 0-2.5 1.5-3l1 2Z"/>',
		'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.2" fill="currentColor" stroke="none"/>',
		'pin'      => '<path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11Z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/>',
		'heart'    => '<path d="M12 20.5C7 16.5 3.5 13.3 3.5 9.6 3.5 7 5.5 5 8 5c1.6 0 3.1.8 4 2.1C12.9 5.8 14.4 5 16 5c2.5 0 4.5 2 4.5 4.6 0 3.7-3.5 6.9-8.5 10.9Z"/>',
		'check'    => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
	);
	if ( ! isset( $icons[ $name ] ) ) {
		return;
	}
	printf(
		'<svg class="ico ico-%1$s %2$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%3$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		$icons[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — static icon paths.
	);
}
