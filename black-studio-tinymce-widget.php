<?php
/*
Plugin Name: Black Studio TinyMCE Widget
Plugin URI: https://wordpress.org/plugins/black-studio-tinymce-widget/
Description: Adds a WYSIWYG widget based on the standard TinyMCE WordPress visual editor.
Version: 2.0.0
Author: Black Studio
Author URI: http://www.blackstudio.it
Requires at least: 3.1
Tested up to: 4.0
License: GPLv3
Text Domain: black-studio-tinymce-widget
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Plugin' ) ) {

	final class Black_Studio_TinyMCE_Plugin {

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 2.0.0
		 */
		public static $version = '2.0.0';

		/**
		 * The single instance of the plugin class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $_instance = null;

		/**
		 * Instance of compatibility class for 3rd party plugins
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $compat_plugins = null;

		/**
		 * Instance of compatibility class for WordPress old versions
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $compat_wordpress = null;

		/**
		 * Return the main plugin instance
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Return the instance of the compatibility class for 3rd party plugins
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function compat_plugins() {
			return self::$compat_plugins;
		}

		/**
		 * Return the instance of the compatibility class for WordPress old versions
		 *
		 * @return object
		 * @since 2.0.0
		 */
		public static function compat_wordpress() {
			return self::$compat_wordpress;
		}

		/**
		 * Get plugin version
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public static function get_version() {
			return self::$version;
		}

		/**
		 * Class constructor
		 *
		 * @uses add_action()
		 * @uses add_filter()
		 * @uses get_option()
		 * @uses get_bloginfo()
		 *
		 * @global object $wp_embed
		 * @return void
		 * @since 2.0.0
		 */
		protected function __construct() {
			// Include required file(s)
			include_once( plugin_dir_path( __FILE__ ) . '/includes/class-wp-widget-black-studio-tinymce.php' );
			// Register action and filter hooks
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'plugins_loaded', array( $this, 'compatibility' ), 20 );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			add_filter( 'wp_default_editor', array( $this, 'editor_accessibility_mode' ) );
			// Support for autoembed urls in widget text
			if ( get_option( 'embed_autourls' ) ) {
				add_filter( 'widget_text', array( $this, 'widget_text_autoembed' ), 10, 3 );
			}
			// Support for smilies in widget text
			if ( get_option( 'use_smilies' ) ) {
				add_filter( 'widget_text', array( $this, 'widget_text_convert_smilies' ), 20, 3 );
			}
			// Support for wpautop in widget text
			add_filter( 'widget_text', array( $this, 'widget_text_wpautop' ), 30, 3 );
			// Support for shortcodes in widget text
			add_filter( 'widget_text', array( $this, 'widget_text_do_shortcode' ), 40, 3 );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Include compatibility code
		 *
		 * @uses apply_filters()
		 * @uses get_bloginfo()
		 * @uses plugin_dir_path()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function compatibility() {
			// Compatibility load flag (for both deprecated functions and code for compatibility with other plugins)
			$load_compatibility = apply_filters( 'black_studio_tinymce_load_compatibility', true );
			// Compatibility with previous BSTW versions
			$load_deprecated = apply_filters( 'black_studio_tinymce_load_deprecated', true );
			if ( $load_compatibility && $load_deprecated ) {
				include_once( plugin_dir_path( __FILE__ ) . '/includes/deprecated.php' );
			}
			// Compatibility with other plugins
			$compat_plugins = apply_filters( 'black_studio_tinymce_load_compatibility_plugins', array( 'siteorigin_panels', 'wpml', 'jetpack_after_the_deadline', 'wp_page_widget' ) );
			if ( $load_compatibility && ! empty( $compat_plugins ) ) {
				include_once( plugin_dir_path( __FILE__ ) . '/includes/class-compatibility-plugins.php' );
				self::$compat_plugins = Black_Studio_TinyMCE_Compatibility_Plugins::instance( $compat_plugins );
			}
			// Compatibility with previous WordPress versions
			if ( version_compare( get_bloginfo( 'version' ), '3.9', '<' ) ) {
				include_once( plugin_dir_path( __FILE__ ) . '/includes/class-compatibility-wordpress.php' );
				self::$compat_wordpress = Black_Studio_TinyMCE_Compatibility_Wordpress::instance();
			}
		}

		/**
		 * Load language files
		 *
		 * @uses load_plugin_textdomain()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function load_textdomain() {
			load_plugin_textdomain( 'black-studio-tinymce-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Widget initialization
		 *
		 * @uses is_blog_installed()
		 * @uses register_widget()
		 *
		 * @return null|void
		 * @since 2.0.0
		 */
		public function widgets_init() {
			if ( ! is_blog_installed() ) {
				return;
			}
			register_widget( 'WP_Widget_Black_Studio_TinyMCE' );
		}

		/**
		 * Checks if the plugin admin code should be loaded
		 *
		 * @uses apply_filters()
		 *
		 * @global string $pagenow
		 * @return void
		 * @since 2.0.0
		 */
		public function enabled() {
			global $pagenow;
			$enabled_pages = apply_filters( 'black_studio_tinymce_enable_pages', array( 'widgets.php', 'customize.php', 'admin-ajax.php' ) );
			return apply_filters( 'black_studio_tinymce_enable', in_array( $pagenow, $enabled_pages ) );
		}

		/**
		 * Add actions and filters (only in widgets admin page)
		 *
		 * @uses apply_filters()
		 * @uses add_action()
		 * @uses add_filter()
		 * @uses do_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function admin_init() {
			if ( $this->enabled() ) {
				// Add plugin hooks
				add_action( 'admin_head', array( $this, 'enqueue_media' ) );
				add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				add_action( 'black_studio_tinymce_editor', array( $this, 'editor' ), 10, 3 );
				add_action( 'black_studio_tinymce_after_editor', array( $this, 'links' ) );
				// Action hook on plugin load
				do_action( 'black_studio_tinymce_load' );
			}
		}

		/**
		 * Instantiate tinyMCE editor
		 *
		 * @uses add_thickbox()
		 * @uses wp_enqueue_media()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function enqueue_media() {
			// Add support for thickbox media dialog
			add_thickbox();
			// New media modal dialog (WP 3.5+)
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		}

		/**
		 * TinyMCE setup customization
		 * This method is deprecated but it is kept for compatibility reasons as it was present in 2.0.0 pre-release
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 * @since 2.0.0
		 * @deprecated 2.0.0
		 */
		public function tiny_mce_before_init( $settings ) {
			_deprecated_function( __FUNCTION__, '2.0.0' );
			return $settings;
		}

		/**
		 * Enqueue styles
		 *
		 * @uses wp_enqueue_style()
		 * @uses Black_Studio_TinyMCE_Plugin::enqueue_style()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function admin_print_styles() {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );
			wp_enqueue_style( 'editor-buttons' );
			$this->enqueue_style();
		}

		/**
		 * Helper function to enqueue style
		 *
		 * @uses apply_filters()
		 * @uses wp_enqueue_style()
		 * @uses plugins_url()
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function enqueue_style() {
			$style = apply_filters( 'black-studio-tinymce-widget-style', 'black-studio-tinymce-widget' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_style(
				$style,
				plugins_url( 'css/' . $style . $suffix. '.css', __FILE__ ),
				array(),
				self::$version
			);
		}

		/**
		 * Enqueue header scripts
		 *
		 * @uses wp_enqueue_script()
		 * @uses Black_Studio_TinyMCE_Plugin::enqueue_script()
		 * @uses Black_Studio_TinyMCE_Plugin::localize_script()
		 * @uses do_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'wplink' );
			wp_enqueue_script( 'wpdialogs-popup' );
			$this->enqueue_script();
			$this->localize_script();
			do_action( 'wp_enqueue_editor', array( 'tinymce' => true ) );
		}

		/**
		 * Helper function to enqueue script
		 *
		 * @uses apply_filters()
		 * @uses wp_enqueue_script()
		 * @uses plugins_url()
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function enqueue_script() {
			$script = apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				$script,
				plugins_url( 'js/' . $script . $suffix . '.js', __FILE__ ),
				array( 'jquery', 'editor', 'quicktags' ),
				self::$version,
				true
			);
		}

		/**
		 * Helper function to enqueue localized script
		 *
		 * @uses apply_filters()
		 * @uses wp_localize_script()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function localize_script() {
			$container_selectors = apply_filters( 'black_studio_tinymce_container_selectors', array(  'div.widget', 'div.widget-inside' ) );
			$activate_events = apply_filters( 'black_studio_tinymce_activate_events', array() );
			$deactivate_events = apply_filters( 'black_studio_tinymce_deactivate_events', array() );
			$data = array(
				'container_selectors' => implode( ', ', $container_selectors ),
				'activate_events' => $activate_events,
				'deactivate_events' => $deactivate_events,
				/* translators: error message shown when a duplicated widget ID is detected */
				'error_duplicate_id' => __( 'ERROR: Duplicate widget ID detected. To avoid content loss, please create a new widget with the same content and then delete this one.', 'black-studio-tinymce-widget' )
			);
			wp_localize_script( apply_filters( 'black-studio-tinymce-widget-script', 'black-studio-tinymce-widget' ), 'bstw_data', $data );
		}

		/**
		 * Enqueue footer scripts
		 *
		 * @uses wp_editor()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function admin_print_footer_scripts() {
			$this->editor( '', 'black-studio-tinymce-widget', 'black-studio-tinymce-widget' );
		}

		/**
		 * Output the visual editor code
		 *
		 * @uses wp_editor()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function editor( $text, $id, $name = '' ) {
			$editor_settings = array(
				'default_editor' => 'tmce',
				'tinymce' => array( 'wp_skip_init' => true ),
				'textarea_name' => $name,
				'editor_height' => 350,
			);
			wp_editor( $text, $id, $editor_settings );
		}

		/**
		 * Edit widgets in accessibility mode
		 *
		 * @global string $pagenow
		 * @param string $editor
		 * @return string
		 * @since 2.0.0
		 */
		public function editor_accessibility_mode( $editor ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' && isset( $_GET['editwidget'] ) && strpos( $_GET['editwidget'], 'black-studio-tinymce' ) === 0 ) {
				$editor = 'html';
			}
			return $editor;
		}

		/**
		 * Apply auto_embed to widget text
		 *
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function widget_text_autoembed( $text, $instance, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				global $wp_embed;
				$text = $wp_embed->run_shortcode( $text );
				$text = $wp_embed->autoembed( $text );
			}
			return $text;
		}

		/**
		 * Apply smilies conversion to widget text
		 *
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function widget_text_convert_smilies( $text, $instance, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = convert_smilies( $text );
			}
			return $text;
		}

		/**
		 * Apply automatic paragraphs in widget text
		 *
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function widget_text_wpautop( $text, $instance, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = wpautop( $text );
			}
			return $text;
		}

		/**
		 * Process shortcodes in widget text
		 *
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function widget_text_do_shortcode( $text, $instance, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = do_shortcode( $text );
			}
			return $text;
		}

		/**
		 * Check if a widget is a Black Studio Tinyme Widget instance
		 *
		 * @param object $widget
		 * @return boolean
		 * @since 2.0.0
		 */
		public function check_widget( $widget ) {
			return gettype( $widget ) == 'object' && get_class( $widget ) == 'WP_Widget_Black_Studio_TinyMCE';
		}

		/**
		 * Output plugin links below the editor
		 *
		 * @return void
		 * @since 2.0.0
		 */
		function links() {
			if ( false == apply_filters( 'black_studio_tinymce_whitelabel', false ) ) { // consider donating if you whitelabel 
				$links = array(
					array(
						/* translators: text used for donation link */
						'text' => __( 'Donate', 'black-studio-tinymce-widget' ),
						'url' => 'http://www.blackstudio.it/en/wordpress-plugins/black-studio-tinymce-widget/',
					),
					array(
						/* translators: text used for support forum link */
						'text' => __( 'Support', 'black-studio-tinymce-widget' ),
						'url' => 'http://wordpress.org/support/plugin/black-studio-tinymce-widget',
					),
					array(
						/* translators: text used for reviews link */
						'text' => __( 'Rate', 'black-studio-tinymce-widget' ),
						'url' => 'http://wordpress.org/support/view/plugin-reviews/black-studio-tinymce-widget',
					),
					array(
						/* translators: text used for follow on twitter link */
						'text' => __( 'Follow', 'black-studio-tinymce-widget' ),
						'url' => 'https://twitter.com/blackstudioita',
					),
				);
				echo '<div class="bstw-links">';
				$counter = 0;
				foreach ( $links as $link ) {
					if ( $counter++ > 0 ) {
						echo ' | ';
					}
					echo '<a href="' . esc_url( $link['url'] ) . '" target="_blank">' . esc_html( $link['text'] ) . '</a>';
				}
				echo '</div>';
			}
		}
		
	} // END class Black_Studio_TinyMCE_Plugin

} // class_exists check

/**
 * Return the main instance to prevent the need to use globals
 *
 * @return object
 * @since 2.0.0
 */
function bstw() {
	return Black_Studio_TinyMCE_Plugin::instance();
}

/* Create the main instance */
bstw();
