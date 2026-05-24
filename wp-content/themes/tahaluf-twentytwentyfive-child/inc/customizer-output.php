<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', function() {

	$accent = get_theme_mod( 'tahaluf_accent_color', '#2271b1' );
	if ( ! preg_match( '/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', (string) $accent ) ) {
		$accent = '#2271b1';
	}

	$logo_w = absint( get_theme_mod( 'tahaluf_logo_max_width',  120 ) );
	$logo_h = absint( get_theme_mod( 'tahaluf_logo_max_height', 60 ) );
	$cmw    = absint( get_theme_mod( 'tahaluf_content_max_width', 1200 ) );
	$bfs    = absint( get_theme_mod( 'tahaluf_body_font_size',    16 ) );
	$h_bg   = sanitize_hex_color( get_theme_mod( 'tahaluf_header_bg', '' ) );
	$h_text = sanitize_hex_color( get_theme_mod( 'tahaluf_header_text', '' ) );

	$css  = ':root{';
	$css .= '--tahaluf-accent:' . $accent . ';';
	$css .= '--community-ai-accent:' . $accent . ';';
	$css .= '--tahaluf-logo-max-width:' . $logo_w . 'px;';
	$css .= '--tahaluf-logo-max-height:' . $logo_h . 'px;';
	$css .= '--tahaluf-content-max-width:' . $cmw . 'px;';
	$css .= '}';

	$css .= 'body{font-size:' . $bfs . 'px;}';

	$css .= '.tahaluf-site-header .wp-block-site-logo img{max-width:var(--tahaluf-logo-max-width);max-height:var(--tahaluf-logo-max-height);width:auto;height:auto;}';

	if ( $h_bg ) {
		$css .= '.tahaluf-site-header{background-color:' . $h_bg . ';}';
	}
	if ( $h_text ) {
		$css .= '.tahaluf-site-header,.tahaluf-site-header a{color:' . $h_text . ';}';
	}

	$css .= 'body.tahaluf-sticky-header .tahaluf-site-header{position:sticky;top:0;z-index:100;backdrop-filter:saturate(120%) blur(8px);}';
	$css .= 'body.tahaluf-hide-site-title .tahaluf-site-header .wp-block-site-title{display:none;}';
	$css .= '.tahaluf-brand{align-items:center;}';

	if ( $cmw ) {
		// Soft cap; theme.json layouts win where they apply.
		$css .= '.wp-block-group.alignwide{max-width:min(100%,' . $cmw . 'px);}';
	}

	printf( '<style id="tahaluf-vars">%s</style>', wp_strip_all_tags( $css ) );

}, 20 );

add_action( 'wp_footer', function() {
	$text  = (string) get_theme_mod( 'tahaluf_footer_text', '' );
	$badge = (bool) get_theme_mod( 'tahaluf_show_ai_badge', true );
	if ( '' === $text && ! $badge ) {
		return;
	}

	echo '<div class="tahaluf-footer" role="contentinfo">';
	if ( '' !== $text ) {
		echo '<p class="tahaluf-footer__text">' . wp_kses_post( $text ) . '</p>';
	}
	
	echo '</div>';
} );
