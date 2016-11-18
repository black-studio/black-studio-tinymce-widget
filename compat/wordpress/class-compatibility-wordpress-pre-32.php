<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for WordPress versions prior to 3.2
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_32' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_32 {

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
				add_action( 'admin_init', array( $this, 'admin_init' ), 32 );
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
		 * @uses remove_action()
		 * @uses add_action()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_init() {
			if ( bstw()->admin()->enabled() ) {
				remove_action( 'admin_print_footer_scripts', array( bstw()->admin(), 'admin_print_footer_scripts' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
			}
		}

		/**
		 * Enqueue footer scripts
		 *
		 * @uses wp_tiny_mce()
		 * @uses wp_tiny_mce_preload_dialogs()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function  admin_print_footer_scripts() {
			if ( function_exists( 'wp_tiny_mce' ) ) {
				wp_tiny_mce( false, array() );
			}
			if ( function_exists( 'wp_tiny_mce_preload_dialogs' ) ) {
				wp_tiny_mce_preload_dialogs();
			}
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_32

} // END class_exists check
