<?php
/**
 * Black Studio TinyMCE Widget - Main plugin class
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Plugin', true ) ) {

	/**
	 * Main plugin class
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 2.0.0
	 */
	final class Plugin {

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 2.0.0
		 */
		public static $version = '2.6.2';

		/**
		 * The single instance of the plugin class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $_instance = null;

		/**
		 * Instance of admin class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $admin = null;

		/**
		 * Instance of admin pointer class
		 *
		 * @var object
		 * @since 2.1.0
		 */
		protected static $admin_pointer = null;

		/**
		 * Instance of compatibility class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $compatibility = null;

		/**
		 * Instance of the text filters class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $text_filters = null;

		/**
		 * Return the main plugin instance
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Return the instance of the admin class
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function admin() {
			return self::$admin;
		}

		/**
		 * Return the instance of the admin pointer class
		 *
		 * @return object
		 * @since 2.1.0
		 */
		public static function admin_pointer() {
			return self::$admin_pointer;
		}

		/**
		 * Return the instance of the compatibility class
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function compatibility() {
			return self::$compatibility;
		}

		/**
		 * Return the instance of the text filters class
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function text_filters() {
			return self::$text_filters;
		}

		/**
		 * Get plugin version
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public static function get_version() {
			return self::$version;
		}

		/**
		 * Get plugin basename
		 *
		 * @uses plugin_basename()
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public static function get_basename() {
			return plugin_basename( dirname( __FILE__ ) . '/black-studio-tinymce-widget.php' );
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
		 * @since 2.0.0
		 */
		protected function __construct() {
			if ( is_admin() ) {
				// Instantiate admin classes on admin pages.
				self::$admin         = Admin\Admin::instance();
				self::$admin_pointer = Admin\Admin_Pointer::instance();
			} else {
				// Instantiate text filter class on frontend pages.
				self::$text_filters = Utilities\Text_Filters::instance();
			}
			// Register action and filter hooks.
			add_action( 'plugins_loaded', array( $this, 'load_compatibility' ), 50 );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Load compatibility class
		 *
		 * @uses apply_filters()
		 * @uses get_bloginfo()
		 * @uses plugin_dir_path()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function load_compatibility() {
			// Flag to control load of compatibility code.
			$load_compatibility = apply_filters( 'black_studio_tinymce_load_compatibility', true );
			if ( $load_compatibility ) {
				include_once plugin_dir_path( __DIR__ ) . 'compat/class-black-studio-tinymce-compatibility.php';
				self::$compatibility = Black_Studio_TinyMCE_Compatibility::instance();
			}
		}

		/**
		 * Widget initialization
		 *
		 * @uses is_blog_installed()
		 * @uses register_widget()
		 *
		 * @return null|void
		 * @since 2.0.0
		 */
		public function widgets_init() {
			if ( ! is_blog_installed() ) {
				return;
			}
			register_widget( 'Black_Studio_TinyMCE_Widget\\WP_Widget_Black_Studio_TinyMCE' );
		}

		/**
		 * Check if a widget is a Black Studio Tinyme Widget instance
		 *
		 * @param object $widget Widget instance.
		 * @return boolean
		 * @since 2.0.0
		 */
		public function check_widget( $widget ) {
			return 'object' === gettype( $widget ) && ( 'WP_Widget_Black_Studio_TinyMCE' === get_class( $widget ) || is_subclass_of( $widget, 'WP_Widget_Black_Studio_TinyMCE' ) );
		}

	} // END class
} // END class_exists
