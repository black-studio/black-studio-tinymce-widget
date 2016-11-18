<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for Jetpack After the deadline
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugin_Jetpack_After_The_Deadline' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugin_Jetpack_After_The_Deadline {

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
				add_action( 'black_studio_tinymce_load', array( $this, 'after_the_deadline_load' ) );
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
		 * Load Jetpack After the deadline scripts
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function after_the_deadline_load() {
			add_filter( 'atd_load_scripts', '__return_true' );
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugin_Jetpack_After_The_Deadline

} // END class_exists check
