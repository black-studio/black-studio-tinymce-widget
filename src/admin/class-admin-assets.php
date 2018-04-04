<?php
/**
 * Black Studio TinyMCE Widget - Admin assets
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget\Admin;

use WP_Query;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Admin\\Admin_Assets', false ) ) {

	/**
	 * Class that provides admin functionalities
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 3.0.0
	 */
	final class Admin_Assets {

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
		 * @uses add_action()
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			// Register action and filter hooks.
			add_action( 'admin_init', array( $this, 'admin_init' ), 20 );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 3.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; uh?' ), '2.0' );
		}

		/**
		 * Add actions and filters (only in widgets admin page)
		 *
		 * @uses add_action()
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function admin_init() {
			if ( bstw()->admin()->enabled() ) {
				add_action( 'admin_head', array( $this, 'enqueue_media' ) );
				add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );
				add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				add_action( 'wp_tiny_mce_init', array( $this, 'wp_tiny_mce_init' ) );
				add_filter( 'wp_editor_settings', array( $this, 'editor_settings' ), 5, 2 );
				add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_fix_rtl' ), 10 );
				add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_fullscreen' ), 10, 2 );
				add_filter( 'quicktags_settings', array( $this, 'quicktags_fullscreen' ), 10, 2 );
			}
		}

		/**
		 * Instantiate tinyMCE editor
		 *
		 * @uses add_thickbox()
		 * @uses wp_enqueue_media()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function enqueue_media() {
			// Add support for thickbox media dialog.
			add_thickbox();
			// New media modal dialog (WP 3.5+).
			if ( function_exists( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}
		}

		/**
		 * Enqueue styles
		 *
		 * @uses wp_enqueue_style()
		 *
		 * @return void
		 * @since 3.0.0
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
		 * @since 3.0.0
		 */
		public function enqueue_style() {
			$style  = apply_filters( 'black_studio_tinymce_widget_style', 'black-studio-tinymce-widget' );
			$path   = apply_filters( 'black_studio_tinymce_widget_style_path', 'css/' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.css' : '.min.css';
			wp_enqueue_style(
				$style,
				plugins_url( $path . $style . $suffix, dirname( dirname( __FILE__ ) ) ),
				array(),
				bstw()->get_version()
			);
		}

		/**
		 * Enqueue header scripts
		 *
		 * @uses wp_enqueue_script()
		 * @uses do_action()
		 *
		 * @return void
		 * @since 3.0.0
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
		 * @since 3.0.0
		 */
		public function enqueue_script() {
			$script = apply_filters( 'black_studio_tinymce_widget_script', 'black-studio-tinymce-widget' );
			$path   = apply_filters( 'black_studio_tinymce_widget_script_path', 'js/' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';
			wp_enqueue_script(
				$script,
				plugins_url( $path . $script . $suffix, dirname( dirname( __FILE__ ) ) ),
				array( 'jquery', 'editor', 'quicktags' ),
				bstw()->get_version(),
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
		 * @since 3.0.0
		 */
		public function localize_script() {
			$container_selectors = apply_filters( 'black_studio_tinymce_container_selectors', array( 'div.widget', 'div.widget-inside' ) );
			$activate_events     = apply_filters( 'black_studio_tinymce_activate_events', array() );
			$deactivate_events   = apply_filters( 'black_studio_tinymce_deactivate_events', array() );
			$data                = array(
				'dummy_post_id'       => bstw()->admin()->get_dummy_post_id(),
				'container_selectors' => implode( ', ', $container_selectors ),
				'activate_events'     => $activate_events,
				'deactivate_events'   => $deactivate_events,
				/* translators: error message shown when a duplicated widget ID is detected */
				'error_duplicate_id'  => __( 'ERROR: Duplicate widget ID detected. To avoid content loss, please create a new widget with the same content and then delete this one.', 'black-studio-tinymce-widget' ),
			);
			wp_localize_script( apply_filters( 'black_studio_tinymce_widget_script', 'black-studio-tinymce-widget' ), 'bstwData', $data );
		}

		/**
		 * Enqueue footer scripts
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function admin_print_footer_scripts() {
			bstw()->admin()->editor( '', 'black-studio-tinymce-widget', 'black-studio-tinymce-widget' );
		}

		/**
		 * Setup editor instance for event handling
		 *
		 * @uses SCRIPT_DEBUG
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function wp_tiny_mce_init() {
			$script = 'black-studio-tinymce-widget-setup';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';
			// phpcs:ignore WordPress.WP.EnqueuedResources
			echo '<script type="text/javascript" src="' . plugins_url( 'js/' . $script . $suffix, dirname( dirname( __FILE__ ) ) ) . '"></script>' . "\n"; // xss ok.
		}

		/**
		 * Set editor settings
		 *
		 * @param mixed[] $settings Array of settings.
		 * @param string  $editor_id Editor instance ID.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function editor_settings( $settings, $editor_id ) {
			if ( strstr( $editor_id, 'black-studio-tinymce' ) ) {
				$settings['tinymce']       = array(
					'wp_skip_init'       => 'widget-black-studio-tinymce-__i__-text' === $editor_id,
					'add_unload_trigger' => false,
					'wp_autoresize_on'   => false,
				);
				$settings['editor_height'] = 350;
				$settings['dfw']           = true;
				$settings['editor_class']  = 'black-studio-tinymce';
			}
			return $settings;
		}

		/**
		 * Fix for rtl languages
		 *
		 * @param mixed[] $settings Array of settings.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function tinymce_fix_rtl( $settings ) {
			// This fix has to be applied to all editor instances (not just BSTW ones).
			if ( is_rtl() && isset( $settings['plugins'] ) && ',directionality' === $settings['plugins'] ) {
				unset( $settings['plugins'] );
			}
			return $settings;
		}

		/**
		 * Apply TinyMCE default fullscreen
		 *
		 * @param mixed[]     $settings  Array of settings.
		 * @param string|null $editor_id Editor ID.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function tinymce_fullscreen( $settings, $editor_id = null ) {
			if ( strstr( $editor_id, 'black-studio-tinymce' ) ) {
				for ( $i = 1; $i <= 4; $i++ ) {
					$toolbar = 'toolbar' . $i;
					if ( isset( $settings[ $toolbar ] ) ) {
						$settings[ $toolbar ] = str_replace( 'wp_fullscreen', 'wp_fullscreen,fullscreen', $settings[ $toolbar ] );
					}
				}
			}
			return $settings;
		}

		/**
		 * Disable Quicktags default fullscreen
		 *
		 * @param mixed[] $settings  Array of settings.
		 * @param string  $editor_id Editor ID.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function quicktags_fullscreen( $settings, $editor_id ) {
			if ( strstr( $editor_id, 'black-studio-tinymce' ) ) {
				$settings['buttons'] = str_replace( ',fullscreen', '', $settings['buttons'] );
			}
			return $settings;
		}

	} // END class

} // END class_exists check
