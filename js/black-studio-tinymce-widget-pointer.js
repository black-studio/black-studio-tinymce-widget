/* Black Studio TinyMCE Widget - JS - Admin pointer */

/* global bstwPointers, ajaxurl */

jQuery( document ).ready( function( $ ) {
	bstwOpenPointers( 0 );
	function bstwOpenPointers( i ) {
		var pointer = bstwPointers.pointers[i],
			options = $.extend( pointer.options, {
			close: function() {
				$.post( ajaxurl, {
					pointer: pointer.pointer_id,
					action: 'dismiss-wp-pointer'
				});
			}
		});
		$( pointer.target ).pointer( options ).pointer( 'open' );
	}
});
