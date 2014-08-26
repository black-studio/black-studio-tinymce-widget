/* Black Studio TinyMCE Widget - JS */

/* global tinymce */
/* global bstw_data */
/* global tinyMCEPreInit */
/* global wpActiveEditor: true */

(function( $ ) {

	// Returns bstw instance given the textarea ID or any jQuery object inside the widget object
	function bstw( arg ) {

		var id = null;

		// ID initialization
		if ( typeof arg === 'string' ) {
			id = arg;
		}
		// jQuery object initialization
		else if ( typeof arg === 'object' && arg instanceof jQuery ) {
			id = $( 'textarea[id^=widget-black-studio-tinymce]', arg.closest( bstw_data.container_selectors ) ).attr( 'id' );
		}

		// Create and return instance
		return {

			// Activate visual editor
			activate: function () {
				$( '#' + id ).addClass( 'mceEditor' );
				if ( typeof tinymce === 'object' && typeof tinymce.execCommand === 'function' ) {
					this.deactivate();
					tinyMCEPreInit.mceInit[id] = tinyMCEPreInit.mceInit['black-studio-tinymce-widget'];
					tinyMCEPreInit.mceInit[id].selector = '#' + id;
					try {
						// Instantiate new TinyMCE editor
						tinymce.init( tinymce.extend( {}, tinyMCEPreInit.mceInit['black-studio-tinymce-widget'], tinyMCEPreInit.mceInit[id] ) );
						tinymce.execCommand( 'mceAddControl', false, id );
					} catch( e ) {
						window.alert( e );
					}
					// Real time preview (Theme customizer)
					if ( tinymce.get( id ) !== null) {
						if ( typeof tinymce.get( id ).on === 'function' ) {
							tinymce.get( id ).on( 'keyup change', function() {
								var content = tinymce.get( id ).getContent();
								$( '#' + id ).val( content ).change();
							});
						}
					}
				}
				return this;
			},

			// Deactivate visual editor
			deactivate: function() {
				if ( typeof tinymce === 'object' && typeof tinymce.execCommand === 'function' ) {
					if ( tinymce.get( id ) !== null && typeof tinymce.get( id ).getContent === 'function' ) {
						var content = tinymce.get( id ).getContent();
						// tinymce.execCommand('mceRemoveControl', false, id);
						tinymce.get( id ).remove();
						$( '#' + id ).val( content );
					}
				}
				return this;
			},

			// Activate editor deferred (after widget opening)
			activate_after_open: function() {
				$( 'div.widget-inside:has(#' + id + ') input[id^=widget-black-studio-tinymce][id$=type][value=visual]' ).each(function() {
					// If textarea is visible and animation/ajax has completed (or in accessibility mode) then trigger a click to Visual button and enable the editor
					if ( $('div.widget:has(#' + id + ') :animated' ).size() === 0 && tinymce.get( id ) === null && $( '#' + id ).is( ':visible' ) ) {
						$( 'a[id^=widget-black-studio-tinymce][id$=tmce]', $( this ).closest( 'div.widget-inside' ) ).click();
					}
					// Otherwise wait and retry later (animation ongoing)
					else if ( tinymce.get( id ) === null ) {
						setTimeout(function() {
							bstw( id ).activate_after_open();
							id = null;
						}, 100 );
					}
					// If editor instance is already existing (i.e. dragged from another sidebar) just activate it
					else {
						$( 'a[id^=widget-black-studio-tinymce][id$=tmce]', $( this ).closest( 'div.widget-inside' ) ).click();
					}
				});
				return this;
			},

			// Activate editor deferred (after ajax requests)
			activate_after_ajax: function () {
				$( 'div.widget-inside:has(#' + id + ') input[id^=widget-black-studio-tinymce][id$=type][value=visual]' ).each(function() {
					// If textarea is visible and animation/ajax has completed then trigger a click to Visual button and enable the editor
					if ( $.active === 0 && tinymce.get( id ) === null && $( '#' + id ).is( ':visible' ) ) {
						$( 'a[id^=widget-black-studio-tinymce][id$=tmce]', $( this ).closest( 'div.widget-inside' ) ).click();
					}
					// Otherwise wait and retry later (animation ongoing)
					else if ( $( 'div.widget:has(#' + id + ') div.widget-inside' ).is( ':visible' ) && tinymce.get( id ) === null ) {
						setTimeout(function() {
							bstw( id ).activate_after_ajax();
							id=null;
						}, 100 );
					}
				});
				return this;
			},

			// Return the div.widget jQuery object containing the instance
			get_widget: function() {
				return $( '#' + id ).closest( 'div.widget' );
			},

			// Return the div.widget-inside jQuery object containing the instance
			get_widget_inside: function() {
				return $( '#' + id ).closest( 'div.widget-inside' );
			},

			// Return the div.wp-editor-wrap jQuery object containing the instance
			get_editor_wrap: function() {
				return $( '#' + id ).closest( 'div.wp-editor-wrap' );
			},

			// Return the textarea jQuery object related to the instance
			get_textarea: function() {
				return $( '#' + id );
			},

			// Return the textarea ID related to the instance
			get_id: function() {
				return id;
			},

			// Return the tinymce instance related to the instance
			get_tinymce: function() {
				return tinymce.get( id );
			},

			// Check if the connected tinymce instance is active
			is_tinymce_active: function() {
				return tinymce.get( id ) !== null;
			},

			// Set the value of the hidden input "type" ( visual / html )
			switch_to: function( value ) {
				var add_class = ( value === 'visual' ? 'tmce-active' : 'html-active' ),
					remove_class = ( value === 'visual' ? 'html-active' : 'tmce-active' );
				this.get_editor_wrap().removeClass( remove_class ).addClass( add_class );
				$( 'input[id^=widget-black-studio-tinymce][id$=type]', this.get_widget_inside() ).val( value );
				return this;
			},

			// Check for widgets with duplicate ids
			check_duplicates: function() {
				if ( $( '[name="' + this.get_textarea().attr('name') + '"]' ).size() > 1) {
					if ( $( 'div.error', this.get_widget_inside() ).length === 0 ) {
						this.get_widget_inside().prepend('<div class="error"><strong>' + bstw_data.error_duplicate_id + '</strong></div>');
					}
				}
				return this;
			},

			// Fix CSS
			fix_css: function() {
				this.get_widget().css( 'position', 'relative' ).css( 'z-index', '100000' ); // needed for small screens and for fullscreen mode
				$( '#wpbody-content' ).css( 'overflow', 'visible' ); // needed for small screens
				return this;
			},

			// Set target on media buttons
			set_media_target: function() {
				$( '.insert-media', this.get_widget() ).data( 'editor', id );
				return this;
			}
		};
	}

	// Document ready stuff
	$( document ).ready(function() {

		// Event handler for widget opening button
		$( document ).on( 'click', 'div.widget[id*=black-studio-tinymce] .widget-title, div.widget[id*=black-studio-tinymce] a.widget-action', function() {
			bstw( $( this ) ).check_duplicates().fix_css().set_media_target().activate_after_open();
		});

		// Event handler for widget save button
		$( document ).on( 'click', 'div.widget[id*=black-studio-tinymce] input[name=savewidget]', function() {
			if ( bstw( $( this ) ).is_tinymce_active() ) {
				bstw( $( this ) ).deactivate();
			}
			// Event handler for ajax complete
			$( this ).unbind( 'ajaxSuccess' ).ajaxSuccess(function() {
				bstw( $( this ) ).activate_after_ajax();
			});
		});

		// Event handler for visual switch button
		$( document ).on( 'click', 'a[id^=widget-black-studio-tinymce][id$=tmce]', function() {
			bstw( $( this ) ).switch_to( 'visual' ).activate();
		});

		// Event handler for html switch button
		$( document ).on( 'click', 'a[id^=widget-black-studio-tinymce][id$=html]', function() {
			bstw( $( this ) ).switch_to( 'html' ).deactivate();
		});

		// Event handler for widget added (i.e. with Theme Customizer */
		$( document ).on( 'widget-added', function( event, $widget ) {
			if ( $widget.is( '[id*=black-studio-tinymce]' ) ) {
				event.preventDefault();
				bstw( $widget ).activate_after_open();
			}
		});

		// Set wpActiveEditor variables used when adding media from media library dialog
		$( document ).on( 'click', '.wp-media-buttons a', function() {
			wpActiveEditor = bstw( $( this ) ).get_id();
		});

		// Activate editor when in accessibility mode
		if ( $( 'body.widgets_access' ).size() > 0 ) {
			bstw( $( 'textarea[id^=widget-black-studio-tinymce]' ).attr( 'id' ) ).activate_after_open();
		}

	});

})( jQuery ); // end self-invoked wrapper function
