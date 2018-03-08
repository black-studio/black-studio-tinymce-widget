<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility Compatibility with WPML plugins
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugin_Wpml' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugin_Wpml {

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
		 * @uses add_filter()
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'black_studio_tinymce_before_widget', array( $this, 'widget_before' ), 10, 2 );
			add_action( 'black_studio_tinymce_after_widget', array( $this, 'widget_after' ), 10, 2 );
			add_filter( 'black_studio_tinymce_widget_update', array( $this, 'widget_update' ), 10, 2 );
			add_filter( 'widget_text', array( $this, 'widget_text' ), 2, 3 );
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
		 * Initialize compatibility with WPML and WPML Widgets plugins
		 *
		 * @uses is_plugin_active()
		 * @uses has_action()
		 * @uses remove_action()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function init() {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
				if ( false !== has_action( 'update_option_widget_black-studio-tinymce', 'icl_st_update_widget_title_actions' ) ) {
					remove_action( 'update_option_widget_black-studio-tinymce', 'icl_st_update_widget_title_actions', 5 );
				}
			}
		}

		/**
		 * Disable WPML String translation native behavior
		 *
		 * @uses is_plugin_active()
		 * @uses has_filter()
		 * @uses remove_filter()
		 *
		 * @param mixed[] $args
		 * @param mixed[] $instance
		 * @return void
		 * @since 2.4.0
		 */
		public function widget_before( $args, $instance ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				// Avoid native WPML string translation of widget titles 
				// For widgets inserted in pages built with Page Builder (SiteOrigin panels) and also when WPML Widgets is active
				if ( false !== has_filter( 'widget_title', 'icl_sw_filters_widget_title' ) ) {
					if ( isset( $instance['panels_info'] ) || is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
						remove_filter( 'widget_title', 'icl_sw_filters_widget_title', 0 );
					}
				}
				// Avoid native WPML string translation of widget texts (for all widgets) 
				// Black Studio TinyMCE Widget already supports WPML string translation, so this is needed to prevent duplicate translations
				if ( false !== has_filter( 'widget_text', 'icl_sw_filters_widget_text' ) ) {
					remove_filter( 'widget_text', 'icl_sw_filters_widget_text', 0 );
				}
			}
			
		}
		/**
		 * Re-Enable WPML String translation native behavior
		 *
		 * @uses is_plugin_active()
		 * @uses has_filter()
		 * @uses add_filter()
		 *
		 * @param mixed[] $args
		 * @param mixed[] $instance
		 * @return void
		 * @since 2.4.0
		 */
		public function widget_after( $args, $instance ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				if ( false === has_filter( 'widget_title', 'icl_sw_filters_widget_title' ) && function_exists( 'icl_sw_filters_widget_title' ) ) {
					if ( isset( $instance['panels_info'] ) || is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
						add_filter( 'widget_title', 'icl_sw_filters_widget_title', 0 );
					}
				}
				if ( false === has_filter( 'widget_text', 'icl_sw_filters_widget_text' ) && function_exists( 'icl_sw_filters_widget_text' ) ) {
					add_filter( 'widget_text', 'icl_sw_filters_widget_text', 0 );
				}
			}
		}

		/**
		 * Add widget text to WPML String translation
		 *
		 * @uses is_plugin_active()
		 * @uses icl_register_string() (WPML)
		 *
		 * @param mixed[] $instance
		 * @param object $widget
		 * @return mixed[]
		 * @since 2.4.0
		 */
		public function widget_update( $instance, $widget ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
				if ( function_exists( 'icl_register_string' ) && ! empty( $widget->number ) ) {
					if ( ! isset( $instance['panels_info'] ) ) { // Avoid translation of Page Builder (SiteOrigin panels) widgets
						icl_register_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $instance['text'] );
					}
				}
			}
			return $instance;
		}

		/**
		 * Translate widget text
		 *
		 * @uses is_plugin_active()
		 * @uses icl_t() (WPML)
		 *
		 * @param string $text
		 * @param mixed[]|null $instance
		 * @param object|null $widget
		 * @return string
		 * @since 2.4.0
		 */
		public function widget_text( $text, $instance = null, $widget = null ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
				if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
					if ( function_exists( 'icl_t' ) ) {
						// Avoid translation of Page Builder (SiteOrigin panels) widgets
						if ( ! isset( $instance['panels_info'] ) ) { 
							$text = icl_t( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $text );
						}
					}
				}
			}
			return $text;
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugin_Wpml

} // END class_exists check
