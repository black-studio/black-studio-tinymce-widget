<?php

/**
 * Widget class
 *
 * @package Black Studio TinyMCE Widget
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE' ) ) {

	class Black_Studio_TinyMCE {

		/* Class constructor */
		function __construct() {
			// Load localization
			load_plugin_textdomain( 'black-studio-tinymce-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			// Register action and filter hooks
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_filter( 'widget_text', array( $this, 'apply_smilies_to_widget_text' ) );
			add_filter( 'wp_default_editor', array( $this, 'editor_accessibility_mode' ) );
			add_filter( '_upload_iframe_src', array( $this, '_upload_iframe_src' ) );
			add_filter( 'siteorigin_panels_widget_object', array( $this, 'siteorigin_panels_widget_object' ), 10 );
		}

		/* Get plugin version */
		function get_version() {
			$plugin_data = get_plugin_data( __FILE__ );
			$plugin_version = $plugin_data['Version'];
			return $plugin_version;
		}

		/* Widget initialization */
		function widgets_init() {
			if ( ! is_blog_installed() ) {
				return;
			}
			register_widget( 'WP_Widget_Black_Studio_TinyMCE' );
		}

		/* Add actions and filters (only in widgets admin page) */
		function admin_init() {
			// Load editor features
			if ( $this->should_be_loaded ) {
				add_action( 'admin_head', array( $this, 'load_tiny_mce' ) );
				add_filter( 'tiny_mce_before_init', array( $this, 'init_editor' ), 20 );
				add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				add_filter( 'atd_load_scripts', '__return_true' ); // Compatibility with Jetpack After the deadline
			}
		}
		
		/* Check if editor should be loaded */
		function should_be_loaded() {
			global $pagenow;
			$load_editor = false;
			if ( $pagenow == 'widgets.php' || $pagenow == 'customize.php' ) {
				$load_editor = true;
			}
			// Compatibility for WP Page Widget plugin
			if ( is_plugin_active( 'wp-page-widget/wp-page-widgets.php' ) && (
					( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) ||
					( in_array( $pagenow, array( 'edit-tags.php' ) ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) ||
					( in_array( $pagenow, array( 'admin.php' ) ) && isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'pw-front-page', 'pw-search-page' ) ) )
			) ) {
				$load_editor = true;
			}
			return $load_editor;
		}

		/* Instantiate tinyMCE editor */
		function load_tiny_mce() {
			// Add support for thickbox media dialog
			add_thickbox();
			// New media modal dialog (WP 3.5+)
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		}

		/* TinyMCE setup customization */
		function init_editor( $initArray ) {
			global $pagenow;
			// Remove WP fullscreen mode and set the native tinyMCE fullscreen mode
			if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
				$plugins = explode( ',', $initArray['plugins'] );
				if ( isset( $plugins['wpfullscreen'] ) ) {
					unset( $plugins['wpfullscreen'] );
				}
				if ( ! isset( $plugins['fullscreen'] ) ) {
					$plugins[] = 'fullscreen';
				}
				$initArray['plugins'] = implode( ',', $plugins );
			}
			// Remove the "More" toolbar button (only in widget screen)
			if ( $pagenow == 'widgets.php' && version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				$initArray['theme_advanced_buttons1'] = str_replace( ',wp_more', '', $initArray['theme_advanced_buttons1'] );
			}
			// Do not remove linebreaks
			$initArray['remove_linebreaks'] = false;
			// Convert newline characters to BR tags
			$initArray['convert_newlines_to_brs'] = false;
			// Force P newlines
			$initArray['force_p_newlines'] = true;
			// Force no newlines for BR
			$initArray['force_br_newlines'] = false;
			// Do not remove redundant BR tags
			$initArray['remove_redundant_brs'] = false;
			// Force p block
			$initArray['forced_root_block'] = 'p';
			// Apply source formatting
			$initArray['apply_source_formatting '] = true;
			// Add proper newlines to source (i.e. around divs)
			$initArray['indent '] = true;
			// Return modified settings
			return $initArray;
		}

		/* Enqueue styles */
		function admin_print_styles() {
			if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
				wp_enqueue_style( 'thickbox' );
			}
			else {
				wp_enqueue_style( 'wp-jquery-ui-dialog' );
			}
			$style = 'black-studio-tinymce-widget';
			wp_enqueue_style( 'editor-buttons' );
			if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				$style .= '-legacy';
			}
			wp_enqueue_style(
				$style,
				esc_url( BLACK_STUDIO_TINYMCE_WIDGET_URL . 'css/' . $style . '.css' ),
				array(),
				BLACK_STUDIO_TINYMCE_WIDGET_VERSION
			);
		}

		/* Enqueue header scripts */
		function admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			if ( version_compare( get_bloginfo( 'version' ), '3.3', '>=' ) ) {
				$local_data = array( 'error_duplicate_id' => __( 'ERROR: Duplicate widget ID detected. To avoid content loss, please create a new one with the same content and then delete this widget.', 'black-studio-tinymce-widget' ) );
				wp_enqueue_script( 'wplink' );
				wp_enqueue_script( 'wpdialogs-popup' );
				wp_enqueue_script(
					'black-studio-tinymce-widget',
					esc_url( BLACK_STUDIO_TINYMCE_WIDGET_URL . 'js/black-studio-tinymce-widget' . $suffix . '.js' ),
					array( 'jquery', 'editor' ),
					BLACK_STUDIO_TINYMCE_WIDGET_VERSION,
					true
				);
				wp_localize_script( 'black-studio-tinymce-widget', 'black_studio_tinymce_local', $local_data );
				do_action( 'wp_enqueue_editor', array( 'tinymce' => true ) ); // Advanced Image Styles compatibility
			}
			else {
				wp_enqueue_script(
					'black-studio-tinymce-widget-legacy',
					esc_url( BLACK_STUDIO_TINYMCE_WIDGET_URL . 'js/black-studio-tinymce-widget-legacy' . $suffix . '.js' ),
					array( 'jquery', 'editor' ),
					BLACK_STUDIO_TINYMCE_WIDGET_VERSION,
					true
				);
			}
		}

		/* Enqueue footer scripts */
		function admin_print_footer_scripts() {
			// Setup for WP 3.1 and previous versions
			if ( version_compare( get_bloginfo( 'version' ), '3.2', '<' ) ) {
				if ( function_exists( 'wp_tiny_mce' ) ) {
					wp_tiny_mce( false, array() );
				}
				if ( function_exists( 'wp_tiny_mce_preload_dialogs' ) ) {
					wp_tiny_mce_preload_dialogs();
				}
			}
			// Setup for WP 3.2
			else if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
				if ( function_exists( 'wp_tiny_mce' ) ) {
					wp_tiny_mce( false, array() );
				}
				if ( function_exists( 'wp_preload_dialogs' ) ) {
					wp_preload_dialogs( array( 'plugins' => 'wpdialogs,wplink,wpfullscreen' ) );
				}
			}
			// Setup for WP 3.3+ - New Editor API
			else {
				wp_editor( '', 'black-studio-tinymce-widget' );
			}
		}

		/* Support for smilies */
		function apply_smilies_to_widget_text( $text ) {
			if ( get_option( 'use_smilies' ) ) {
				$text = convert_smilies( $text );
			}
			return $text;
		}

		/* Hack needed to enable full media options when adding content from media library */
		/* (this is done excluding post_id parameter in Thickbox iframe url) */
		function _upload_iframe_src( $upload_iframe_src ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' || ( $pagenow == 'admin-ajax.php' && isset ( $_POST['id_base'] ) && $_POST['id_base'] == 'black-studio-tinymce' ) ) {
				$upload_iframe_src = str_replace( 'post_id=0', '', $upload_iframe_src );
			}
			return $upload_iframe_src;
		}

		/* Hack for widgets accessibility mode */
		function editor_accessibility_mode( $editor ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' && isset( $_GET['editwidget'] ) && strpos( $_GET['editwidget'], 'black-studio-tinymce' ) === 0 ) {
				$editor = 'html';
			}
			return $editor;
		}

		/* Hack for compatibility with Page Builder + WPML String Translation */
		function siteorigin_panels_widget_object( $the_widget ) {
			if ( isset($the_widget->id_base) && $the_widget->id_base == 'black-studio-tinymce' ) {
				$the_widget->number = '';
			}
			return $the_widget;
		}

	} // class declaration

} // class_exists check