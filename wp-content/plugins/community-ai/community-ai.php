<?php
/**
 * Plugin Name:       Community AI
 * Plugin URI:        https://github.com/amr99osama/ai-metadata-tool
 * Description:       Adds a Community Discussions CPT with a mock-AI summarization workflow, hardened with WordPress security primitives.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Amr Osama
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       community-ai
 */

defined( 'ABSPATH' ) || exit;

define( 'COMMUNITY_AI_VERSION', '1.0.0' );
define( 'COMMUNITY_AI_FILE',    __FILE__ );
define( 'COMMUNITY_AI_DIR',     plugin_dir_path( __FILE__ ) );
define( 'COMMUNITY_AI_URL',     plugin_dir_url( __FILE__ ) );

require_once COMMUNITY_AI_DIR . 'includes/class-community-ai-plugin.php';

add_action( 'plugins_loaded', array( 'Community_AI_Plugin', 'instance' ) );

register_activation_hook(   __FILE__, array( 'Community_AI_Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Community_AI_Plugin', 'deactivate' ) );
