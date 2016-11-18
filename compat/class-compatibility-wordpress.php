<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code with older WordPress versions
 * Starting from version 2.4.0 this class is no longer used, and it will be removed in future version
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 * @deprecated 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Wordpress' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Wordpress {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		protected static $_instance = null;

		/**
		 * Return the single class instance
		 *
		 * @return object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public static function instance() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Class constructor
		 *
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		protected function __construct() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		protected function __clone() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Compatibility for WordPress prior to 3.2
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_32() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Enqueue footer scripts for WordPress prior to 3.2
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function  wp_pre_32_admin_print_footer_scripts() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Compatibility for WordPress prior to 3.3
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_33() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Remove WP fullscreen mode and set the native tinyMCE fullscreen mode for WordPress prior to 3.3
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_33_tiny_mce_before_init( $settings ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $settings;
		}

		/**
		 * Enqueue styles for WordPress prior to 3.3
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_33_admin_print_styles() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Enqueue header scripts for WordPress prior to 3.3
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_33_admin_print_scripts() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Filter to enqueue style / script for WordPress prior to 3.3
		 *
		 * @return string
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_33_handle() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return 'black-studio-tinymce-widget-pre33';
		}

		/**
		 * Enqueue footer scripts for WordPress prior to 3.3
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_33_admin_print_footer_scripts() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Compatibility for WordPress prior to 3.5
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_35() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Enable full media options in upload dialog for WordPress prior to 3.5
		 * (this is done excluding post_id parameter in Thickbox iframe url)
		 *
		 * @param string $upload_iframe_src
		 * @return string
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_35_upload_iframe_src( $upload_iframe_src ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $upload_iframe_src;
		}

		/**
		 * Compatibility for WordPress prior to 3.9
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_39() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Filter to enqueue style / script for WordPress prior to 3.9
		 *
		 * @return string
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_39_handle() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return 'black-studio-tinymce-widget-pre39';
		}

		/**
		 * TinyMCE initialization for WordPress prior to 3.9
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_39_tiny_mce_before_init( $settings ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $settings;
		}

		/**
		 * Enqueue footer scripts for WordPress prior to 3.9
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_39_admin_print_footer_scripts() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Output the visual editor code for WordPress prior to 3.9
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_pre_39_editor() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Wordpress

} // END class_exists check
