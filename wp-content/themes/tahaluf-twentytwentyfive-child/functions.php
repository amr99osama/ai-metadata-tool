<?php
defined( 'ABSPATH' ) || exit;

define( 'TAHALUF_CHILD_VERSION', '1.0.0' );

require_once get_stylesheet_directory() . '/inc/template-tags.php';
require_once get_stylesheet_directory() . '/inc/customizer.php';
require_once get_stylesheet_directory() . '/inc/customizer-output.php';

add_action( 'wp_enqueue_scripts', function() {
	$parent     = 'twentytwentyfive-style';
	$parent_url = get_template_directory_uri() . '/style.css';
	wp_enqueue_style( $parent, $parent_url, array(), wp_get_theme( 'twentytwentyfive' )->get( 'Version' ) );
	wp_enqueue_style(
		'tahaluf-child-style',
		get_stylesheet_uri(),
		array( $parent ),
		TAHALUF_CHILD_VERSION
	);
}, 20 );

add_action( 'after_setup_theme', function() {
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-width'  => true,
		'flex-height' => true,
	) );
} );
