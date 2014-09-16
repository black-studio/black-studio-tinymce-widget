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

	final class Black_Studio_TinyMCE_Compatibility_Wordpress {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $_instance = null;

		/**
		 * Return the single class instance
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
		 * Class constructor
		 *
		 * @uses get_bloginfo()
		 * 
		 * @param object $plugin
		 * @since 2.0.0
		 */
		protected function __construct() {
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
			if ( version_compare( $wp_version, '3.9', '<' ) ) {
				add_action( 'admin_init', array( $this, 'wp_pre_39' ), 39 );
			}
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
		 * Compatibility for WordPress prior to 3.2
		 *
		 * @uses remove_action()
		 * @uses add_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_32() {
			if ( bstw()->enabled() ) {
				remove_action( 'admin_print_footer_scripts', array( bstw(), 'admin_print_footer_scripts' ) );
				add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_32_admin_print_footer_scripts' ) );
			}
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
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_33() {
			$wp_version = get_bloginfo( 'version' );
			if ( bstw()->enabled() ) {
				add_filter( 'tiny_mce_before_init', array( $this, 'wp_pre_33_tiny_mce_before_init' ), 67 );
				add_filter( 'black-studio-tinymce-widget-script', array( $this, 'wp_pre_33_handle' ), 67 );
				add_filter( 'black-studio-tinymce-widget-style', array( $this, 'wp_pre_33_handle' ), 67 );
				remove_action( 'admin_print_styles', array( bstw(), 'admin_print_styles' ) );
				add_action( 'admin_print_styles', array( $this, 'wp_pre_33_admin_print_styles' ) );
				remove_action( 'admin_print_scripts', array( bstw(), 'admin_print_scripts' ) );
				add_action( 'admin_print_scripts', array( $this, 'wp_pre_33_admin_print_scripts' ) );
				remove_action( 'admin_print_footer_scripts', array( bstw(), 'admin_print_footer_scripts' ) );
				if ( ! version_compare( $wp_version, '3.2', '<' ) ) {
					remove_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_32_admin_print_footer_scripts' ) );
				}
				add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_33_admin_print_footer_scripts' ) );
			}
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
			bstw()->enqueue_style();
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
			bstw()->enqueue_script();
			bstw()->localize_script();
		}

		/**
		 * Filter to enqueue style / script for WordPress prior to 3.3
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function wp_pre_33_handle() {
			return 'black-studio-tinymce-widget-pre33';
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
			if ( bstw()->enabled() ) {
				add_filter( '_upload_iframe_src', array( $this, 'wp_pre_35_upload_iframe_src' ), 65 );
			}
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
			if ( $pagenow == 'widgets.php' || ( $pagenow == 'admin-ajax.php' && isset( $_POST['id_base'] ) && $_POST['id_base'] == 'black-studio-tinymce' ) ) {
				$upload_iframe_src = str_replace( 'post_id=0', '', $upload_iframe_src );
			}
			return $upload_iframe_src;
		}

		/**
		 * Compatibility for WordPress prior to 3.9
		 *
		 * @uses add_action()
		 * @uses remove_action()
		 * @uses add_filter()
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_39() {
			$wp_version = get_bloginfo( 'version' );
			if ( bstw()->enabled() ) {
				add_filter( 'black-studio-tinymce-widget-script', array( $this, 'wp_pre_39_handle' ), 61 );
				add_filter( 'tiny_mce_before_init', array( $this, 'wp_pre_39_tiny_mce_before_init' ), 61 );
				add_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_39_admin_print_footer_scripts' ) );
				remove_action( 'admin_print_footer_scripts', array( bstw(), 'admin_print_footer_scripts' ) );
				if ( ! version_compare( $wp_version, '3.2', '<' ) ) {
					remove_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_32_admin_print_footer_scripts' ) );
				}
				if ( ! version_compare( $wp_version, '3.3', '<' ) ) {
					remove_action( 'admin_print_footer_scripts', array( $this, 'wp_pre_33_admin_print_footer_scripts' ) );
				}
				add_action( 'black_studio_tinymce_editor', array( $this, 'wp_pre_39_editor' ), 10, 4 );
				remove_action( 'black_studio_tinymce_editor', array( bstw(), 'editor' ), 10, 3 );
			}
		}

		/**
		 * Filter to enqueue style / script for WordPress prior to 3.9
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function wp_pre_39_handle() {
			return 'black-studio-tinymce-widget-pre39';
		}

		/**
		 * TinyMCE initialization for WordPress prior to 3.9
		 *
		 * @param mixed[] $settings
		 * @return mixed[]
		 * @since 2.0.0
		 */
		public function wp_pre_39_tiny_mce_before_init( $settings ) {
			$custom_settings = array(
				'remove_linebreaks' => false,
				'convert_newlines_to_brs' => false,
				'force_p_newlines' => true,
				'force_br_newlines' => false,
				'remove_redundant_brs' => false,
				'forced_root_block' => 'p',
				'apply_source_formatting' => true,
			);
			// Return modified settings
			return array_merge( $settings, $custom_settings );
		}

		/**
		 * Enqueue footer scripts for WordPress prior to 3.9
		 *
		 * @uses wp_editor()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_39_admin_print_footer_scripts() {
			if ( function_exists( 'wp_editor' ) ) {
				wp_editor( '', 'black-studio-tinymce-widget' );
			}
		}

		/**
		 * Output the visual editor code for WordPress prior to 3.9
		 *
		 * @uses wp_editor()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function wp_pre_39_editor( $text, $id, $name = '', $type = 'visual' ) {
			$switch_class = $type == 'visual' ? 'html-active' : 'tmce-active';
			?>
            <div id="<?php echo $id; ?>-wp-content-wrap" class="wp-core-ui wp-editor-wrap <?php echo esc_attr( $switch_class ); ?> has-dfw">
                <div id="<?php echo $id; ?>-wp-content-editor-tools" class="wp-editor-tools hide-if-no-js">
                    <div class="wp-editor-tabs">
                        <a id="<?php echo esc_attr( $id ); ?>-content-html" class="wp-switch-editor switch-html"><?php _e( 'HTML' ); ?></a>
                        <a id="<?php echo esc_attr( $id ); ?>-content-tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
                    </div>
                    <div id="<?php esc_attr( $id ); ?>-wp-content-media-buttons" class="wp-media-buttons">
						<?php do_action( 'media_buttons', $id ); ?>
                    </div>
                </div>
                <div class="wp-editor-container">
	                <textarea class="widefat" rows="20" cols="40" id="<?php echo $id; ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
                </div>
            </div>
            <?php
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Wordpress

} // class_exists check
