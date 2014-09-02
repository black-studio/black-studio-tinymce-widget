<?php

/**
 * Class that provides compatibility code with older WordPress versions
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Wordpress' ) ) {

	class Black_Studio_TinyMCE_Compatibility_Wordpress {

		/**
		 * Reference to main plugin instance
		 *
		 * @var object
		 * @since 2.0.0
		 */
		private $plugin;

		/**
		 * Class constructor
		 *
		 * @param object $plugin
		 * @since 2.0.0
		 */
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			$wp_version = get_bloginfo( 'version' );
			if ( version_compare( $wp_version, '3.2', '<' ) ) {
				add_action( 'admin_init', array( $this, 'wp_pre_32' ), 32 );
			}
			if ( version_compare( $wp_version, '3.3', '<' ) ) {
				add_action( 'admin_init', array( $this, 'wp_pre_33' ), 33 );
			}
			if ( version_compare( $wp_version, '3.5', '<' ) ) {
				add_action( 'admin_init', array( $this, 'wp_pre_35' ), 35 );
			}
			if ( version_compare( $wp_version, '3.8', '<' ) ) {
				add_action( 'admin_init', array( $this, 'wp_pre_38' ), 38 );
			}
		}

		/**
		 * Compatibility for WordPress prior to 3.2
		 *
		 * @uses remove_action()
		 * @uses add_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_32() {
			remove_action( 'admin_print_footer_scripts', array( $this->plugin, 'admin_print_footer_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_32_admin_print_footer_scripts' ) );
		}

		/**
		 * Enqueue footer scripts for WordPress prior to 3.2
		 *
		 * @uses wp_tiny_mce()
		 * @uses wp_tiny_mce_preload_dialogs()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function  wp_pre_32_admin_print_footer_scripts() {
			if ( function_exists( 'wp_tiny_mce' ) ) {
				wp_tiny_mce( false, array() );
			}
			if ( function_exists( 'wp_tiny_mce_preload_dialogs' ) ) {
				wp_tiny_mce_preload_dialogs();
			}
		}

		/**
		 * Compatibility for WordPress prior to 3.3
		 *
		 * @uses add_filter()
		 * @uses add_action()
		 * @uses remove_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_33() {
			add_filter( 'tiny_mce_before_init', array( $this, 'wp_pre_33_tiny_mce_before_init' ), 67 );
			remove_action( 'admin_print_styles', array( $this->plugin, 'admin_print_styles' ) );
			add_action( 'admin_print_styles', array( $this, 'wp_pre_33_admin_print_styles' ) );
			remove_action( 'admin_print_scripts', array( $this->plugin, 'admin_print_scripts' ) );
			add_action( 'admin_print_scripts', array( $this, 'wp_pre_33_admin_print_scripts' ) );
			remove_action( 'admin_print_footer_scripts', array( $this->plugin, 'admin_print_footer_scripts' ) );
			remove_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_32_admin_print_footer_scripts' ) );
			add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_33_admin_print_footer_scripts' ) );
			add_filter( 'black-studio-tinymce-widget-script', array( $this, 'wp_pre_33_handle' ), 67 );
			add_filter( 'black-studio-tinymce-widget-style', array( $this, 'wp_pre_33_handle' ), 67 );
		}

		/**
		 * Remove WP fullscreen mode and set the native tinyMCE fullscreen mode for WordPress prior to 3.3
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 * @since 2.0.0
		 */
		public function wp_pre_33_tiny_mce_before_init( $settings ) {
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

		/**
		 * Enqueue styles for WordPress prior to 3.3
		 *
		 * @uses wp_enqueue_style()
		 * @uses Black_Studio_TinyMCE_Plugin::enqueue_style()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_33_admin_print_styles() {
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'editor-buttons' );
			$this->plugin->enqueue_style();
		}

		/**
		 * Enqueue header scripts for WordPress prior to 3.3
		 *
		 * @uses wp_enqueue_script()
		 * @uses Black_Studio_TinyMCE_Plugin::enqueue_script()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_33_admin_print_scripts() {
			wp_enqueue_script( 'media-upload' );
			$this->plugin->enqueue_script();
			$this->plugin->localize_script();
		}

		/**
		 * Filter to enqueue style / script for WordPress prior to 3.3
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function wp_pre_33_handle() {
			return 'black-studio-tinymce-widget-legacy';
		}

		/**
		 * Enqueue footer scripts for WordPress prior to 3.3
		 *
		 * @uses wp_tiny_mce()
		 * @uses wp_preload_dialog()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_33_admin_print_footer_scripts() {
			if ( function_exists( 'wp_tiny_mce' ) ) {
				wp_tiny_mce( false, array() );
			}
			if ( function_exists( 'wp_preload_dialogs' ) ) {
				wp_preload_dialogs( array( 'plugins' => 'wpdialogs,wplink,wpfullscreen' ) );
			}
		}

		/**
		 * Compatibility for WordPress prior to 3.5
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_35() {
			add_filter( '_upload_iframe_src', array( $this, 'wp_pre_35_upload_iframe_src' ) );
		}

		/**
		 * Enable full media options in upload dialog for WordPress prior to 3.5
		 * (this is done excluding post_id parameter in Thickbox iframe url)
		 *
		 * @global string $pagenow
		 * @param string $upload_iframe_src
		 * @return string
		 * @since 2.0.0
		 */
		public function wp_pre_35_upload_iframe_src( $upload_iframe_src ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' || ( $pagenow == 'admin-ajax.php' && isset ( $_POST['id_base'] ) && $_POST['id_base'] == 'black-studio-tinymce' ) ) {
				$upload_iframe_src = str_replace( 'post_id=0', '', $upload_iframe_src );
			}
			return $upload_iframe_src;
		}

		/**
		 * Compatibility for WordPress prior to 3.8
		 *
		 * @uses add_filter()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_38() {
			add_filter( 'tiny_mce_before_init', array( $this, 'wp_pre_38_tiny_mce_before_init' ), 62 );
		}

		/**
		 * Remove the "More" toolbar button (only in widget screen) for WordPress prior to 3.8
		 *
		 * @global string $pagenow
		 * @param string[] $settings
		 * @return string[]
		 * @since 2.0.0
		 */
		public function wp_pre_38_tiny_mce_before_init( $settings ) {
			global $pagenow;
			if ( $pagenow == 'widgets.php' ) {
				$settings['theme_advanced_buttons1'] = str_replace( ',wp_more', '', $settings['theme_advanced_buttons1'] );
			}
			return $settings;
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Wordpress

} // class_exists check
