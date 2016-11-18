<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that manages compatibility code
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility' ) ) {

	final class Black_Studio_TinyMCE_Compatibility {

		/**
		 * The single instance of the plugin class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $_instance = null;

		/**
		 * Array of compatibility modules class instances
		 *
		 * @var array
		 * @since 2.4.0
		 */
		protected static $modules = null;

		/**
		 * Instance of compatibility class for 3rd party plugins
		 *
		 * @var object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		protected static $plugins = null;

		/**
		 * Instance of compatibility class for WordPress old versions
		 *
		 * @var object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		protected static $wordpress = null;

		/**
		 * Class constructor
		 *
		 * @global object $wp_embed
		 * @since 2.0.0
		 */
		protected function __construct() {
			$this->load_deprecated();
			$this->load_plugins();
			$this->load_wordpress();
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Return the single class instance
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
		 * Return the instance of a compatibility class module, given its slug
		 *
		 * @param string $slug
		 *
		 * @return object
		 * @since 2.4.0
		 */
		public static function module( $slug ) {
			return isset( self::$modules[ $slug ] ) ? self::$modules[ $slug ] : null;
		}

		/**
		 * Return the instance of the compatibility class for 3rd party plugins
		 *
		 * @return object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public static function plugins() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			include_once( self::get_path() . 'class-compatibility-plugins.php' );
			self::$plugins = Black_Studio_TinyMCE_Compatibility_Plugins::instance();
			return self::$plugins;
		}

		/**
		 * Return the instance of the compatibility class for WordPress old versions
		 *
		 * @return object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public static function wordpress() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			if ( version_compare( get_bloginfo( 'version' ), '3.9', '<' ) ) {
				include_once( self::get_path() . 'class-compatibility-wordpress.php' );
				self::$wordpress = Black_Studio_TinyMCE_Compatibility_Wordpress::instance();
			}
			return self::$wordpress;
		}

		/**
		 * Load compatibility code for previous BSTW versions
		 *
		 * @uses apply_filters()
		 * @uses plugin_dir_path()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function load_deprecated() {
			$load_deprecated = apply_filters( 'black_studio_tinymce_load_deprecated', true );
			if ( $load_deprecated ) {
				include_once( plugin_dir_path( dirname( __FILE__ ) ) . 'compat/deprecated.php' );
			}
		}

		/**
		 * Load compatibility code for other plugins
		 *
		 * @uses apply_filters()
		 * @uses plugin_dir_path()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function load_plugins() {
			$compatibility_plugins = array(
				'siteorigin_panels',
				'wpml',
				'jetpack_after_the_deadline',
				'wp_page_widget',
				'nextgen_gallery',
			);
			$compatibility_plugins = apply_filters( 'black_studio_tinymce_load_compatibility_plugins', $compatibility_plugins );
			if ( ! empty( $compatibility_plugins ) ) {
				foreach ( $compatibility_plugins as $plugin ) {
					$this->create_module_instance( 'plugin', $plugin );
				}
			}
		}

		/**
		 * Load compatibility code for previous WordPress versions
		 *
		 * @uses get_bloginfo()
		 * @uses plugin_dir_path()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function load_wordpress() {
			$compatibility_versions = array( '3.2', '3.3', '3.5', '3.9' );
			foreach ( $compatibility_versions as $version ) {
				if ( version_compare( get_bloginfo( 'version' ), $version, '<' ) ) {
					$this->create_module_instance( 'wordpress', 'pre_' . str_replace( '.', '', $version ) );
				}
			}
		}

		/**
		 * Get path for compatibility code files
		 *
		 * @uses plugin_dir_path()
		 *
		 * @return string
		 * @since 2.4.0
		 */
		public static function get_path( $folder = '' ) {
			$path = plugin_dir_path( dirname( __FILE__ ) ) . 'compat/';
			if ( ! empty( $folder ) ) {
				$path .= $folder . '/';
			}
			return $path;
		}

		/**
		 * Get instance of a compatibility module
		 *
		 * @since 2.4.0
		 */
		public static function create_module_instance( $folder, $slug ) {
			$file = self::get_path( $folder ) . 'class-compatibility-' . $folder . '-' . str_replace( '_', '-',  $slug ) . '.php';
			if ( file_exists( $file ) ) {
				include_once( $file );
				$class_name = 'Black_Studio_TinyMCE_Compatibility_' . ucwords( $folder )  . '_';
				$class_name .= str_replace( ' ', '_', ucwords( str_replace( '_', ' ', $slug ) ) );
				self::$modules[ $slug ] = call_user_func( array( $class_name, 'instance' ) );
			}
		}

	} // END class Black_Studio_TinyMCE_Compatibility

} // END class_exists check
