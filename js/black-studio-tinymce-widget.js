/* Black Studio TinyMCE Widget - JS */

/* global tinymce */
/* global bstw_local */

// TinyMCE initialization parameters
var tinyMCEPreInit;
// Current editor
var wpActiveEditor;

(function( $ ) {

	// Main object
	var bstw = {
		
		// Activate visual editor
		activate: function ( id ) {
			$( '#' + id ).addClass( 'mceEditor' );
			if ( typeof tinymce === 'object' && typeof tinymce.execCommand === 'function' ) {
				this.deactivate( id );
				tinyMCEPreInit.mceInit[id] = tinyMCEPreInit.mceInit['black-studio-tinymce-widget'];
				tinyMCEPreInit.mceInit[id].selector = '#' + id;
				try {
					// Instantiate new TinyMCE editor
					tinymce.init( tinymce.extend( {}, tinyMCEPreInit.mceInit['black-studio-tinymce-widget'], tinyMCEPreInit.mceInit[id] ) );
					tinymce.execCommand( 'mceAddControl', false, id );
				} catch( e ) {
					window.alert( e );
				}
				if ( tinymce.get( id ) !== null) {
					if ( typeof tinymce.get( id ).on === 'function' ) {
						tinymce.get( id ).on( 'keyup change', function() {
							var content = tinymce.get( id ).getContent();
							$( 'textarea#' + id ).val( content ).change();
						});
					}
				}
			}
		},
		
		// Deactivate visual editor
		deactivate: function( id ) {
			if ( typeof tinymce === 'object' && typeof tinymce.execCommand === 'function' ) {
				if ( tinymce.get( id ) !== null && typeof tinymce.get( id ).getContent === 'function' ) {
					var content = tinymce.get( id ).getContent();
					// tinymce.execCommand('mceRemoveControl', false, id);
					tinymce.get( id ).remove();
					$( 'textarea#' + id ).val( content );
				}
			}
		},
	
		// Activate editor deferred (used upon opening the widget)
		activate_after_open: function( id ) {
			$( 'div.widget-inside:has(#' + id + ') input[id^=widget-black-studio-tinymce][id$=type][value=visual]' ).each(function() {
				// If textarea is visible and animation/ajax has completed (or in accessibility mode) then trigger a click to Visual button and enable the editor
				if ( $('div.widget:has(#' + id + ') :animated' ).size() === 0 && tinymce.get( id ) === null && $( '#' + id ).is( ':visible' ) ) {
					$( 'a[id^=widget-black-studio-tinymce][id$=tmce]', $( this ).closest( 'div.widget-inside' ) ).click();
				}
				// Otherwise wait and retry later (animation ongoing)
				else if ( tinymce.get( id ) === null ) {
					setTimeout(function() {
						bstw.activate_after_open( id );
						id = null;
					}, 100 );
				}
				// If editor instance is already existing (i.e. dragged from another sidebar) just activate it
				else {
					$( 'a[id^=widget-black-studio-tinymce][id$=tmce]', $( this ).closest( 'div.widget-inside' ) ).click();
				}
			});
		},
		
		// Activate editor deferred (used upon ajax requests)
		activate_after_ajax: function ( id ) {
			$( 'div.widget-inside:has(#' + id + ') input[id^=widget-black-studio-tinymce][id$=type][value=visual]' ).each(function() {
				// If textarea is visible and animation/ajax has completed then trigger a click to Visual button and enable the editor
				if ( $.active === 0 && tinymce.get( id ) === null && $( '#' + id ).is( ':visible' ) ) {
					$( 'a[id^=widget-black-studio-tinymce][id$=tmce]', $( this ).closest( 'div.widget-inside' ) ).click();
				}
				// Otherwise wait and retry later (animation ongoing)
				else if ( $( 'div.widget:has(#' + id + ') div.widget-inside' ).is( ':visible' ) && tinymce.get( id ) === null ) {
					setTimeout(function() {
						bstw.activate_after_ajax( id );
						id=null;
					}, 100 );
				}
			});
		}
		
	};
	
	// Document ready stuff
	$( document ).ready(function() {
		// Event handler for widget opening button
		$( document ).on( 'click', 'div.widget:has(textarea[id^=widget-black-studio-tinymce]) .widget-title, div.widget:has(textarea[id^=widget-black-studio-tinymce]) a.widget-action', function() {
			//event.preventDefault();
			var $widget, $text_area, $widget_inside;
			$widget = $( this ).closest( 'div.widget' );
			$text_area = $( 'textarea[id^=widget-black-studio-tinymce]', $widget );
			if ( $( '[name="' + $( '#' + $text_area.attr('id') ).attr('name') + '"]' ).size() > 1) {
				$widget_inside = $( 'div.widget-inside', $widget );
				if ( $( 'div.error', $widget_inside).length === 0 ) {
					$widget_inside.prepend('<div class="error"><strong>' + bstw_local.error_duplicate_id + '</strong></div>');
				}
			}
			$( '#wpbody-content' ).css( 'overflow', 'visible' ); // needed for small screens
			$widget.css( 'position', 'relative' ).css( 'z-index', '100000' ); // needed for small screens and for fullscreen mode
			bstw.activate_after_open( $text_area.attr( 'id' ) );
			$( '.insert-media', $widget ).data( 'editor', $text_area.attr( 'id' ) );
		});
		// Event handler for widget save button
		$( document ).on( 'click', 'div.widget[id*=black-studio-tinymce] input[name=savewidget]', function() {
			var $widget, $text_area;
			$widget = $( this ).closest( 'div.widget' );
			$text_area = $( 'textarea[id^=widget-black-studio-tinymce]', $widget );
			if ( tinymce.get( $text_area.attr( 'id' ) ) !== null ) {
				bstw.deactivate( $text_area.attr( 'id' ) );
			}
			// Event handler for ajax complete
			$( this ).unbind( 'ajaxSuccess' ).ajaxSuccess(function() {
				var $text_area = $( 'textarea[id^=widget-black-studio-tinymce]', $( this ).closest( 'div.widget-inside' ) );
				bstw.activate_after_ajax( $text_area.attr( 'id' ) );
			});
		});
		// Event handler for visual switch button
		$( document ).on( 'click', 'a[id^=widget-black-studio-tinymce][id$=tmce]', function() {
			//event.preventDefault();
			var $widget_inside, $wrap_id, $textarea_id;
			$widget_inside = $( this ).closest( 'div.widget-inside, div.panel-dialog' );
			$wrap_id = $( 'div[id^=wp-widget-black-studio-tinymce][id$=-wrap]', $widget_inside );
			$textarea_id = $( 'textarea[id^=widget-black-studio-tinymce]', $widget_inside ).attr( 'id' );
			tinymce.DOM.removeClass( $wrap_id, 'html-active' );
			tinymce.DOM.addClass( $wrap_id, 'tmce-active' );
			$( 'input[id^=widget-black-studio-tinymce][id$=type]', $widget_inside ).val( 'visual' );
			bstw.activate( $textarea_id );
		});
		// Event handler for html switch button
		$( document ).on( 'click', 'a[id^=widget-black-studio-tinymce][id$=html]', function() {
			//event.preventDefault();
			var $widget_inside, $wrap_id, $textarea_id;
			$widget_inside = $( this ).closest( 'div.widget-inside,div.panel-dialog' );
			$wrap_id = $( 'div[id^=wp-widget-black-studio-tinymce][id$=-wrap]', $widget_inside );
			$textarea_id = $( 'textarea[id^=widget-black-studio-tinymce]', $widget_inside ).attr( 'id' );
			$( 'input[id^=widget-black-studio-tinymce][id$=type]', $widget_inside ).val( 'visual' );
			tinymce.DOM.removeClass( $wrap_id, 'tmce-active' );
			tinymce.DOM.addClass( $wrap_id, 'html-active' );
			$( 'input[id^=widget-black-studio-tinymce][id$=type]', $widget_inside ).val( 'html' );
			bstw.deactivate( $textarea_id );
		});
		// Set wpActiveEditor variables used when adding media from media library dialog
		$( document ).on( 'click', '.wp-media-buttons a', function() {
			var $widget_inside = $( this ).closest( 'div.widget-inside' );
			wpActiveEditor = $( 'textarea[id^=widget-black-studio-tinymce]', $widget_inside ).attr( 'id' );
		});
		// Activate editor when in accessibility mode
		if ( $( 'body.widgets_access' ).size() > 0 ) {
			var $text_area = $( 'textarea[id^=widget-black-studio-tinymce]' );
			bstw.activate_after_open( $text_area.attr( 'id' ) );
		}
	});

})( jQuery ); // end self-invoked wrapper function