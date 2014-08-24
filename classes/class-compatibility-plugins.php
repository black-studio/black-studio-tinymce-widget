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

		/* Class constructor */
		public function __construct() {
			// Call compatibility methods
			$this->wp_page_widget();
			$this->jetpack_after_the_deadline();
			$this->siteorigin_panels();
		}

		/* Compatibility for WP Page Widget plugin */
		public function wp_page_widget() {
			add_filter( 'black_studio_tinymce_enable', array( $this, 'wp_page_widget_enable' ) );
		}

		/* Enable filter for WP Page Widget plugin */
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

		/* Compatibility with Page Builder + WPML String Translation */
		public function siteorigin_panels() {
			add_filter( 'siteorigin_panels_widget_object', array( $this, 'siteorigin_panels_widget_object' ), 10 );
		}

		/* Remove widget number to prevent translation when using Page Builder + WPML String Translation */
		public function siteorigin_panels_widget_object( $the_widget ) {
			if ( isset($the_widget->id_base) && $the_widget->id_base == 'black-studio-tinymce' ) {
				$the_widget->number = '';
			}
			return $the_widget;
		}

		/* Compatibility with Jetpack After the deadline */
		public function jetpack_after_the_deadline() {
			add_action( 'black_studio_tinymce_load', array( $this, 'jetpack_after_the_deadline_load' ) );
		}

		/* Load Jetpack After the deadline scripts */
		public function jetpack_after_the_deadline_load() {
			add_filter( 'atd_load_scripts', '__return_true' );
		}

	} // class declaration

} // class_exists check
