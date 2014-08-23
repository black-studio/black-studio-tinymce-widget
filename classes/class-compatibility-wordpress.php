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

		private $plugin;

		/* Class constructor */
		function __construct( $plugin ) {
			$this->plugin = $plugin;
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
			add_filter( 'tiny_mce_before_init', array( $this, 'wp_pre_38_tiny_mce_before_init' ), 20 );
			add_filter( 'black-studio-tinymce-widget-style', array( $this, 'wp_pre_38_style' ) );
		}

		/* Remove the "More" toolbar button (only in widget screen) for WordPress prior to 3.8 */
		function wp_pre_38_tiny_mce_before_init( $settings ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' ) {
				$settings['theme_advanced_buttons1'] = str_replace( ',wp_more', '', $settings['theme_advanced_buttons1'] );
			}
			return $settings;
		}

		/* Enqueue styles for WordPress prior to 3.8 */
		function wp_pre_38_style() {
			return 'black-studio-tinymce-widget-legacy';
		}

		/* Compatibility for WordPress prior 3.3 */
		function wp_pre_33() {
			add_filter( 'tiny_mce_before_init', array( $this, 'wp_pre_33_tiny_mce_before_init' ), 20 );
			remove_action( 'admin_print_styles', array( $this->plugin, 'admin_print_styles' ) );
			add_action( 'admin_print_styles', array( $this, 'wp_pre_33_admin_print_styles' ) );
			remove_action( 'admin_print_scripts', array( $this->plugin, 'admin_print_scripts' ) );
			add_action( 'admin_print_scripts', array( $this, 'wp_pre_33_admin_print_scripts' ) );
			remove_action( 'admin_print_footer_scripts', array( $this->plugin, 'admin_print_footer_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_33_admin_print_footer_scripts' ) );
			add_filter( 'black-studio-tinymce-widget-script', array( $this, 'wp_pre_33_script' ) );
		}

		/* Remove WP fullscreen mode and set the native tinyMCE fullscreen mode for WordPress prior to 3.3 */
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
			$this->plugin->enqueue_style();
		}

		/* Enqueue header scripts for WordPress prior to 3.3 */
		function wp_pre_33_admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			$this->plugin->enqueue_script();
		}

		/* Enqueue script for WordPress prior to 3.3 */
		function wp_pre_33_script() {
			return 'black-studio-tinymce-widget-legacy';
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
			remove_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_33_admin_print_footer_scripts' ) );
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

return new Black_Studio_TinyMCE_Compatibility_Wordpress( bstw() );
