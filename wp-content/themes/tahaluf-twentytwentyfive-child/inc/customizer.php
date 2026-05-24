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
		'title' => __( 'Branding', 'tahaluf-tt5-child' ),
		'panel' => 'tahaluf_panel',
	) );

	$wp_customize->add_setting( 'tahaluf_logo_id', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
		'capability'        => 'edit_theme_options',
	) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control( $wp_customize, 'tahaluf_logo_id', array(
			'label'     => __( 'Site logo', 'tahaluf-tt5-child' ),
			'section'   => 'tahaluf_branding',
			'mime_type' => 'image',
		) )
	);

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
