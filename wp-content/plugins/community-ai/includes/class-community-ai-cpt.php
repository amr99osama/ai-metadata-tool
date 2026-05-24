<?php
defined( 'ABSPATH' ) || exit;

final class Community_AI_CPT {

	const POST_TYPE           = 'community_discussion';
	const META_KEY_SUMMARY    = '_community_ai_summary';
	const META_KEY_SUMMARY_TS = '_community_ai_summary_generated_at';

	public static function register() {
		add_action( 'init', array( __CLASS__, 'register_post_type' ) );
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
	}

	public static function register_post_type() {
		register_post_type( self::POST_TYPE, array(
			'labels'          => array(
				'name'               => __( 'Community Discussions', 'community-ai' ),
				'singular_name'      => __( 'Community Discussion', 'community-ai' ),
				'add_new_item'       => __( 'Add New Discussion', 'community-ai' ),
				'edit_item'          => __( 'Edit Discussion', 'community-ai' ),
				'new_item'           => __( 'New Discussion', 'community-ai' ),
				'view_item'          => __( 'View Discussion', 'community-ai' ),
				'search_items'       => __( 'Search Discussions', 'community-ai' ),
				'not_found'          => __( 'No discussions yet.', 'community-ai' ),
				'not_found_in_trash' => __( 'No discussions found in Trash.', 'community-ai' ),
				'all_items'          => __( 'All Discussions', 'community-ai' ),
				'menu_name'          => __( 'Discussions', 'community-ai' ),
			),
			'public'          => true,
			'show_in_rest'    => true,
			'has_archive'     => true,
			'menu_icon'       => 'dashicons-format-chat',
			'menu_position'   => 20,
			'supports'        => array( 'title', 'editor', 'author', 'excerpt', 'thumbnail', 'revisions' ),
			'rewrite'         => array( 'slug' => 'discussions' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		) );
	}

	public static function register_meta() {
		register_post_meta( self::POST_TYPE, self::META_KEY_SUMMARY, array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => array( 'Community_AI_Security', 'sanitize_plain_text' ),
			'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
				return Community_AI_Security::user_can_edit_post( $post_id );
			},
		) );

		register_post_meta( self::POST_TYPE, self::META_KEY_SUMMARY_TS, array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
			'auth_callback'     => function( $allowed, $meta_key, $post_id ) {
				return Community_AI_Security::user_can_edit_post( $post_id );
			},
		) );
	}
}
