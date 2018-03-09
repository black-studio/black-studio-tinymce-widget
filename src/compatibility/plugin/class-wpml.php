<?php
/**
 * Black Studio TinyMCE Widget - Compatibility with WPML plugin(s)
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget\Compatibility\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Compatibility\\Plugin\\Wpml', false ) ) {
	/**
	 * Class that provides compatibility with WPML plugin(s)
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 3.0.0
	 */
	final class Wpml {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 3.0.0
		 */
		protected static $_instance = null;

		/**
		 * Flag to keep track of removed WPML filter on widget title
		 *
		 * @var boolean
		 * @since 3.0.0
		 */
		private $removed_widget_title_filter = false;

		/**
		 * Flag to keep track of removed WPML filter on widget text
		 *
		 * @var boolean
		 * @since 3.0.0
		 */
		private $removed_widget_text_filter = false;

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
		 * @uses add_filter()
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'black_studio_tinymce_before_widget', array( $this, 'widget_before' ), 10, 2 );
			add_action( 'black_studio_tinymce_after_widget', array( $this, 'widget_after' ) );
			add_filter( 'black_studio_tinymce_widget_update', array( $this, 'widget_update' ), 10, 2 );
			add_action( 'black_studio_tinymce_before_editor', array( $this, 'check_deprecated_translations' ), 5, 2 );
			add_filter( 'widget_text', array( $this, 'widget_text' ), 2, 3 );
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
		 * Helper function to get WPML version
		 *
		 * @uses get_plugin_data()
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_version() {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/sitepress.php', false, false );
			return $plugin_data['Version'];
		}

		/**
		 * Initialize compatibility with WPML and WPML Widgets plugins
		 *
		 * @uses is_plugin_active()
		 * @uses has_action()
		 * @uses remove_action()
		 *
		 * @return void
		 * @since 3.0.0
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
		 * @param mixed[] $args     Array of arguments.
		 * @param mixed[] $instance Widget instance.
		 * @return void
		 * @since 3.0.0
		 */
		public function widget_before( $args, $instance ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				/*
				Avoid native WPML string translation of widget titles
				for widgets inserted in pages built with Page Builder (SiteOrigin panels)
				and also when WPML Widgets is active and for WPML versions from 3.8.0 on
				*/
				if ( false !== has_filter( 'widget_title', 'icl_sw_filters_widget_title' ) ) {
					if ( isset( $instance['panels_info'] ) || isset( $instance['wp_page_widget'] ) || is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) || version_compare( $this->get_version(), '3.8.0' ) >= 0 ) {
						remove_filter( 'widget_title', 'icl_sw_filters_widget_title', 0 );
						$this->removed_widget_title_filter = true;
					}
				}

				/*
				Avoid native WPML string translation of widget texts (for all widgets)
				Note: Black Studio TinyMCE Widget already supports WPML string translation,
				so this is needed to prevent duplicate translations
				*/
				if ( false !== has_filter( 'widget_text', 'icl_sw_filters_widget_text' ) ) {
					remove_filter( 'widget_text', 'icl_sw_filters_widget_text', 0 );
					$this->removed_widget_text_filter = true;
				}
			}

		}

		/**
		 * Re-Enable WPML String translation native behavior
		 *
		 * @uses add_filter()
		 * @uses has_filter()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function widget_after() {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
				// Restore widget title's native WPML string translation filter if it was removed.
				if ( $this->removed_widget_title_filter ) {
					if ( false === has_filter( 'widget_title', 'icl_sw_filters_widget_title' ) && function_exists( 'icl_sw_filters_widget_title' ) ) {
						add_filter( 'widget_title', 'icl_sw_filters_widget_title', 0 );
						$this->removed_widget_title_filter = false;
					}
				}
				// Restore widget text's native WPML string translation filter if it was removed.
				if ( $this->removed_widget_text_filter ) {
					if ( false === has_filter( 'widget_text', 'icl_sw_filters_widget_text' ) && function_exists( 'icl_sw_filters_widget_text' ) ) {
						add_filter( 'widget_text', 'icl_sw_filters_widget_text', 0 );
						$this->removed_widget_text_filter = false;
					}
				}
			}
		}

		/**
		 * Add widget text to WPML String translation
		 *
		 * @uses is_plugin_active()
		 * @uses icl_register_string() Part of WPML
		 *
		 * @param mixed[] $instance Array of arguments.
		 * @param object  $widget   Widget instance.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function widget_update( $instance, $widget ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) &&
				version_compare( $this->get_version(), '3.8.0' ) < 0 &&
				! is_plugin_active( 'wpml-widgets/wpml-widgets.php' )
			) {
				if ( function_exists( 'icl_register_string' ) && ! empty( $widget->number ) ) {
					// Avoid translation of Page Builder (SiteOrigin panels) and WP Page Widget widgets.
					if ( ! isset( $instance['panels_info'] ) && ! isset( $instance['wp_page_widget'] ) ) {
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
		 * @uses icl_t() Part of WPML
		 * @uses icl_st_is_registered_string() Part of WPML
		 *
		 * @param string       $text     Widget text.
		 * @param mixed[]|null $instance Widget instance.
		 * @param object|null  $widget   Widget object.
		 * @return string
		 * @since 3.0.0
		 */
		public function widget_text( $text, $instance = null, $widget = null ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && ! is_plugin_active( 'wpml-widgets/wpml-widgets.php' ) ) {
				if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
					if ( function_exists( 'icl_t' ) && function_exists( 'icl_st_is_registered_string' ) ) {
						// Avoid translation of Page Builder (SiteOrigin panels) and WP Page Widget widgets.
						if ( ! isset( $instance['panels_info'] ) && ! isset( $instance['wp_page_widget'] ) ) {
							if ( icl_st_is_registered_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number ) ) {
								$text = icl_t( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number, $text );
							}
						}
					}
				}
			}
			return $text;
		}

		/**
		 * Check for existing deprecated translations (made with WPML String Translations plugin) and display warning
		 *
		 * @uses is_plugin_active()
		 * @uses icl_st_is_registered_string() Part of WPML
		 * @uses admin_url()
		 *
		 * @param mixed[]|null $instance Widget instance.
		 * @param object|null  $widget   Widget object.
		 * @return void
		 * @since 2.6.0
		 */
		public function check_deprecated_translations( $instance, $widget ) {
			if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) && version_compare( $this->get_version(), '3.8.0' ) >= 0 ) {
				if ( function_exists( 'icl_st_is_registered_string' ) ) {
					if ( icl_st_is_registered_string( 'Widgets', 'widget body - ' . $widget->id_base . '-' . $widget->number ) ) {
						$wpml_st_url = admin_url( 'admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=Widgets' );
						echo '<div class="notice notice-warning inline"><p>';
						/* translators: Warning displayed when deprecated translations of the current widget are detected */
						echo sprintf( esc_html__( 'WARNING: This widget has one or more translations made using WPML String Translation, which is now a deprecated translation method. Starting from WPML 3.8 you should replicate this widget for each language and set the "Display on language" dropdown accordingly. Finally, you should delete the previously existing translations from %s.', 'black-studio-tinymce-widget' ), '<a href="' . esc_url( $wpml_st_url ) . '">WPML String Translation</a>' );
						echo '</p></div>';
					}
				}
			}
		}

	} // END class

} // END class_exists
