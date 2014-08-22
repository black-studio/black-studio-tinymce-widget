<?php
/*
Plugin Name: Black Studio TinyMCE Widget
Plugin URI: http://wordpress.org/extend/plugins/black-studio-tinymce-widget/
Description: Adds a WYSIWYG widget based on the standard TinyMCE WordPress visual editor.
Version: 2.0.0
Author: Black Studio
Author URI: http://www.blackstudio.it
License: GPLv3
Text Domain: black-studio-tinymce-widget
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 *
 * @package Black Studio TinyMCE Widget
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Plugin' ) ) {

	final class Black_Studio_TinyMCE_Plugin {
		
		/* Plugin version*/
		public $version = '2.0.0';

		/* The single instance of the class */		
		protected static $_instance = null;

		/* Return the main plugin instance */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/* Class constructor */
		public function __construct() {
			// Include required files
			$this->includes();
			// Load localization
			load_plugin_textdomain( 'black-studio-tinymce-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			// Register action and filter hooks
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_filter( 'black_studio_tinymce_enable', array( $this, 'enable' ) );
			add_filter( 'widget_text', array( $this, 'apply_smilies_to_widget_text' ) );
			add_filter( '_upload_iframe_src', array( $this, '_upload_iframe_src' ) );
			add_filter( 'wp_default_editor', array( $this, 'editor_accessibility_mode' ) );
			// Handle compatibility code
			new Black_Studio_TinyMCE_Compatibility_Wordpress( $this );
			new Black_Studio_TinyMCE_Compatibility_Plugins();
		}

		/* Get plugin version */
		function get_version() {
			return $this->version;
		}

		/* Include additional files */
		private function includes() {
			include_once( plugin_dir_path( __FILE__ ) . '/classes/class-wp-widget-black-studio-tinymce.php' );
			include_once( plugin_dir_path( __FILE__ ) . '/classes/class-compatibility-wordpress.php' );
			include_once( plugin_dir_path( __FILE__ ) . '/classes/class-compatibility-plugins.php' );
			include_once( plugin_dir_path( __FILE__ ) . '/includes/deprecated.php' );
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
			$custom_settings = array(
				'remove_linebreaks' => false,
				'convert_newlines_to_brs' => false,
				'force_p_newlines' => true,
				'force_br_newlines' => false,
				'remove_redundant_brs' => false,
				'forced_root_block' => 'p',
				'apply_source_formatting ' => true,
				'indent' => true,
			);
			// Return modified settings
			return array_merge( $settings, $custom_settings );
		}

		/* Enqueue styles */
		function admin_print_styles() {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'editor-buttons' );
			$this->enqueue_style();
		}

		/* Helper function to enqueue style */
		function enqueue_style() {
			$style = apply_filters( 'black-studio-tinymce-widget-style', 'black-studio-tinymce-widget' );
			wp_enqueue_style(
				$style,
				plugins_url( 'css/' . $style . '.css', __FILE__ ),
				array(),
				$this->version
			);
		}

		/* Enqueue header scripts */
		function admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'wplink' );
			wp_enqueue_script( 'wpdialogs-popup' );
			$this->enqueue_script();
			$this->localize_script();
			do_action( 'wp_enqueue_editor', array( 'tinymce' => true ) );
		}

		/* Helper function to enqueue script */
		function enqueue_script() {
			$script = apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				$script,
				plugins_url( 'js/' . $script . $suffix . '.js', __FILE__ ),
				array( 'jquery', 'editor' ),
				$this->version,
				true
			);
		}

		/* Helper function to enqueue localized script */
		function localize_script() {
			$local_data = array( 'error_duplicate_id' => __( 'ERROR: Duplicate widget ID detected. To avoid content loss, please create a new one with the same content and then delete this widget.', 'black-studio-tinymce-widget' ) );
			wp_localize_script( 'black-studio-tinymce-widget', 'black_studio_tinymce_local', $local_data );
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

/* Return the main instance to prevent the need to use globals */
function BSTW() {
	return Black_Studio_TinyMCE_Plugin::instance();
}

/* Create the main instance */
BSTW();
