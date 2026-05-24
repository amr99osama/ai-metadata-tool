<?php
defined( 'ABSPATH' ) || exit;

final class Community_AI_Plugin {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->load_dependencies();
			self::$instance->boot_modules();
		}
		return self::$instance;
	}

	private function load_dependencies() {
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-security.php';
		require_once COMMUNITY_AI_DIR . 'includes/interface-community-ai-service.php';
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-mock-service.php';
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-cpt.php';
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-settings.php';
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-metabox.php';
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-ajax.php';
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-frontend.php';
	}

	private function boot_modules() {
		Community_AI_CPT::register();
		Community_AI_Settings::register();
		Community_AI_MetaBox::register();
		Community_AI_Ajax::register( new Community_AI_Mock_Service() );
		Community_AI_Frontend::register();
	}

	public static function activate() {
		require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-cpt.php';
		Community_AI_CPT::register_post_type();
		flush_rewrite_rules();

		if ( false === get_option( 'community_ai_settings' ) ) {
			add_option( 'community_ai_settings', array(
				'min_length' => 40,
				'max_length' => 160,
			) );
		}
	}

	public static function deactivate() {
		flush_rewrite_rules();
	}
}
