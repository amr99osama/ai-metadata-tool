<?php
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'tahaluf_site_logo' ) ) {
	function tahaluf_site_logo() {
		$id = (int) get_theme_mod( 'tahaluf_logo_id', 0 );
		if ( ! $id ) {
			the_custom_logo();
			return;
		}
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( ! $src ) {
			return;
		}
		printf(
			'<a href="%1$s" class="tahaluf-logo" rel="home"><img src="%2$s" alt="%3$s" /></a>',
			esc_url( home_url( '/' ) ),
			esc_url( $src[0] ),
			esc_attr( get_bloginfo( 'name' ) )
		);
	}
}
