/* Black Studio TinyMCE Widget - JS */

/* global bstwData, tinymce, tinyMCEPreInit, isRtl */

( function( $ ) {

	// Returns bstw instance given the textarea ID or any jQuery object inside the widget object
	function bstw( arg ) {
		var id = null;
		if ( 'string' === typeof arg ) {

			// ID initialization
			id = arg;
		} else if ( 'object' === typeof arg && arg instanceof jQuery ) {

			// jQuery object initialization
			id = $( 'textarea[id^=widget-black-studio-tinymce]', arg.closest( bstwData.container_selectors ) ).attr( 'id' );
		}

		// Create and return instance
		return {

			// Activate visual editor
			activate: function() {
				$( '#' + id ).addClass( 'mceEditor' );
				if ( 'object' === typeof tinymce && 'function' === typeof tinymce.execCommand ) {
					this.deactivate();
					tinyMCEPreInit.mceInit[id] = tinyMCEPreInit.mceInit['black-studio-tinymce-widget'];
					tinyMCEPreInit.mceInit[id].selector = '#' + id;
					try {

						// Instantiate new TinyMCE editor
						tinymce.init( tinymce.extend({}, tinyMCEPreInit.mceInit['black-studio-tinymce-widget'], tinyMCEPreInit.mceInit[ id ]) );
						tinymce.execCommand( 'mceAddControl', false, id );
					} catch ( e ) {
						window.alert( e );
					}

					// Real time preview (Customizer)
					if ( this.isTinymceActive() ) {
						if ( 'function' === typeof tinymce.get( id ).on ) {
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
				var content;
				if ( 'object' === typeof tinymce && 'function' === typeof tinymce.execCommand ) {
					if ( this.isTinymceActive() ) {
						content = tinymce.get( id ).getContent();
						tinymce.get( id ).remove();
						$( '#' + id ).val( content );
					}
				}
				return this;
			},

			// Activate editor deferred (after widget opening)
			activateAfterOpen: function() {

				// Activate only if type is set to visual
				if ( 'visual' === this.getMode() ) {
					if ( 0 === $( 'div.widget:has(#' + id + ') :animated' ).size() && ! this.isTinymceActive() && this.isTexteareaVisible() ) {

						// If textarea is visible and animation/ajax has completed (or in accessibility mode) then trigger a click to Visual button and enable the editor
						this.setMode( 'visual' );
					} else if ( ! this.isTinymceActive() ) {

						// Otherwise wait and retry later (animation ongoing)
						setTimeout( function() {
							bstw( id ).activateAfterOpen();
						}, 100 );
					} else {

						// If editor instance is already existing (i.e. dragged from another sidebar) just activate it
						this.setMode( 'visual' );
					}
				}
				return this;
			},

			// Activate editor deferred (after ajax requests)
			activateAfterAjax: function() {

				// Activate only if type is set to visual
				if ( 'visual' === this.getMode() ) {
					if ( 0 === $.active && ! this.isTinymceActive() && this.isTexteareaVisible() ) {

						// If textarea is visible and animation/ajax has completed then trigger a click to Visual button and enable the editor
						this.setMode( 'visual' );
					} else if ( this.isWidgetInsideVisible() && ! this.isTinymceActive() ) {

						// Otherwise wait and retry later (animation ongoing)
						setTimeout( function() {
							bstw( id ).activateAfterAjax();
						}, 100 );
					}
				}
				return this;
			},

			// Get the div.widget jQuery object containing the instance
			getWidget: function() {
				return $( '#' + id ).closest( 'div.widget' );
			},

			// Get the div.widget-inside jQuery object containing the instance
			getWidgetInside: function() {
				return $( '#' + id ).closest( 'div.widget-inside' );
			},

			// Get the div.wp-editor-wrap jQuery object containing the instance
			getEditorWrap: function() {
				return $( '#' + id ).closest( 'div.wp-editor-wrap' );
			},

			// Get the textarea jQuery object related to the instance
			getTextarea: function() {
				return $( '#' + id );
			},

			// Get the textarea ID related to the instance
			getId: function() {
				return id;
			},

			// Get the tinymce instance related to the instance
			getTinymce: function() {
				return tinymce.get( id );
			},

			// Get the current editor mode ( visual / html )
			getMode: function() {
				return  $( 'input[id^=widget-black-studio-tinymce][id$=type]', this.getWidgetInside() ).val();

			},

			// Set editor mode ( visual / html )
			setMode: function( value ) {
				if ( 'visual' === value ) {
					this.getEditorWrap().removeClass( 'html-active' ).addClass( 'tmce-active' );
					this.activate();
				}
				if ( 'html' === value ) {
					this.getEditorWrap().removeClass( 'tmce-active' ).addClass( 'html-active' );
					this.deactivate();
				}
				$( 'input[id^=widget-black-studio-tinymce][id$=type]', this.getWidgetInside() ).val( value );
				return this;
			},

			// Check if the connected tinymce instance is active
			isTinymceActive: function() {
				return 'object' === typeof tinymce && 'object' === typeof tinymce.get( id ) && null !== tinymce.get( id );
			},

			// Check if the textarea is visible
			isTexteareaVisible: function() {
				return $( '#' + id ).is( ':visible' );
			},

			// Check if the widget inside is visible
			isWidgetInsideVisible: function() {
				return $( ' div.widget-inside:has(#' + id + ')' ).is( ':visible' );
			},

			// Check for widgets with duplicate ids
			checkDuplicates: function() {
				if ( 1 < $( '[name="' + this.getTextarea().attr( 'name' ) + '"]' ).size() ) {
					if ( 0 === $( 'div.error', this.getWidgetInside() ).length ) {
						this.getWidgetInside().prepend( '<div class="error"><strong>' + bstwData.error_duplicate_id + '</strong></div>' );
					}
				}
				return this;
			},

			// Fix CSS
			fixCss: function() {
				this.getWidget().css( 'position', 'relative' ).css( 'z-index', '100000' ); // needed for small screens and for fullscreen mode
				$( '#wpbody-content' ).css( 'overflow', 'visible' ); // needed for small screens
				return this;
			},

			// Set target on media buttons
			setMediaTarget: function() {
				$( '.insert-media', this.getWidget() ).data( 'editor', id );
				return this;
			}
		};
	}

	// Document ready stuff
	$( document ).ready( function() {

		// Event handler for widget open button
		$( document ).on( 'click', 'div.widget[id*=black-studio-tinymce] .widget-title, div.widget[id*=black-studio-tinymce] a.widget-action, div.widget[id*=black-studio-tinymce] div.widget-title-action', function() {
			var targetWidth, windowWidth, widgetWidth, menuWidth, isRTL, margin;
			bstw( $( this ) ).checkDuplicates().fixCss().setMediaTarget().activateAfterOpen();

			// Event handler for widget save button (for new instances)
			// Note: this event handler is intentionally attached to the save button instead of document
			// to let the the textarea content be updated before the ajax request is run
			$( 'input[name=savewidget]',  bstw( $( this ) ).getWidget() ).on( 'click', function() {
				if ( bstw( $( this ) ).isTinymceActive() ) {
					bstw( $( this ) ).deactivate();
				}

				// Event handler for ajax complete
				$( this ).unbind( 'ajaxSuccess' ).ajaxSuccess( function() {
					bstw( $( this ) ).activateAfterAjax();
				});
			});

			// Responsive: adjust widget width if it can't fit into the screen
			if ( ! $( this ).parents( '#available-widgets' ).length ) {
				targetWidth = parseInt( $( 'input[name=widget-width]', bstw( $( this ) ).getWidget() ).val(), 10 );
				windowWidth = $( window ).width();
				widgetWidth = bstw( $( this ) ).getWidget().parent().width();
				menuWidth = parseInt( $( '#wpcontent' ).css( 'margin-left' ), 10 );
				isRTL = !! ( 'undefined' !== typeof isRtl && isRtl );
				if ( targetWidth + menuWidth + 30 > windowWidth ) {
					if ( bstw( $( this ) ).getWidget().closest( 'div.widget-liquid-right' ).length ) {
						margin = isRTL ? 'margin-right' : 'margin-left';
					} else {
						margin = isRTL ? 'margin-left' : 'margin-right';
					}
					$( bstw( $( this ) ).getWidget() ).css( margin, ( widgetWidth - ( windowWidth - 30 - menuWidth ) ) + 'px' );
				}
			}
		});

		// Event handler for widget save button (for existing instances)
		$( 'div.widget[id*=black-studio-tinymce] input[name=savewidget]' ).on( 'click', function() {
			if ( bstw( $( this ) ).isTinymceActive() ) {
				bstw( $( this ) ).deactivate();
			}

			// Event handler for ajax complete
			$( this ).unbind( 'ajaxSuccess' ).ajaxSuccess( function() {
				bstw( $( this ) ).activateAfterAjax();
			});
		});

		// Event handler for visual switch button
		$( document ).on( 'click', 'a[id^=widget-black-studio-tinymce][id$=tmce]', function() {
			bstw( $( this ) ).setMode( 'visual' );
		});

		// Event handler for html switch button
		$( document ).on( 'click', 'a[id^=widget-black-studio-tinymce][id$=html]', function() {
			bstw( $( this ) ).setMode( 'html' );
		});

		// Event handler for widget added (i.e. with Customizer */
		$( document ).on( 'widget-added', function( event, $widget ) {
			if ( $widget.is( '[id*=black-studio-tinymce]' ) ) {
				event.preventDefault();
				bstw( $widget ).activateAfterOpen();
			}
		});

		// Set window.wpActiveEditor variable used when adding media from media library dialog
		$( document ).on( 'click', '.wp-media-buttons a', function() {
			window.wpActiveEditor = bstw( $( this ) ).getId();
		});

		// Activate editor when in accessibility mode
		if ( 0 < $( 'body.widgets_access' ).size() ) {
			bstw( $( 'textarea[id^=widget-black-studio-tinymce]' ).attr( 'id' ) ).activateAfterOpen();
		}

		// Plugin links toggle behavior
		$( document ).on( 'click', '.bstw-links-icon', function( event ) {
			event.preventDefault();
			$( this ).closest( '.bstw-links' ).children( '.bstw-links-list' ).toggle();
		});

	});

}( jQuery ) ); // end self-invoked wrapper function
