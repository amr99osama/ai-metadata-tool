<?php
defined( 'ABSPATH' ) || exit;

final class Community_AI_Ajax {

	/** @var Community_AI_Service_Interface */
	private static $service;

	public static function register( Community_AI_Service_Interface $service ) {
		self::$service = $service;
		add_action( 'wp_ajax_community_ai_generate_summary', array( __CLASS__, 'handle_generate' ) );
		// Note: no wp_ajax_nopriv_* — only logged-in editors can call this.
	}

	public static function handle_generate() {

		// 1) Nonce.
		Community_AI_Security::require_ajax_nonce( 'community_ai_generate', 'nonce' );

		// 2) Input.
		$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( array( 'message' => __( 'Missing post ID.', 'community-ai' ) ), 400 );
		}

		// 3) Capability.
		if ( ! Community_AI_Security::user_can_edit_post( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', 'community-ai' ) ), 403 );
		}

		// 4) Resource exists + correct CPT.
		$post = get_post( $post_id );
		if ( ! $post || Community_AI_CPT::POST_TYPE !== $post->post_type ) {
			wp_send_json_error( array( 'message' => __( 'Discussion not found.', 'community-ai' ) ), 404 );
		}

		// 5) Read content (sanitize before passing to AI service).
		$content = Community_AI_Security::sanitize_rich_text( $post->post_content );
		if ( '' === trim( wp_strip_all_tags( $content ) ) ) {
			wp_send_json_error( array( 'message' => __( 'Post has no content to summarize.', 'community-ai' ) ), 422 );
		}

		// 6) Generate.
		$settings = Community_AI_Settings::get_settings();
		$result   = self::$service->summarize( $content, $settings['min_length'], $settings['max_length'] );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ), 500 );
		}

		// 7) Persist.
		$clean = Community_AI_Security::sanitize_plain_text( $result );
		update_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY,    $clean );
		update_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY_TS, time() );

		wp_send_json_success( array(
			'summary'      => $clean,
			'generated_at' => time(),
		) );
	}
}
