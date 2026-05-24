<?php
defined( 'ABSPATH' ) || exit;

/**
 * Deterministic mock summarizer.
 *
 * No network calls. Extracts the first meaningful sentences from the post,
 * trims to the configured length, and returns plain text editors can adjust.
 */
final class Community_AI_Mock_Service implements Community_AI_Service_Interface {

	public function summarize( $content, $min_length, $max_length ) {

		$content = wp_strip_all_tags( (string) $content );
		$content = preg_replace( '/\s+/u', ' ', $content );
		$content = trim( $content );

		if ( '' === $content ) {
			return new WP_Error(
				'community_ai_empty_content',
				__( 'Cannot summarize empty content.', 'community-ai' )
			);
		}

		$sentences = preg_split( '/(?<=[.!?])\s+/u', $content );
		$summary   = '';
		foreach ( $sentences as $s ) {
			if ( mb_strlen( $summary . ' ' . $s ) > $max_length ) {
				break;
			}
			$summary = trim( $summary . ' ' . $s );
		}

		if ( '' === $summary ) {
			$summary    = mb_substr( $content, 0, $max_length );
			$last_space = mb_strrpos( $summary, ' ' );
			if ( false !== $last_space && $last_space > $min_length ) {
				$summary = mb_substr( $summary, 0, $last_space );
			}
			$summary .= '…';
		}

		if ( mb_strlen( $summary ) < $min_length ) {
			$pad     = mb_substr( $content, mb_strlen( $summary ), $min_length - mb_strlen( $summary ) );
			$summary = trim( $summary . ' ' . $pad );
		}

		return $summary;
	}
}
