<?php
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'community_ai_settings' );

global $wpdb;
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s OR meta_key = %s",
		'_community_ai_summary',
		'_community_ai_summary_generated_at'
	)
);
