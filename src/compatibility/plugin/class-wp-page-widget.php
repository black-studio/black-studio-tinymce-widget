<?php
/**
 * Black Studio TinyMCE Widget - Compatibility with WP Page Widget plugin
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget\Compatibility\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Compatibility\\Plugin\\Wp_Page_Widget', false ) ) {

	/**
	 * Class that provides compatibility code for WP Page Widget plugin
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 3.0.0
	 */
	final class Wp_Page_Widget {

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
		 * @uses add_action()
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
			}
			if ( ! function_exists( 'is_plugin_active' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
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
		 * Initialize compatibility for WP Page Widget plugin (only for WordPress 3.3+)
		 *
		 * @uses add_filter()
		 * @uses add_action()
		 * @uses is_plugin_active()
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function admin_init() {
			if ( is_plugin_active( 'wp-page-widget/wp-page-widgets.php' ) && version_compare( get_bloginfo( 'version' ), '3.3', '>=' ) ) {
				add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'enable_pages' ) );
				add_action( 'admin_print_scripts', array( $this, 'enqueue_script' ) );
				add_filter( 'black_studio_tinymce_widget_update', array( $this, 'add_data' ), 10, 2 );
			}
		}

		/**
		 * Enable filter for WP Page Widget plugin
		 *
		 * @param string[] $pages Array of pages.
		 * @return string[]
		 * @since 3.0.0
		 */
		public function enable_pages( $pages ) {
			$action  = filter_input( INPUT_GET, 'action' );
			$page    = filter_input( INPUT_GET, 'page' );
			$pages[] = 'post-new.php';
			$pages[] = 'post.php';
			if ( 'edit' === $action ) {
				$pages[] = 'edit-tags.php';
			}
			if ( in_array( $page, array( 'pw-front-page', 'pw-search-page' ), true ) ) { // Input var ok.
				$pages[] = 'admin.php';
			}
			return $pages;
		}

		/**
		 * Add WP Page Widget marker
		 *
		 * @param mixed[] $instance Widget instance.
		 * @param object  $widget   Widget object.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function add_data( $instance, $widget ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$action = filter_input( INPUT_POST, 'action' );
				if ( 'pw-save-widget' === $action ) {
					$instance['wp_page_widget'] = true;
				}
			}
			return $instance;
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
		 * @since 3.0.0
		 */
		public function enqueue_script() {
			$main_script   = apply_filters( 'black_studio_tinymce_widget_script', 'black-studio-tinymce-widget' );
			$compat_script = 'js/compatibility/plugin/wp-page-widget';
			$suffix        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';
			wp_enqueue_script(
				$compat_script,
				plugins_url( $compat_script . $suffix, dirname( dirname( dirname( __FILE__ ) ) ) ),
				array( 'jquery', 'editor', 'quicktags', $main_script ),
				bstw()->get_version(),
				true
			);
		}

	} // END class

} // END class_exists
