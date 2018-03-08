<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for WP Page Widget plugin
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugin_Wp_Page_Widget' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugin_Wp_Page_Widget {

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
				add_action( 'admin_init', array( $this, 'admin_init' ) );
			}
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
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
		 * Initialize compatibility for WP Page Widget plugin (only for WordPress 3.3+)
		 *
		 * @uses add_filter()
		 * @uses add_action()
		 * @uses is_plugin_active()
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_init() {
			if ( is_plugin_active( 'wp-page-widget/wp-page-widgets.php' ) && version_compare( get_bloginfo( 'version' ), '3.3', '>=' ) ) {
				add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'enable_pages' ) );
				add_action( 'admin_print_scripts', array( $this, 'enqueue_script' ) );
			}
		}

		/**
		 * Enable filter for WP Page Widget plugin
		 *
		 * @param string[] $pages
		 * @return string[]
		 * @since 2.4.0
		 */
		public function enable_pages( $pages ) {
			$pages[] = 'post-new.php';
			$pages[] = 'post.php';
			if ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) {
				$pages[] = 'edit-tags.php';
			}
			if ( isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'pw-front-page', 'pw-search-page' ) ) ) {
				$pages[] = 'admin.php';
			}
			return $pages;
		}

		/**
		 * Enqueue script for WP Page Widget plugin
		 *
		 * @uses apply_filters()
		 * @uses wp_enqueue_script()
		 * @uses plugins_url()
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function enqueue_script() {
			$main_script = apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' );
			$compat_script = 'wp-page-widget';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';
			wp_enqueue_script(
				$compat_script,
				plugins_url( 'plugin/js/' . $compat_script . $suffix, dirname( __FILE__ ) ),
				array( 'jquery', 'editor', 'quicktags', $main_script ),
				bstw()->get_version(),
				true
			);
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugin_Wp_Page_Widget

} // END class_exists check
