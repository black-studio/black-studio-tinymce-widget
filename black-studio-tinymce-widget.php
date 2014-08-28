<?php
/*
Plugin Name: Black Studio TinyMCE Widget
Plugin URI: https://wordpress.org/plugins/black-studio-tinymce-widget/
Description: Adds a WYSIWYG widget based on the standard TinyMCE WordPress visual editor.
Version: 2.0.0
Author: Black Studio
Author URI: http://www.blackstudio.it
Requires at least: 3.1
Tested up to: 4.0
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
 * @package BlackStudio\TinyMCEWidget
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Plugin' ) ) {

	final class Black_Studio_TinyMCE_Plugin {

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public static $version = '2.0.0';

		/**
		 * The single instance of the class
		 *
		 * @var object
		 */
		protected static $_instance = null;

		/**
		 * Return the main plugin instance
		 *
		 * @return object
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Get plugin version
		 *
		 * @return string
		 */
		public static function get_version() {
			return self::$version;
		}

		/**
		 * Class constructor
		 *
		 * @uses add_action()
		 * @uses add_filter()
		 * @uses get_option()
		 * @uses get_bloginfo()
		 *
		 * @global object $wp_embed
		 * @return void
		 */
		protected function __construct() {
			// Include required file(s)
			include_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp-widget-black-studio-tinymce.php' );
			// Register action and filter hooks
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'plugins_loaded', array( $this, 'compatibility' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_filter( 'black_studio_tinymce_enable', array( $this, 'enable' ) );
			add_filter( '_upload_iframe_src', array( $this, '_upload_iframe_src' ) );
			add_filter( 'wp_default_editor', array( $this, 'editor_accessibility_mode' ) );
			// Support for autoembed urls in widget text
			if ( get_option( 'embed_autourls' ) ) {
				global $wp_embed;
				add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
				add_filter( 'widget_text', array( $wp_embed, 'autoembed' ), 8 );
			}
			// Support for smilies in widget text
			if ( get_option( 'use_smilies' ) ) {
				add_filter( 'widget_text', 'convert_smilies', 12 );
			}
			// Support for shortcodes in widget text
			add_filter( 'widget_text', 'do_shortcode', 15 );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Include compatibility code
		 *
		 * @uses apply_filters()
		 * @uses get_bloginfo()
		 * @uses plugin_dir_path()
		 *
		 * @return void
		 */
		public function compatibility() {
			// Compatibility load flag (for both deprecated functions and other plugins)
			$load_compatibility = apply_filters( 'black_studio_tinymce_load_compatibility', true );
			// Compatibility with previous BSTW versions
			$load_deprecated = apply_filters( 'black_studio_tinymce_load_deprecated', true );
			if ( $load_compatibility && $load_deprecated ) {
				include_once( plugin_dir_path( __FILE__ ) . '/includes/deprecated.php' );
			}
			// Compatibility with other plugins
			$compat_plugins = apply_filters( 'black_studio_tinymce_load_compatibility_plugins', array( 'siteorigin_panels', 'wpml', 'jetpack_after_the_deadline', 'wp_page_widget' ) );
			if ( $load_compatibility && ! empty( $compat_plugins ) ) {
				include_once( plugin_dir_path( __FILE__ ) . '/includes/class-compatibility-plugins.php' );
				new Black_Studio_TinyMCE_Compatibility_Plugins( $compat_plugins );
			}
			// Compatibility with previous WordPress versions
			if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				include_once( plugin_dir_path( __FILE__ ) . '/includes/class-compatibility-wordpress.php' );
				new Black_Studio_TinyMCE_Compatibility_Wordpress( $this );
			}
		}

		/**
		 * Load language files
		 *
		 * @uses load_plugin_textdomain()
		 *
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'black-studio-tinymce-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Widget initialization
		 *
		 * @uses is_blog_installed()
		 * @uses register_widget()
		 *
		 * @return null|void
		 */
		public function widgets_init() {
			if ( ! is_blog_installed() ) {
				return;
			}
			register_widget( 'WP_Widget_Black_Studio_TinyMCE' );
		}

		/**
		 * Add actions and filters (only in widgets admin page)
		 *
		 * @uses apply_filters()
		 * @uses add_action()
		 * @uses add_filter()
		 * @uses do_action()
		 *
		 * @return void
		 */
		public function admin_init() {
			// Load editor features
			$enable = apply_filters( 'black_studio_tinymce_enable', false );
			if ( $enable ) {
				// Add plugin hooks
				add_action( 'admin_head', array( $this, 'enqueue_media' ) );
				add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 20 );
				add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				// Action hook on plugin load
				do_action( 'black_studio_tinymce_load' );
			}
		}

		/**
		 * Check if editor should be loaded
		 *
		 * @global string $pagenow
		 * @param boolean $enable
		 * @return boolean
		 */
		public function enable( $enable ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' || $pagenow == 'customize.php' ) {
				$enable = true;
			}
			return $enable;
		}

		/**
		 * Instantiate tinyMCE editor
		 *
		 * @uses add_thickbox()
		 * @uses wp_enqueue_media()
		 *
		 * @return void
		 */
		public function enqueue_media() {
			// Add support for thickbox media dialog
			add_thickbox();
			// New media modal dialog (WP 3.5+)
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		}

		/**
		 * TinyMCE setup customization
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 */
		public function tiny_mce_before_init( $settings ) {
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

		/**
		 * Enqueue styles
		 *
		 * @uses wp_enqueue_style()
		 * @uses Black_Studio_TinyMCE_Plugin::enqueue_style()
		 *
		 * @return void
		 */
		public function admin_print_styles() {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'editor-buttons' );
			$this->enqueue_style();
		}

		/**
		 * Helper function to enqueue style
		 *
		 * @uses apply_filters()
		 * @uses wp_enqueue_style()
		 * @uses plugins_url()
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 */
		public function enqueue_style() {
			$style = apply_filters( 'black-studio-tinymce-widget-style', 'black-studio-tinymce-widget' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style(
				$style,
				plugins_url( 'css/' . $style . $suffix. '.css', __FILE__ ),
				array(),
				self::$version
			);
		}

		/**
		 * Enqueue header scripts
		 *
		 * @uses wp_enqueue_script()
		 * @uses Black_Studio_TinyMCE_Plugin::enqueue_script()
		 * @uses Black_Studio_TinyMCE_Plugin::localize_script()
		 * @uses do_action()
		 *
		 * @return void
		 */
		public function admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'wplink' );
			wp_enqueue_script( 'wpdialogs-popup' );
			$this->enqueue_script();
			$this->localize_script();
			do_action( 'wp_enqueue_editor', array( 'tinymce' => true ) );
		}

		/**
		 * Helper function to enqueue script
		 *
		 * @uses apply_filters()
		 * @uses wp_enqueue_script()
		 * @uses plugins_url()
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 */
		public function enqueue_script() {
			$script = apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				$script,
				plugins_url( 'js/' . $script . $suffix . '.js', __FILE__ ),
				array( 'jquery', 'editor' ),
				self::$version,
				true
			);
		}

		/**
		 * Helper function to enqueue localized script
		 *
		 * @uses apply_filters()
		 * @uses wp_localize_script()
		 *
		 * @return void
		 */
		public function localize_script() {
			$container_selectors = apply_filters( 'black_studio_tinymce_container_selectors', array(  'div.widget', 'div.widget-inside' ) );
			$data = array(
				'container_selectors' => implode( ', ', $container_selectors ),
				'error_duplicate_id' => __( 'ERROR: Duplicate widget ID detected. To avoid content loss, please create a new widget with the same content and then delete this one.', 'black-studio-tinymce-widget' )
			);
			wp_localize_script( apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' ), 'bstw_data', $data );
		}

		/**
		 * Enqueue footer scripts
		 *
		 * @uses wp_editor()
		 *
		 * @return void
		 */
		public function admin_print_footer_scripts() {
			wp_editor( '', 'black-studio-tinymce-widget' );
		}

		/**
		 * Hack needed to enable full media options when adding content from media library
		 * (this is done excluding post_id parameter in Thickbox iframe url)
		 *
		 * @global string $pagenow
		 * @param string $upload_iframe_src
		 * @return string
		 */
		public function _upload_iframe_src( $upload_iframe_src ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' || ( $pagenow == 'admin-ajax.php' && isset ( $_POST['id_base'] ) && $_POST['id_base'] == 'black-studio-tinymce' ) ) {
				$upload_iframe_src = str_replace( 'post_id=0', '', $upload_iframe_src );
			}
			return $upload_iframe_src;
		}

		/**
		 * Hack for widgets accessibility mode
		 *
		 * @global string $pagenow
		 * @param string $editor
		 * @return string
		 */
		public function editor_accessibility_mode( $editor ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' && isset( $_GET['editwidget'] ) && strpos( $_GET['editwidget'], 'black-studio-tinymce' ) === 0 ) {
				$editor = 'html';
			}
			return $editor;
		}

	} // END class Black_Studio_TinyMCE_Plugin

} // class_exists check

/**
 * Return the main instance to prevent the need to use globals
 *
 * @return object
 */
function bstw() {
	return Black_Studio_TinyMCE_Plugin::instance();
}

/* Create the main instance */
bstw();
