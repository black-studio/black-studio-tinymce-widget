<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for WordPress versions prior to 3.5
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_35' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_35 {

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
		 * @uses add_action()
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ), 35 );
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
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_init() {
			if ( bstw()->admin()->enabled() ) {
				add_filter( '_upload_iframe_src', array( $this, 'upload_iframe_src' ), 65 );
			}
		}

		/**
		 * Enable full media options in upload dialog
		 * (this is done excluding post_id parameter in Thickbox iframe url)
		 *
		 * @global string $pagenow
		 * @param string $upload_iframe_src
		 * @return string
		 * @since 2.4.0
		 */
		public function upload_iframe_src( $upload_iframe_src ) {
			global $pagenow;
			if ( 'widgets.php' == $pagenow || ( 'admin-ajax.php' == $pagenow && isset( $_POST['id_base'] ) && 'black-studio-tinymce' == $_POST['id_base'] ) ) {
				$upload_iframe_src = str_replace( 'post_id=0', '', $upload_iframe_src );
			}
			return $upload_iframe_src;
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Wordpress_Pre_35

} // END class_exists check
