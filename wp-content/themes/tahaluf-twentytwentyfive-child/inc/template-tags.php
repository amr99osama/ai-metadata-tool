<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'tahaluf_site_logo' ) ) {
	/**
	 * Render the site logo using core's custom_logo (Customize → Site Identity → Logo).
	 *
	 * Available as a classic-PHP fallback. The block-theme header (parts/header.html)
	 * uses the Site Logo block directly and does not call this function.
	 */
	function tahaluf_site_logo() {
		if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
			the_custom_logo();
		}
	}
}
