<?php
/**
 * Black Studio TinyMCE Widget - Compatibility with Elementor plugin
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget\Compatibility\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Compatibility\\Plugin\\Elementor', false ) ) {

	/**
	 * Class that provides compatibility code for Elementor
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 3.0.0
	 */
	final class Elementor {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 3.0.0
		 */
		protected static $_instance = null;

		/**
		 * Return the single class instance
		 *
		 * @return object
		 * @since 3.0.0
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
		 * @uses add_filter()
		 * @uses add_action()
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			$action = filter_input( INPUT_GET, 'action' );
			if ( is_admin() && 'elementor' === $action ) {
				add_filter( 'black_studio_tinymce_enable', '__return_false', 100 );
				add_action( 'widgets_init', array( $this, 'unregister_widget' ), 20 );
			}
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 3.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; uh?' ), '3.0' );
		}
		/**
		 * Unregister Widget for Elementor plugin
		 *
		 * @uses unregister_widget()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function unregister_widget() {
			unregister_widget( 'WP_Widget_Black_Studio_TinyMCE' );
		}

	} // END class

} // END class_exists
