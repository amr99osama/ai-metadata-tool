<?php
defined( 'ABSPATH' ) || exit;

/**
 * Centralized input/output security helpers.
 *
 * Every entry point (meta-save, ajax, settings, customizer) routes through this
 * class so the rules live in exactly one place.
 */
final class Community_AI_Security {

	const MAX_TEXT_BYTES = 100000;

	/* ---------- Nonces ---------- */

	public static function verify_post_nonce( $action, $field = '_wpnonce' ) {
		if ( ! isset( $_POST[ $field ] ) ) {
			return false;
		}
		return (bool) wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST[ $field ] ) ),
			$action
		);
	}

	public static function require_ajax_nonce( $action, $field = 'nonce' ) {
		check_ajax_referer( $action, $field );
	}

	/* ---------- Capabilities ---------- */

	public static function user_can_edit_post( $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return false;
		}
		return current_user_can( 'edit_post', $post_id );
	}

	public static function user_can_manage_options() {
		return current_user_can( 'manage_options' );
	}

	/* ---------- Sanitizers ---------- */

	public static function sanitize_plain_text( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}
		$value = wp_unslash( $value );
		if ( strlen( $value ) > self::MAX_TEXT_BYTES ) {
			$value = substr( $value, 0, self::MAX_TEXT_BYTES );
		}
		return sanitize_text_field( $value );
	}

	public static function sanitize_rich_text( $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}
		$value = wp_unslash( $value );
		if ( strlen( $value ) > self::MAX_TEXT_BYTES ) {
			$value = substr( $value, 0, self::MAX_TEXT_BYTES );
		}
		return wp_kses_post( $value );
	}

	public static function sanitize_summary_length( $value, $min = 10, $max = 500 ) {
		$value = absint( $value );
		if ( $value < $min ) {
			return $min;
		}
		if ( $value > $max ) {
			return $max;
		}
		return $value;
	}

	public static function sanitize_url( $value ) {
		return esc_url_raw( wp_unslash( (string) $value ) );
	}

	/* ---------- Escapers (thin wrappers for call-site clarity) ---------- */

	public static function out_text( $value ) { return esc_html( $value ); }
	public static function out_attr( $value ) { return esc_attr( $value ); }
	public static function out_url( $value )  { return esc_url( $value ); }
	public static function out_kses( $value ) { return wp_kses_post( $value ); }
}
