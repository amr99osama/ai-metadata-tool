<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_head', function() {
	$accent = get_theme_mod( 'tahaluf_accent_color', '#2271b1' );
	if ( ! preg_match( '/^#([a-f0-9]{3}|[a-f0-9]{6})$/i', (string) $accent ) ) {
		$accent = '#2271b1';
	}
	printf(
		'<style id="tahaluf-vars">:root{--tahaluf-accent:%1$s;--community-ai-accent:%1$s;}</style>',
		esc_attr( $accent )
	);
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
	if ( $badge ) {
		echo '<p class="tahaluf-footer__badge">' . esc_html__( 'AI-assisted summaries powered by the Community AI plugin.', 'tahaluf-tt5-child' ) . '</p>';
	}
	echo '</div>';
} );
