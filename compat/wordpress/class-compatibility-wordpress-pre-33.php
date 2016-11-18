<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for WordPress versions prior to 3.3 
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_33' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_33 {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 2.4.0
		 */
		protected static $_instance = null;

		/**
		 * Return the single class instance
		 *
		 * @return object
		 * @since 2.4.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Class constructor
		 *
		 * @uses is_admin()
		 * @uses add_action()
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ), 33 );
			}
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.4.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Admin init
		 *
		 * @uses add_filter()
		 * @uses add_action()
		 * @uses remove_action()
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_init() {
			$wp_version = get_bloginfo( 'version' );
			if ( bstw()->admin()->enabled() ) {
				add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 67 );
				add_filter( 'black-studio-tinymce-widget-script', array( $this, 'handle' ), 67 );
				add_filter( 'black-studio-tinymce-widget-style', array( $this, 'handle' ), 67 );
				add_filter( 'black-studio-tinymce-widget-script-path', array( $this, 'path' ), 67 );
				add_filter( 'black-studio-tinymce-widget-style-path', array( $this, 'path' ), 67 );
				remove_action( 'admin_print_styles', array( bstw()->admin(), 'admin_print_styles' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
				remove_action( 'admin_print_scripts', array( bstw()->admin(), 'admin_print_scripts' ) );
				add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
				remove_action( 'admin_print_footer_scripts', array( bstw()->admin(), 'admin_print_footer_scripts' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				remove_action( 'admin_print_scripts', array( bstw()->admin(), 'pointer_load' ) );
				remove_filter( 'black_studio_tinymce_admin_pointers-widgets', array( bstw()->admin(), 'pointer_register' ) );
			}
		}

		/**
		 * Remove WP fullscreen mode and set the native TinyMCE fullscreen mode
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 * @since 2.4.0
		 */
		public function tiny_mce_before_init( $settings ) {
			$plugins = explode( ',', $settings['plugins'] );
			if ( isset( $plugins['wpfullscreen'] ) ) {
				unset( $plugins['wpfullscreen'] );
			}
			if ( ! isset( $plugins['fullscreen'] ) ) {
				$plugins[] = 'fullscreen';
			}
			$settings['plugins'] = implode( ',', $plugins );
			return $settings;
		}

		/**
		 * Enqueue styles
		 *
		 * @uses wp_enqueue_style()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_print_styles() {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'editor-buttons' );
			bstw()->admin()->enqueue_style();
		}

		/**
		 * Enqueue header scripts
		 *
		 * @uses wp_enqueue_script()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			bstw()->admin()->enqueue_script();
			bstw()->admin()->localize_script();
		}

		/**
		 * Filter to enqueue style / script
		 *
		 * @return string
		 * @since 2.4.0
		 */
		public function handle() {
			return 'black-studio-tinymce-widget-pre33';
		}

		/**
		 * Filter for styles / scripts path
		 *
		 * @param string $path
		 *
		 * @return string
		 * @since 2.4.0
		 */
		public function path( $path ) {
			return 'compat/wordpress/' . $path;
		}

		/**
		 * Enqueue footer scripts
		 *
		 * @uses wp_tiny_mce()
		 * @uses wp_preload_dialog()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_print_footer_scripts() {
			if ( function_exists( 'wp_tiny_mce' ) ) {
				wp_tiny_mce( false, array() );
			}
			if ( function_exists( 'wp_preload_dialogs' ) ) {
				wp_preload_dialogs( array( 'plugins' => 'wpdialogs,wplink,wpfullscreen' ) );
			}
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_33

} // END class_exists check
