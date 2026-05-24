<?php
defined( 'ABSPATH' ) || exit;

final class Community_AI_Frontend {

	public static function register() {
		add_filter( 'the_content',        array( __CLASS__, 'prepend_summary' ), 5 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	public static function prepend_summary( $content ) {

		if ( ! is_singular( Community_AI_CPT::POST_TYPE ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}

		$summary = (string) get_post_meta( get_the_ID(), Community_AI_CPT::META_KEY_SUMMARY, true );
		if ( '' === $summary ) {
			return $content;
		}

		$html  = '<aside class="community-ai-summary" aria-label="' . esc_attr__( 'AI-generated summary', 'community-ai' ) . '">';
		$html .= '<h2 class="community-ai-summary__title">' . esc_html__( 'Quick summary', 'community-ai' ) . '</h2>';
		$html .= '<p class="community-ai-summary__body">' . esc_html( $summary ) . '</p>';
		$html .= '</aside>';

		return $html . $content;
	}

	public static function enqueue() {
		if ( is_singular( Community_AI_CPT::POST_TYPE ) ) {
			wp_enqueue_style(
				'community-ai-frontend',
				COMMUNITY_AI_URL . 'assets/frontend.css',
				array(),
				COMMUNITY_AI_VERSION
			);
		}
	}
}
