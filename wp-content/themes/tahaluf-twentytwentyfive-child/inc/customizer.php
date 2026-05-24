<?php
defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', function( WP_Customize_Manager $wp_customize ) {

	$wp_customize->add_panel( 'tahaluf_panel', array(
		'title'       => __( 'Tahaluf Settings', 'tahaluf-tt5-child' ),
		'description' => __( 'Branding and theme options for the Tahaluf community site.', 'tahaluf-tt5-child' ),
		'priority'    => 30,
	) );

	/* ---------- Section: Branding ---------- */
	$wp_customize->add_section( 'tahaluf_branding', array(
		'title'       => __( 'Branding', 'tahaluf-tt5-child' ),
		'description' => __( 'Upload your site logo via Site Identity. Use the options below to tune how it appears in the header.', 'tahaluf-tt5-child' ),
		'panel'       => 'tahaluf_panel',
	) );

	$wp_customize->add_setting( 'tahaluf_accent_color', array(
		'default'           => '#2271b1',
		'sanitize_callback' => 'sanitize_hex_color',
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control(
		new WP_Customize_Color_Control( $wp_customize, 'tahaluf_accent_color', array(
			'label'   => __( 'Accent colour', 'tahaluf-tt5-child' ),
			'section' => 'tahaluf_branding',
		) )
	);

	$wp_customize->add_setting( 'tahaluf_logo_max_width', array(
		'default'           => 120,
		'sanitize_callback' => array( 'Tahaluf_Customizer', 'sanitize_int_range_40_800' ),
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_logo_max_width', array(
		'label'       => __( 'Logo max width (px)', 'tahaluf-tt5-child' ),
		'description' => __( 'Range 40–800.', 'tahaluf-tt5-child' ),
		'section'     => 'tahaluf_branding',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 40, 'max' => 800, 'step' => 5 ),
	) );

	$wp_customize->add_setting( 'tahaluf_logo_max_height', array(
		'default'           => 60,
		'sanitize_callback' => array( 'Tahaluf_Customizer', 'sanitize_int_range_20_400' ),
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_logo_max_height', array(
		'label'       => __( 'Logo max height (px)', 'tahaluf-tt5-child' ),
		'description' => __( 'Range 20–400.', 'tahaluf-tt5-child' ),
		'section'     => 'tahaluf_branding',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 20, 'max' => 400, 'step' => 5 ),
	) );

	/* ---------- Section: Header ---------- */
	$wp_customize->add_section( 'tahaluf_header', array(
		'title' => __( 'Header', 'tahaluf-tt5-child' ),
		'panel' => 'tahaluf_panel',
	) );

	$wp_customize->add_setting( 'tahaluf_header_bg', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control(
		new WP_Customize_Color_Control( $wp_customize, 'tahaluf_header_bg', array(
			'label'       => __( 'Header background', 'tahaluf-tt5-child' ),
			'description' => __( 'Leave empty for theme default.', 'tahaluf-tt5-child' ),
			'section'     => 'tahaluf_header',
		) )
	);

	$wp_customize->add_setting( 'tahaluf_header_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_hex_color',
		'capability'        => 'edit_theme_options',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control(
		new WP_Customize_Color_Control( $wp_customize, 'tahaluf_header_text', array(
			'label'       => __( 'Header text colour', 'tahaluf-tt5-child' ),
			'description' => __( 'Affects site title and navigation links inside the header.', 'tahaluf-tt5-child' ),
			'section'     => 'tahaluf_header',
		) )
	);

	$wp_customize->add_setting( 'tahaluf_sticky_header', array(
		'default'           => false,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_sticky_header', array(
		'label'   => __( 'Sticky header (stays visible while scrolling)', 'tahaluf-tt5-child' ),
		'section' => 'tahaluf_header',
		'type'    => 'checkbox',
	) );

	$wp_customize->add_setting( 'tahaluf_show_site_title', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_show_site_title', array(
		'label'       => __( 'Show site title alongside logo', 'tahaluf-tt5-child' ),
		'description' => __( 'Uncheck to display only the logo image.', 'tahaluf-tt5-child' ),
		'section'     => 'tahaluf_header',
		'type'        => 'checkbox',
	) );

	/* ---------- Section: Layout ---------- */
	$wp_customize->add_section( 'tahaluf_layout', array(
		'title' => __( 'Layout & Typography', 'tahaluf-tt5-child' ),
		'panel' => 'tahaluf_panel',
	) );

	$wp_customize->add_setting( 'tahaluf_content_max_width', array(
		'default'           => 1200,
		'sanitize_callback' => array( 'Tahaluf_Customizer', 'sanitize_int_range_800_2400' ),
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_content_max_width', array(
		'label'       => __( 'Content max width (px)', 'tahaluf-tt5-child' ),
		'description' => __( 'Range 800–2400.', 'tahaluf-tt5-child' ),
		'section'     => 'tahaluf_layout',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 800, 'max' => 2400, 'step' => 20 ),
	) );

	$wp_customize->add_setting( 'tahaluf_body_font_size', array(
		'default'           => 16,
		'sanitize_callback' => array( 'Tahaluf_Customizer', 'sanitize_int_range_12_24' ),
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_body_font_size', array(
		'label'       => __( 'Body font size (px)', 'tahaluf-tt5-child' ),
		'description' => __( 'Range 12–24.', 'tahaluf-tt5-child' ),
		'section'     => 'tahaluf_layout',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 12, 'max' => 24, 'step' => 1 ),
	) );

	/* ---------- Section: Footer ---------- */
	$wp_customize->add_section( 'tahaluf_footer', array(
		'title' => __( 'Footer', 'tahaluf-tt5-child' ),
		'panel' => 'tahaluf_panel',
	) );

	$wp_customize->add_setting( 'tahaluf_footer_text', array(
		'default'           => sprintf(
			/* translators: %s: current year */
			__( '© %s Tahaluf — Community-driven.', 'tahaluf-tt5-child' ),
			gmdate( 'Y' )
		),
		'sanitize_callback' => 'wp_kses_post',
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_footer_text', array(
		'label'   => __( 'Footer text', 'tahaluf-tt5-child' ),
		'section' => 'tahaluf_footer',
		'type'    => 'textarea',
	) );

	$wp_customize->add_setting( 'tahaluf_show_ai_badge', array(
		'default'           => true,
		'sanitize_callback' => 'rest_sanitize_boolean',
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control( 'tahaluf_show_ai_badge', array(
		'label'   => __( 'Show "AI-assisted" badge in footer', 'tahaluf-tt5-child' ),
		'section' => 'tahaluf_footer',
		'type'    => 'checkbox',
	) );
} );

/**
 * Range-clamp sanitizers reused across pixel-value settings.
 */
final class Tahaluf_Customizer {

	private static function clamp( $value, $min, $max, $default ) {
		$value = absint( $value );
		if ( $value < $min ) return $default;
		if ( $value > $max ) return $default;
		return $value;
	}

	public static function sanitize_int_range_40_800( $value ) {
		return self::clamp( $value, 40, 800, 120 );
	}
	public static function sanitize_int_range_20_400( $value ) {
		return self::clamp( $value, 20, 400, 60 );
	}
	public static function sanitize_int_range_800_2400( $value ) {
		return self::clamp( $value, 800, 2400, 1200 );
	}
	public static function sanitize_int_range_12_24( $value ) {
		return self::clamp( $value, 12, 24, 16 );
	}
}
