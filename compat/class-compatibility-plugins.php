<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code with other plugins
 * Starting from version 2.4.0 this class is no longer used, and it will be removed in future version
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 * @deprecated 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugins' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugins {

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
		 * Compatibility with WPML
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wpml() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Add widget text to WPML String translation
		 *
		 * @param mixed[] $instance
		 * @return mixed[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wpml_widget_update( $instance ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $instance;
		}

		/**
		 * Translate widget text
		 *
		 * @param string $text
		 * @param mixed[]|null $instance
		 * @param object|null $widget
		 * @return string
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wpml_widget_text( $text, $instance = null, $widget = null ) {
			_deprecated_function( __FUNCTION__, '2.4.0', 'bstw()->compatibility()->module( \'wpml\' )->widget_text( $text, $instance, $widget )' );
			return bstw()->compatibility()->module( 'wpml' )->widget_text( $text, $instance, $widget );
		}

		/**
		 * Compatibility for WP Page Widget plugin
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_page_widget() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Initialize compatibility for WP Page Widget plugin (only for WordPress 3.3+)
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_page_widget_admin_init() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Enable filter for WP Page Widget plugin
		 *
		 * @param string[] $pages
		 * @return string[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_page_widget_enable_pages( $pages ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $pages;
		}

		/**
		 * Enqueue script for WP Page Widget plugin
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function wp_page_widget_enqueue_script() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Compatibility with Page Builder (SiteOrigin Panels)
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Initialize compatibility for Page Builder (SiteOrigin Panels)
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_admin_init() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Remove widget number to prevent translation when using Page Builder (SiteOrigin Panels) + WPML String Translation
		 *
		 * @param object $the_widget
		 * @return object
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_widget_object( $the_widget ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $the_widget;
		}

		/**
		 * Add selector for widget detection for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $selectors
		 * @return string[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_container_selectors( $selectors ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $selectors;
		}

		/**
		 * Add activate events for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $events
		 * @return string[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_activate_events( $events ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $events;
		}

		/**
		 * Add deactivate events for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $events
		 * @return string[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_deactivate_events( $events ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $events;
		}

		/**
		 * Add pages filter to enable editor for Page Builder (SiteOrigin Panels)
		 *
		 * @param string[] $pages
		 * @return string[]
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_enable_pages( $pages ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
			return $pages;
		}

		/**
		 * Disable old compatibility code provided by Page Builder (SiteOrigin Panels)
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function siteorigin_panels_disable_compat( ) {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Compatibility with Jetpack After the deadline
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function jetpack_after_the_deadline() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

		/**
		 * Load Jetpack After the deadline scripts
		 *
		 * @return void
		 * @since 2.0.0
		 * @deprecated 2.4.0
		 */
		public function jetpack_after_the_deadline_load() {
			_deprecated_function( __FUNCTION__, '2.4.0' );
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugins

} // END class_exists check
