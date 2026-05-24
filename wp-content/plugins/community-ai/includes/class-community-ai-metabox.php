<?php
defined( 'ABSPATH' ) || exit;

final class Community_AI_MetaBox {

	const NONCE_ACTION = 'community_ai_metabox_save';
	const NONCE_FIELD  = 'community_ai_metabox_nonce';

	public static function register() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add' ) );
		add_action( 'save_post_' . Community_AI_CPT::POST_TYPE, array( __CLASS__, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue' ) );
	}

	public static function add() {
		add_meta_box(
			'community-ai-summary',
			__( 'AI Summary', 'community-ai' ),
			array( __CLASS__, 'render' ),
			Community_AI_CPT::POST_TYPE,
			'side',
			'high'
		);
	}

	public static function render( $post ) {
		$summary   = (string) get_post_meta( $post->ID, Community_AI_CPT::META_KEY_SUMMARY, true );
		$generated = (int) get_post_meta( $post->ID, Community_AI_CPT::META_KEY_SUMMARY_TS, true );
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		?>
		<p>
			<button type="button" class="button button-primary" id="community-ai-generate-summary"
				data-post-id="<?php echo esc_attr( $post->ID ); ?>">
				<?php esc_html_e( 'Generate AI Summary', 'community-ai' ); ?>
			</button>
			<span class="spinner" id="community-ai-spinner" style="float:none;margin-left:6px;"></span>
		</p>
		<p>
			<label for="community_ai_summary"><strong><?php esc_html_e( 'Summary text', 'community-ai' ); ?></strong></label>
			<textarea id="community_ai_summary" name="community_ai_summary" rows="5" class="widefat"
				maxlength="2000"><?php echo esc_textarea( $summary ); ?></textarea>
		</p>
		<p class="description" id="community-ai-generated-at">
			<?php
			if ( $generated ) {
				printf(
					/* translators: %s: human-readable time diff */
					esc_html__( 'Generated %s ago.', 'community-ai' ),
					esc_html( human_time_diff( $generated, time() ) )
				);
			} else {
				esc_html_e( 'No summary generated yet.', 'community-ai' );
			}
			?>
		</p>
		<div id="community-ai-error" class="notice notice-error inline" style="display:none;"></div>
		<?php
	}

	public static function save( $post_id, $post ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! Community_AI_Security::verify_post_nonce( self::NONCE_ACTION, self::NONCE_FIELD ) ) {
			return;
		}

		if ( ! Community_AI_Security::user_can_edit_post( $post_id ) ) {
			return;
		}

		$raw   = isset( $_POST['community_ai_summary'] ) ? $_POST['community_ai_summary'] : '';
		$clean = Community_AI_Security::sanitize_plain_text( $raw );

		if ( '' === $clean ) {
			delete_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY );
			delete_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY_TS );
		} else {
			update_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY, $clean );
			if ( ! get_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY_TS, true ) ) {
				update_post_meta( $post_id, Community_AI_CPT::META_KEY_SUMMARY_TS, time() );
			}
		}
	}

	public static function enqueue( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || Community_AI_CPT::POST_TYPE !== $screen->post_type ) {
			return;
		}

		wp_enqueue_style(
			'community-ai-admin',
			COMMUNITY_AI_URL . 'assets/admin.css',
			array(),
			COMMUNITY_AI_VERSION
		);
		wp_enqueue_script(
			'community-ai-admin',
			COMMUNITY_AI_URL . 'assets/admin.js',
			array( 'jquery' ),
			COMMUNITY_AI_VERSION,
			true
		);
		wp_localize_script(
			'community-ai-admin',
			'CommunityAI',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'community_ai_generate' ),
				'i18n'     => array(
					'generic_error'  => __( 'Something went wrong. Please try again.', 'community-ai' ),
					'just_generated' => __( 'Generated just now.', 'community-ai' ),
				),
			)
		);
	}
}
