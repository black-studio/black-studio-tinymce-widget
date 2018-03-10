/* Black Studio TinyMCE Widget - JS */

/* global bstwData, tinymce, tinyMCEPreInit, QTags, quicktags, isRtl, ajaxurl */

var bstw;

( function( $ ) {

	// Return bstw instance given the textarea ID or any jQuery object inside the widget object
	bstw = function( arg ) {

		var id = null;

		if ( 'string' === typeof arg ) {

			// ID initialization
			id = arg;

		} else if ( 'object' === typeof arg && arg instanceof jQuery ) {

			// jQuery object initialization
			id = $( 'textarea[id^=widget-black-studio-tinymce][id$=text]', arg.closest( bstwData.container_selectors ) ).attr( 'id' );

		}

		// Create and return instance
		return {

			// Activate editor
			activate: function( forceInit ) {
				var prevInstances, newInstance;
				forceInit = 'undefined' !== typeof forceInit ? forceInit : true;
				if ( ! $( '#' + id ).hasClass( 'active' ) ) {
					if ( ! $( '#' + id ).hasClass( 'activating' ) ) {
						$( '#' + id ).addClass( 'activating' );
					}
					if ( ! this.isQuicktagsConfigured() ) {
						tinyMCEPreInit.qtInit[ id ] = tinyMCEPreInit.qtInit['black-studio-tinymce-widget'];
						tinyMCEPreInit.qtInit[ id ].id = id;
					}
					if ( ! this.isQuicktagsActive() ) {
						prevInstances = QTags.instances;
						QTags.instances = [];
						quicktags( tinyMCEPreInit.qtInit[ id ]);
						QTags._buttonsInit();
						newInstance = QTags.instances[ id ];
						QTags.instances = prevInstances;
						QTags.instances[ id ] = newInstance;
					}
					if ( ! this.isTinymceConfigured() ) {
						if ( 'undefined' !== typeof tinyMCEPreInit.mceInit['black-studio-tinymce-widget']) {
							tinyMCEPreInit.mceInit[ id ] = tinyMCEPreInit.mceInit['black-studio-tinymce-widget'];
							tinyMCEPreInit.mceInit[ id ].selector = '#' + id;
						}
					}
					if ( this.isTinymceConfigured() ) {
						if ( ! this.isTinymceActive() && 'visual' === this.getMode() && $( '#' + id ).is( ':visible' ) ) {
							tinyMCEPreInit.mceInit[ id ].setup = function( ed ) {

								// Real time preview (Customizer)
								ed.on( 'keyup change', function() {
									if ( 'visual' === bstw( id ).getMode() ) {
										bstw( id ).updateContent();
									}
									$( '#' + id ).change();
								});
								$( '#' + id ).addClass( 'active' ).removeClass( 'activating' );
							};
							if ( ! forceInit ) {
								this.go();
							}	else {
								tinymce.init( tinyMCEPreInit.mceInit[ id ]);
							}
						} else if ( ! this.isTinymceActive() && 'visual' === this.getMode() ) {
							setTimeout( function() {
								bstw( id ).activate( forceInit );
							}, 500 );
						} else {
							$( '#' + id ).addClass( 'active' ).removeClass( 'activating' );
						}
					}
				}
				return this;
			},

			// Deactivate editor
			deactivate: function() {
				if ( ! $( '#' + id ).hasClass( 'activating' ) ) {
					if ( this.isTinymceActive() ) {
						tinymce.get( id ).remove();
					}
					if ( this.isTinymceConfigured() ) {
						delete tinyMCEPreInit.mceInit[ id ];
					}
					if ( this.isQuicktagsActive() ) {
						$( '.quicktags-toolbar', this.getWidgetInside() ).remove();
						delete QTags.instances[ id ];
					}
					if ( this.isQuicktagsConfigured() ) {
						delete tinyMCEPreInit.qtInit[ id ];
					}
					$( '#' + id ).removeClass( 'active' );
				}
				return this;
			},

			// Update textarea content when in visual mode
			updateContent: function() {
				var content;
				if ( this.isTinymceConfigured() ) {
					if ( 'visual' === this.getMode() ) {
						content = tinymce.get( id ).save();
						if ( tinyMCEPreInit.mceInit[ id ].wpautop ) {
							content = window.switchEditors.pre_wpautop( content );
						}
						this.getTextarea().val( content );
					} else if ( this.isTinymceActive() ) {
						content = this.getTextarea().val();
						if ( tinyMCEPreInit.mceInit[ id ].wpautop ) {
							content = window.switchEditors.wpautop( content );
						}
						tinymce.get( id ).setContent( content );
					}
				}
				return this;
			},

			// Setup an editor mode
			go: function( mode ) {
				if ( 'undefined' === typeof mode ) {
					mode = this.getMode();
				}
				window.switchEditors.go( id, 'visual' === mode ? 'tmce' : 'html' );
				return this;
			},

			// Get the current editor mode ( visual / html ) from the input value
			getMode: function() {
				return  $( 'input[id^=widget-black-studio-tinymce][id$=type]', this.getContainer() ).val();
			},

			// Set editor mode ( visual / html ) into the input value
			setMode: function( mode ) {
				$( 'input[id^=widget-black-studio-tinymce][id$=type]', this.getContainer() ).val( mode );
				return this;
			},

			// Get the jQuery container object containing the instance
			getContainer: function() {
				return $( '#' + id ).closest( bstwData.container_selectors );
			},

			// Get the div.widget jQuery object containing the instance
			getWidget: function() {
				return $( '#' + id ).closest( 'div.widget' );
			},

			// Get the div.widget-inside jQuery object containing the instance
			getWidgetInside: function() {
				return $( '#' + id ).closest( 'div.widget-inside' );
			},

			// Get the textarea jQuery object related to the instance
			getTextarea: function() {
				return $( '#' + id );
			},

			// Check if the tinymce instance is active
			isTinymceActive: function() {
				return 'object' === typeof tinymce && 'object' === typeof tinymce.get( id ) && null !== tinymce.get( id );
			},

			// Check if the tinymce instance is configured
			isTinymceConfigured: function() {
				return 'undefined' !== typeof tinyMCEPreInit.mceInit[ id ];
			},

			// Check if the quicktags instance is active
			isQuicktagsActive: function() {
				return 'object' === typeof QTags.instances[ id ];
			},

			// Check if the quicktags instance is configured
			isQuicktagsConfigured: function() {
				return 'object' === typeof tinyMCEPreInit.qtInit[ id ];
			},

			// Checks and settings to run before opening the widget
			prepare: function() {

				// Check for widgets with duplicate ids
				if ( 1 < $( '[name="' + this.getTextarea().attr( 'name' ) + '"]' ).size() ) {
					if ( 0 === $( 'div.error', this.getWidgetInside() ).length ) {
						this.getWidgetInside().prepend( '<div class="error"><strong>' + bstwData.error_duplicate_id + '</strong></div>' );
					}
				}

				// Fix CSS
				this.getWidget().css( 'position', 'relative' ).css( 'z-index', '100000' ); // needed for small screens and for fullscreen mode
				$( '#wpbody-content' ).css( 'overflow', 'visible' ); // needed for small screens
				return this;
			},

			// Responsive: adjust widget width if it can't fit into the screen
			responsive: function() {
				var targetWidth, windowWidth, widgetWidth, menuWidth, isRTL, margin;
				if ( this.getWidgetInside().is( ':visible' ) ) {
					targetWidth = parseInt( $( 'input[name=widget-width]', this.getWidget() ).val(), 10 );
					windowWidth = $( window ).width();
					widgetWidth = this.getWidget().parent().width();
					menuWidth = parseInt( $( '#wpcontent' ).css( 'margin-left' ), 10 );
					isRTL = !! ( 'undefined' !== typeof isRtl && isRtl );
					if ( targetWidth + menuWidth + 30 > windowWidth ) {
						if ( this.getWidget().closest( 'div.widget-liquid-right' ).length ) {
							margin = isRTL ? 'margin-right' : 'margin-left';
						} else {
							margin = isRTL ? 'margin-left' : 'margin-right';
						}
						this.getWidget().css( margin, ( widgetWidth - ( windowWidth - 30 - menuWidth ) ) + 'px' );
					}
				}
				return this;
			}

		};
	};

	// Document ready stuff
	$( document ).ready( function() {

		// Event handler for widget open button
		$( document ).on( 'click', 'div.widget[id*=black-studio-tinymce] .widget-title, div.widget[id*=black-studio-tinymce] .widget-title-action', function() {
			if ( ! $( this ).parents( '#available-widgets' ).length ) {
				bstw( $( this ) ).prepare().responsive().activate( false );

				// Note: the save event handler is intentionally attached to the save button instead of document
				// to let the the textarea content be updated before the ajax request starts
				$( 'input[name=savewidget]',  bstw( $( this ) ).getWidget() ).on( 'click', function() {
					var height = $( this ).closest( '.widget' ).find( '.wp-editor-wrap' ).height();
					$( this ).closest( '.widget' ).find( '.wp-editor-wrap' ).height( height ).append( '<div class="bstw-loading"></div>' );
					$( this ).closest( '.widget' ).find( '.bstw-loading' ).height( height ).show();
					bstw( $( this ) ).updateContent();
				});
			}
		});

		// Event handler for widget added
		$( document ).on( 'widget-added', function( event, $widget ) {
			if ( $widget.is( '[id*=black-studio-tinymce]' ) ) {
				event.preventDefault();
				bstw( $widget ).activate();
			}
		});

		// Event handler for widget updated
		$( document ).on( 'widget-updated', function( event, $widget ) {
			if ( $widget.is( '[id*=black-studio-tinymce]' ) ) {
				event.preventDefault();
				bstw( $widget ).deactivate().activate();
			}
		});

		// Event handler for widget control focus and expand (triggered by shift+click on Customizer)
		$( document ).on( 'expand', function( event ) {
			var $widget = bstw( $( 'textarea[id^=widget-black-studio-tinymce][id$=text]', event.target ) ).getWidget();
			if ( $widget.is( '[id*=black-studio-tinymce]' ) ) {
				event.preventDefault();
				setTimeout( function() {
					bstw( $widget ).deactivate().activate();
				}, 500 );
			}
		});

		// Event handler for widget synced
		$( document ).on( 'widget-synced', function( event, $widget ) {
			if ( $widget.is( '[id*=black-studio-tinymce]' ) ) {
				event.preventDefault();
				if ( 'visual' === bstw( $widget ).getMode() ) {
					bstw( $widget ).updateContent();
				}
			}
		});

		// Event handler for visual switch button
		$( document ).on( 'click', '[id^=widget-black-studio-tinymce][id$=tmce]', function() {
			bstw( $( this ) ).setMode( 'visual' );
		});

		// Event handler for html switch button
		$( document ).on( 'click', '[id^=widget-black-studio-tinymce][id$=html]', function() {
			bstw( $( this ) ).setMode( 'html' );
		});

		// Set active editor when clicking on media buttons
		$( document ).on ( 'click.wp-editor', '.wp-editor-wrap', function() {
			if ( this.id ) {
				window.wpActiveEditor = this.id.slice( 3, -5 );
			}
		});

		// Deactivate editor on drag & drop operations
		$( document ).on( 'sortstart',  function( event, ui ) {
			var openWidgetsSelectors;
			if ( ! $( ui.item ).is( '.widget' ) && ! $( ui.item ).is( '.customize-control' ) ) {
				return;
			}
			if ( $( ui.item ).is( '.ui-draggable' ) ) {
				return;
			}
			openWidgetsSelectors = [
				'body.wp-customizer .expanded > div[id*=black-studio-tinymce].widget', // Customizer
				'.widget-liquid-right div[id*=black-studio-tinymce].widget.open' // Widgets page
			];
			$( openWidgetsSelectors.join( ', ' ) ).filter( ':has(.widget-inside:visible)' ).each( function() {
				$( '.widget-title', this ).trigger( 'click' );
				bstw( $( this ) ).deactivate();
			});
			if ( ui.item.is( '[id*=black-studio-tinymce]' ) ) {
				bstw( ui.item.find( 'textarea[id^=widget-black-studio-tinymce]' ) ).deactivate();
			}
		});
		$( document ).on( 'sortupdate',  function( event, ui ) {
			if ( null !== event && ( ! $( ui.item ).is( '.widget' ) || $( ui.item ).is( '.ui-draggable' ) ) ) {
				return;
			}
			$( 'body' ).addClass( 'wait' );
			setTimeout( function() {
				$( 'textarea[id^=widget-black-studio-tinymce].active' ).each( function() {
					bstw( $( this ) ).deactivate();
				});
				$( 'body' ).removeClass( 'wait' );
			}, 1000 );
		});

		// Support for moving widgets in Customizer without drag & drop
		$( document ).on( 'click', 'body.wp-customizer div[id*=black-studio-tinymce].widget .move-widget-btn', function() {
			var $btn = $( this );
			$( 'body' ).addClass( 'wait' );
			setTimeout( function() {
				$( 'textarea[id^=widget-black-studio-tinymce].active' ).each( function() {
					bstw( $( this ) ).deactivate();
				});
				bstw( $btn ).activate();
				$( 'body' ).removeClass( 'wait' );
			}, 1000 );
		});

		// External events
		if ( 'object' === typeof bstwData.activate_events && 0 < bstwData.activate_events.length ) {
			$( document ).on( bstwData.activate_events.join( ' ' ), function( event ) {
				bstw( $( event.target ) ).activate();
			});
		}
		if ( 'object' === typeof bstwData.deactivate_events && 0 < bstwData.deactivate_events.length ) {
			$( document ).on( bstwData.deactivate_events.join( ' ' ), function( event ) {
				bstw( $( event.target ) ).deactivate();
			});
		}

		// Event handler for window resize (needed for responsive behavior)
		$( window ).resize( function() {
			$( 'textarea[id^=widget-black-studio-tinymce]' ).each( function() {
				bstw( $( this ) ).responsive();
			});
		});

		// Event handler for dismission of "Visual Editor disabled" notice
		$( document ).on( 'click', '.bstw-visual-editor-disabled-notice .notice-dismiss', function() {
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'bstw_visual_editor_disabled_dismiss_notice'
				}
			});
		});

		// Deactivate quicktags toolbar on hidden base instance
		$( '#qt_widget-black-studio-tinymce-__i__-text_toolbar' ).remove();

		// Plugin links toggle behavior
		$( document ).on( 'click', '.bstw-links-icon', function( event ) {
			event.preventDefault();
			$( this ).closest( '.bstw-links' ).children( '.bstw-links-list' ).toggle();
		});

		// Populate dummy post ID for embed preview
		if ( 'undefined' !== typeof( wp.media.view.settings.post.id ) && 0 === wp.media.view.settings.post.id ) {
			wp.media.view.settings.post.id = bstwData.dummy_post_id;
		}

	});

}( jQuery ) ); // end self-invoked wrapper function
