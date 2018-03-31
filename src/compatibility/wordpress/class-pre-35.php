<?php
/**
 * Black Studio TinyMCE Widget - Compatibility with WordPress versions prior to 3.5
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget\Compatibility\WordPress;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Compatibility\\WordPress\\WordPress_Pre_35', false ) ) {

	/**
	 * Class that provides compatibility code for WordPress versions prior to 3.5
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 3.0.0
	 */
	final class Pre_35 {

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
		 * @uses add_action()
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ), 35 );
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
		 * Admin init
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 3.0.0
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
		 * @param string $upload_iframe_src Source of the iframe for the upload dialog.
		 * @return string
		 * @since 3.0.0
		 */
		public function upload_iframe_src( $upload_iframe_src ) {
			global $pagenow;
			$id_base = filter_var( INPUT_POST, 'id_base' );
			if ( 'widgets.php' === $pagenow || ( 'admin-ajax.php' === $pagenow && 'black-studio-tinymce' === $id_base ) ) {
				$upload_iframe_src = str_replace( 'post_id=0', '', $upload_iframe_src );
			}
			return $upload_iframe_src;
		}

	} // END class

} // END class_exists
