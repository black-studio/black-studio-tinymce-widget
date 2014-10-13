/* Black Studio TinyMCE Widget - JS - Admin pointer */

/* global bstw_pointer, ajaxurl */

jQuery( document ).ready( function( $ ) {
    bstw_open_pointer( 0 );
    function bstw_open_pointer( i ) {
        var pointer = bstw_pointer.pointers[i];
        var options = $.extend( pointer.options, {
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