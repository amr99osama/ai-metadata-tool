<?php
defined( 'ABSPATH' ) || exit;

final class Community_AI_Settings {

	const OPTION_NAME = 'community_ai_settings';
	const PAGE_SLUG   = 'community-ai';
	const GROUP       = 'community_ai_settings_group';

	public static function register() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
	}

	public static function get_settings() {
		$defaults = array(
			'min_length' => 40,
			'max_length' => 160,
		);
		$saved = get_option( self::OPTION_NAME, array() );
		return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
	}

	public static function register_settings() {
		register_setting( self::GROUP, self::OPTION_NAME, array(
			'type'              => 'array',
			'sanitize_callback' => array( __CLASS__, 'sanitize' ),
			'default'           => array(
				'min_length' => 40,
				'max_length' => 160,
			),
		) );

		add_settings_section(
			'community_ai_main',
			__( 'Summary Length', 'community-ai' ),
			function() {
				echo '<p>' . esc_html__( 'Choose how long generated summaries should be. Editors can still adjust the text before publishing.', 'community-ai' ) . '</p>';
			},
			self::PAGE_SLUG
		);

		add_settings_field(
			'min_length',
			__( 'Minimum length', 'community-ai' ),
			array( __CLASS__, 'render_min_field' ),
			self::PAGE_SLUG,
			'community_ai_main'
		);
		add_settings_field(
			'max_length',
			__( 'Maximum length', 'community-ai' ),
			array( __CLASS__, 'render_max_field' ),
			self::PAGE_SLUG,
			'community_ai_main'
		);
	}

	public static function sanitize( $input ) {
		$out = array();
		$out['min_length'] = Community_AI_Security::sanitize_summary_length(
			isset( $input['min_length'] ) ? $input['min_length'] : 40,
			10,
			500
		);
		$out['max_length'] = Community_AI_Security::sanitize_summary_length(
			isset( $input['max_length'] ) ? $input['max_length'] : 160,
			20,
			1000
		);

		if ( $out['min_length'] >= $out['max_length'] ) {
			$out['max_length'] = min( 1000, max( 160, $out['min_length'] + 20 ) );
			add_settings_error(
				self::OPTION_NAME,
				'community_ai_range',
				sprintf(
					/* translators: %d: adjusted maximum summary length */
					__( 'Maximum length must be greater than minimum length. We adjusted it to %d characters.', 'community-ai' ),
					$out['max_length']
				),
				'error'
			);
		}
		return $out;
	}

	public static function register_menu() {
		add_submenu_page(
			'edit.php?post_type=' . Community_AI_CPT::POST_TYPE,
			__( 'AI Settings', 'community-ai' ),
			__( 'AI Settings', 'community-ai' ),
			'manage_options',
			self::PAGE_SLUG,
			array( __CLASS__, 'render_page' )
		);
	}

	public static function render_page() {
		if ( ! Community_AI_Security::user_can_manage_options() ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'community-ai' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Community AI — Settings', 'community-ai' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( self::GROUP );
				do_settings_sections( self::PAGE_SLUG );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public static function render_min_field() {
		$s = self::get_settings();
		printf(
			'<input type="number" min="10" max="500" name="%1$s[min_length]" value="%2$s" /> <span class="description">%3$s</span>',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $s['min_length'] ),
			esc_html__( 'Characters. Range 10–500.', 'community-ai' )
		);
	}

	public static function render_max_field() {
		$s = self::get_settings();
		printf(
			'<input type="number" min="20" max="1000" name="%1$s[max_length]" value="%2$s" /> <span class="description">%3$s</span>',
			esc_attr( self::OPTION_NAME ),
			esc_attr( $s['max_length'] ),
			esc_html__( 'Characters. Range 20–1000.', 'community-ai' )
		);
	}
}
