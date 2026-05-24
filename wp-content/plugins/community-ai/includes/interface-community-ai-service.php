<?php
defined( 'ABSPATH' ) || exit;

interface Community_AI_Service_Interface {

	/**
	 * Produce a brief summary of the given content.
	 *
	 * @param string $content    The (already-sanitized) post content.
	 * @param int    $min_length Lower bound in characters.
	 * @param int    $max_length Upper bound in characters.
	 * @return string|WP_Error
	 */
	public function summarize( $content, $min_length, $max_length );
}
