<?php

/**
 * Class that provides compatibility code with older WordPress versions
 *
 * @package Black Studio TinyMCE Widget
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Wordpress' ) ) {

	class Black_Studio_TinyMCE_Compatibility_Wordpress {

		private $main;

		/* Class constructor */
		function __construct( $plugin ) {
			if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				wp_pre_38();
			}
			if ( version_compare( get_bloginfo( 'version' ), '3.3', '<' ) ) {
				wp_pre_33();
			}
			if ( version_compare( get_bloginfo( 'version' ), '3.2', '<' ) ) {
				wp_pre_32();
			}
		}

		/* Compatibility for WordPress prior to 3.8 */
		function wp_pre_38() {
			add_filter( 'black-studio-tinymce-widget-style', array( $this, 'wp_pre_38_style' ) );
		}

		/* Enqueue styles for WordPress prior to 3.8 */
		function wp_pre_38_style( $style ) {
			$style = 'black-studio-tinymce-widget-legacy';
			return $style;
		}

		/* Compatibility for WordPress prior 3.3 */
		function wp_pre_33() {
			add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 10 );
			remove_action( 'admin_print_styles', array( $this->main, 'admin_print_styles' ) );
			add_action( 'admin_print_styles', array( $this, 'wp_pre_33_admin_print_styles' ) );
			remove_action( 'admin_print_scripts', array( $this->main, 'admin_print_scripts' ) );
			add_action( 'admin_print_scripts', array( $this, 'wp_pre_33_admin_print_scripts' ) );
			remove_action( 'admin_print_footer_scripts', array( $this->main, 'admin_print_footer_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_33_admin_print_footer_scripts' ) );
		}

		/* Remove WP fullscreen mode and set the native tinyMCE fullscreen mode */
		function wp_pre_33_tiny_mce_before_init( $settings ) {
			$plugins = explode( ',', $settings['plugins'] );
			if ( isset( $plugins['wpfullscreen'] ) ) {
				unset( $plugins['wpfullscreen'] );
			}
			if ( ! isset( $plugins['fullscreen'] ) ) {
				$plugins[] = 'fullscreen';
			}
			$settings['plugins'] = implode( ',', $plugins );
			return $settings;
		}

		/* Enqueue styles for WordPress prior to 3.3 */
		function wp_pre_33_admin_print_styles() {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'editor-buttons' );
			$this->main->enqueue_style( 'black-studio-tinymce-widget-legacy' );
		}

		/* Enqueue header scripts for WordPress prior to 3.3 */
		function wp_pre_33_admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script(
				'black-studio-tinymce-widget-legacy',
				esc_url( BLACK_STUDIO_TINYMCE_WIDGET_URL . 'js/black-studio-tinymce-widget-legacy' . $suffix . '.js' ),
				array( 'jquery', 'editor' ),
				BLACK_STUDIO_TINYMCE_WIDGET_VERSION,
				true
			);
		}

		/* Enqueue footer scripts for WordPress prior to 3.3 */
		function  wp_pre_33_admin_print_footer_scripts() {
			if ( function_exists( 'wp_tiny_mce' ) ) {
				wp_tiny_mce( false, array() );
			}
			if ( function_exists( 'wp_preload_dialogs' ) ) {
				wp_preload_dialogs( array( 'plugins' => 'wpdialogs,wplink,wpfullscreen' ) );
			}
		}

		/* Compatibility for WordPress prior to 3.2 */
		function wp_pre_32() {
			remove_action( 'admin_print_styles', array( $this->main, 'admin_print_styles' ) );
			add_action( 'admin_print_styles', array( $this, 'wp_pre_33_admin_print_styles' ) );
			remove_action( 'admin_print_footer_scripts', array( $this->main, 'admin_print_footer_scripts' ) );
			remove_action( 'admin_print_footer_scripts', array( $thi, 'wp_pre_33_admin_print_footer_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_32_admin_print_footer_scripts' ) );
		}
		
		/* Enqueue footer scripts for WordPress prior to 3.2 */
		function  wp_pre_32_admin_print_footer_scripts() {
			if ( function_exists( 'wp_tiny_mce' ) ) {
				wp_tiny_mce( false, array() );
			}
			if ( function_exists( 'wp_tiny_mce_preload_dialogs' ) ) {
				wp_tiny_mce_preload_dialogs();
			}
		}

	} // class declaration

} // class_exists check
