( function( $ ) {
	'use strict';

	$( function() {
		var $btn   = $( '#community-ai-generate-summary' );
		var $ta    = $( '#community_ai_summary' );
		var $err   = $( '#community-ai-error' );
		var $spin  = $( '#community-ai-spinner' );
		var $stamp = $( '#community-ai-generated-at' );

		if ( ! $btn.length ) {
			return;
		}

		$btn.on( 'click', function() {
			$err.hide().empty();
			$spin.addClass( 'is-active' );
			$btn.prop( 'disabled', true );

			$.ajax( {
				url:    CommunityAI.ajax_url,
				method: 'POST',
				data: {
					action:  'community_ai_generate_summary',
					nonce:   CommunityAI.nonce,
					post_id: $btn.data( 'post-id' )
				}
			} ).done( function( res ) {
				if ( res && res.success && res.data && typeof res.data.summary === 'string' ) {
					$ta.val( res.data.summary );
					$stamp.text( CommunityAI.i18n.just_generated );
				} else {
					var msg = ( res && res.data && res.data.message ) ? res.data.message : CommunityAI.i18n.generic_error;
					$err.text( msg ).show();
				}
			} ).fail( function( xhr ) {
				var msg = (
					xhr &&
					xhr.responseJSON &&
					xhr.responseJSON.data &&
					xhr.responseJSON.data.message
				) ? xhr.responseJSON.data.message : CommunityAI.i18n.generic_error;

				$err.text( msg ).show();
			} ).always( function() {
				$spin.removeClass( 'is-active' );
				$btn.prop( 'disabled', false );
			} );
		} );
	} );

} )( jQuery );
