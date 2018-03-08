<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for Page Builder (SiteOrigin Panels)
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugin_Siteorigin_Panels' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugin_Siteorigin_Panels {

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
				add_action( 'admin_init', array( $this, 'disable_compat' ), 7 );
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
		 * Initialize compatibility for Page Builder (SiteOrigin Panels)
		 *
		 * @uses add_filter()
		 * @uses remove_filter()
		 * @uses is_plugin_active()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function admin_init() {
			if ( is_plugin_active( 'siteorigin-panels/siteorigin-panels.php' ) ) {
				add_filter( 'siteorigin_panels_widget_object', array( $this, 'widget_object' ), 10 );
				add_filter( 'black_studio_tinymce_container_selectors', array( $this, 'container_selectors' ) );
				add_filter( 'black_studio_tinymce_activate_events', array( $this, 'activate_events' ) );
				add_filter( 'black_studio_tinymce_deactivate_events', array( $this, 'deactivate_events' ) );
				add_filter( 'black_studio_tinymce_enable_pages', array( $this, 'enable_pages' ) );
				remove_filter( 'widget_text', array( bstw()->text_filters(), 'wpautop' ), 8 );
			}
		}

		/**
		 * Remove widget number to prevent translation when using Page Builder (SiteOrigin Panels) + WPML String Translation
		 *
		 * @param object $the_widget
		 * @return object
		 * @since 2.4.0
		 */
		public function widget_object( $the_widget ) {
			if ( isset( $the_widget->id_base ) && 'black-studio-tinymce' == $the_widget->id_base ) {
				$the_widget->number = '';
			}
			return $the_widget;
		}

		/**
		 * Add selector for widget detection for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $selectors
		 * @return string[]
		 * @since 2.4.0
		 */
		public function container_selectors( $selectors ) {
			$selectors[] = 'div.panel-dialog';
			return $selectors;
		}

		/**
		 * Add activate events for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $events
		 * @return string[]
		 * @since 2.4.0
		 */
		public function activate_events( $events ) {
			$events[] = 'panelsopen';
			return $events;
		}

		/**
		 * Add deactivate events for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $events
		 * @return string[]
		 * @since 2.4.0
		 */
		public function deactivate_events( $events ) {
			$events[] = 'panelsdone';
			return $events;
		}

		/**
		 * Add pages filter to enable editor for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $pages
		 * @return string[]
		 * @since 2.4.0
		 */
		public function enable_pages( $pages ) {
			$pages[] = 'post-new.php';
			$pages[] = 'post.php';
			if ( isset( $_GET['page'] ) && 'so_panels_home_page' == $_GET['page'] ) {
				$pages[] = 'themes.php';
			}
			return $pages;
		}

		/**
		 * Disable old compatibility code provided by Page Builder (SiteOrigin Panels)
		 *
		 * @uses remove_action()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function disable_compat( ) {
			remove_action( 'admin_init', 'siteorigin_panels_black_studio_tinymce_admin_init' );
			remove_action( 'admin_enqueue_scripts', 'siteorigin_panels_black_studio_tinymce_admin_enqueue', 15 );
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugin_Siteorigin_Panels

} // END class_exists check
