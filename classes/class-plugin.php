<?php

/**
 * Main plugin class
 *
 * @package Black Studio TinyMCE Widget
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Plugin' ) ) {

	class Black_Studio_TinyMCE_Plugin {

		/* Class constructor */
		function __construct() {
			// Load localization
			load_plugin_textdomain( 'black-studio-tinymce-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			// Register action and filter hooks
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_filter( 'black_studio_tinymce_enable', array( $this, 'enable' ) );
			add_filter( 'widget_text', array( $this, 'apply_smilies_to_widget_text' ) );
			add_filter( 'wp_default_editor', array( $this, 'editor_accessibility_mode' ) );
			add_filter( '_upload_iframe_src', array( $this, '_upload_iframe_src' ) );
			// Handle compatibility code
			$compat_wordpress = new Black_Studio_TinyMCE_Compatibility_Wordpress( $this );
			$compat_plugins = new Black_Studio_TinyMCE_Compatibility_Plugins( $this );
		}

		/* Get plugin version */
		function get_version() {
			return BLACK_STUDIO_TINYMCE_WIDGET_VERSION;
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
			$enable = apply_filters( 'black_studio_tinymce_enable', false );
			if ( $enable ) {
				add_action( 'admin_head', array( $this, 'load_tiny_mce' ) );
				add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 20 );
				add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				do_action( 'black_studio_tinymce_load' );
			}
		}

		/* Check if editor should be loaded */
		function enable( $enable ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' || $pagenow == 'customize.php' ) {
				$enable = true;
			}
			return $enable;
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
		function tiny_mce_before_init( $settings ) {
			global $pagenow;
			// Remove the "More" toolbar button (only in widget screen)
			if ( $pagenow == 'widgets.php' && version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				$settings['theme_advanced_buttons1'] = str_replace( ',wp_more', '', $settings['theme_advanced_buttons1'] );
			}
			$custom_settings = array(
				'remove_linebreaks' => false,
				'convert_newlines_to_brs' => false,
				'force_p_newlines' => true,
				'force_br_newlines' => false,
				'remove_redundant_brs' => false,
				'forced_root_block' => 'p',
				'apply_source_formatting ' => true,
				'indent' => true
			);
			// Return modified settings
			return array_merge( $settings, $custom_settings );
		}

		/* Enqueue styles */
		function admin_print_styles() {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'editor-buttons' );
			$style = apply_filters( 'black-studio-tinymce-widget-style', 'black-studio-tinymce-widget' );
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
			do_action( 'wp_enqueue_editor', array( 'tinymce' => true ) );
		}

		/* Enqueue footer scripts */
		function admin_print_footer_scripts() {
			wp_editor( '', 'black-studio-tinymce-widget' );
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


	} // class declaration

} // class_exists check
