<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility Compatibility with WPML plugins
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.3.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugin_Wpml' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugin_Wpml {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 2.3.0
		 */
		protected static $_instance = null;

		/**
		 * Return the single class instance
		 *
		 * @param string[] $plugins
		 * @return object
		 * @since 2.3.0
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
		 * @uses add_filter()
		 *
		 * @since 2.3.0
		 */
		protected function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
			}
			add_filter( 'widget_text', array( $this, 'widget_text' ), 2, 3 );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.3.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Initialize compatibility for Page Builder (SiteOrigin Panels)
		 *
		 * @uses is_plugin_active()
		 * @uses add_filter()
		 * @uses remove_action()
		 *
		 * @return void
		 * @since 2.3.0
		 */
		public function admin_init() {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				// If WPML widgets plugin is not active add widget body to WPML String Translation
				if( ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
					add_filter( 'black_studio_tinymce_widget_update', array( $this, 'widget_update' ), 10, 2 );
				}
				// If WPML widgets plugin is active remove widget title from WPML String Translation
				else if ( is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
					remove_action( 'update_option_sidebars_widgets', '__icl_st_init_register_widget_titles' );
					remove_action( 'update_option_widget_black-studio-tinymce', 'icl_st_update_widget_title_actions', 5 );
				}
			}
		}

		/**
		 * Add widget text to WPML String translation
		 *
		 * @uses icl_register_string() (Part of WPML)
		 *
		 * @param mixed[] $instance
		 * @param object $widget
		 * @return mixed[]
		 * @since 2.3.0
		 */
		public function widget_update( $instance, $widget ) {
			if ( function_exists( 'icl_register_string' ) && ! empty( $widget->number ) ) {
				icl_register_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $instance['text'] );
			}
			return $instance;
		}

		/**
		 * Translate widget text
		 *
		 * @uses is_plugin_active()
		 * @uses icl_t() (Part of WPML)
		 *
		 * @param string $text
		 * @param mixed[]|null $instance
		 * @param object|null $widget
		 * @return string
		 * @since 2.3.0
		 */
		public function widget_text( $text, $instance = null, $widget = null ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
					if ( function_exists( 'icl_t' ) ) {
						if ( ! isset( $instance['panels_info'] ) ) { // Avoid translation of Page Builder (SiteOrigin panels) widgets
							$text = icl_t( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $text );
						}
					}
				}
			}
			return $text;
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugin_Wpml

} // END class_exists check
