<?php

/**
 * Class that provides compatibility code with other plugins
 *
 * @package Black Studio TinyMCE Widget
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugins' ) ) {

	class Black_Studio_TinyMCE_Compatibility_Plugins {

		/**
		 * Class constructor
		 *
		 * @return void
		 */
		public function __construct() {
			$this->wpml();
			$this->wp_page_widget();
			$this->jetpack_after_the_deadline();
			$this->siteorigin_panels();
		}

		/**
		 * Compatibility with WPML
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 */
		public function wpml() {
			add_filter( 'black_studio_tinymce_widget_update', array( $this, 'wpml_widget_update' ), 10, 2 );
			add_filter( 'widget_text', array( $this, 'wpml_widget_text' ), 5, 3 );
		}

		/**
		 * Add widget text to WPML String translation
		 *
		 * @uses icl_register_string() Part of WPML
		 * @param array $instance
		 * @param object $widget
		 * @return array
		 */
		public function wpml_widget_update( $instance, $widget ) {
			if ( function_exists( 'icl_register_string' ) && ! empty( $widget->number ) ) {
				icl_register_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $instance['text'] );
			}
			return $instance;
		}

		/**
		 * Translate widget text
		 *
		 * @uses icl_t() Part of WPML
		 *
		 * @param string $text
		 * @param array $instance
		 * @param object $widget
		 * @return string
		 */
		public function wpml_widget_text( $text, $instance, $widget ) {
			if ( function_exists( 'icl_t' ) ) {
				if ( ! empty( $instance ) && $widget->id_base == 'black-studio-tinymce' ) {
					$text = icl_t( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $text );
				}
			}
			return $text;
		}

		/**
		 * Compatibility for WP Page Widget plugin
		 *
		 * @uses add_filter
		 *
		 * @return void
		 */
		public function wp_page_widget() {
			add_filter( 'black_studio_tinymce_enable', array( $this, 'wp_page_widget_enable' ) );
		}

		/**
		 * Enable filter for WP Page Widget plugin
		 *
		 * @uses is_plugin_active()
		 *
		 * @global string $pagenow
		 * @param boolean $enable
		 * @return boolean
		 */
		public function wp_page_widget_enable( $enable ) {
			global $pagenow;
			if ( is_plugin_active( 'wp-page-widget/wp-page-widgets.php' ) ) {
				$is_post = in_array( $pagenow, array( 'post-new.php', 'post.php' ) );
				$is_tags = in_array( $pagenow, array( 'edit-tags.php' ) );
				$is_admin = in_array( $pagenow, array( 'admin.php' ) );
				if (
					$is_post ||
					( $is_tags  && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) ||
					( $is_admin && isset( $_GET['page'] ) && in_array( $_GET['page'], array( 'pw-front-page', 'pw-search-page' ) ) )
				) {
					$enable = true;
				}
			}
			return $enable;
		}

		/**
		 * Compatibility with Page Builder
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 */
		public function siteorigin_panels() {
			add_filter( 'siteorigin_panels_widget_object', array( $this, 'siteorigin_panels_widget_object' ), 10 );
			add_filter( 'black_studio_tinymce_container_selectors', array( $this, 'siteorigin_panels_container_selectors' ) );
		}

		/**
		 * Remove widget number to prevent translation when using Page Builder + WPML String Translation
		 *
		 * @param object $the_widget
		 * @return object
		 */
		public function siteorigin_panels_widget_object( $the_widget ) {
			if ( isset($the_widget->id_base) && $the_widget->id_base == 'black-studio-tinymce' ) {
				$the_widget->number = '';
			}
			return $the_widget;
		}

		/**
		 * Add selector for widget detection for Page Builder
		 *
		 * @param array $selectors
		 * @return string
		 */
		public function siteorigin_panels_container_selectors( $selectors ) {
			$selectors[] = 'div.panel-dialog';
			return $selectors;
		}

		/**
		 * Compatibility with Jetpack After the deadline
		 *
		 * @uses add_action()
		 *
		 * @return void
		 */
		public function jetpack_after_the_deadline() {
			add_action( 'black_studio_tinymce_load', array( $this, 'jetpack_after_the_deadline_load' ) );
		}

		/**
		 * Load Jetpack After the deadline scripts
		 *
		 * @uses add_filter()
		 * 
		 * @return void
		 */
		public function jetpack_after_the_deadline_load() {
			add_filter( 'atd_load_scripts', '__return_true' );
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugins

} // class_exists check
