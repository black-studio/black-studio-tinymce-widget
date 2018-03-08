<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that provides compatibility code for NextGEN Gallery plugin
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.4.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_Plugin_Nextgen_Gallery' ) ) {

	final class Black_Studio_TinyMCE_Compatibility_Plugin_Nextgen_Gallery {

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
		 * @uses is_admin()
		 * @uses add_filter()
		 * @uses add_action()
		 *
		 * @since 2.4.0
		 */
		protected function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'nextgen_gallery_admin_init' ) );
			}
			add_action( 'init', array( $this, 'nextgen_gallery_customizer_init' ), 20 );
			add_filter( 'widget_text', array( $this, 'widget_text' ) );
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
		 * Initialize compatibility code for NextGEN Gallery plugin 
		 *
		 * @uses add_action()
		 * @uses is_plugin_active()
		 * @uses M_Attach_To_Post class (part of NextGEN Gallery)
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function nextgen_gallery_admin_init() {
			if ( is_plugin_active( 'nextgen-gallery/nggallery.php' ) ) {
				if ( ! preg_match( '/\/wp-admin\/(post|post-new)\.php$/', $_SERVER['SCRIPT_NAME'] ) && bstw()->admin()->enabled() ) {
					if ( class_exists( 'M_Attach_To_Post' ) ) {
						$ngg_module_attach_to_post = new M_Attach_To_Post();
						add_action( 'admin_enqueue_scripts', array( $ngg_module_attach_to_post, '_enqueue_tinymce_resources' ) );
						add_action( 'admin_enqueue_scripts', array( $this, 'nextgen_gallery_admin_enqueue_style' ) );
					}
				}
			}
		}

		/**
		 * Enqueue admin style for NextGEN Gallery plugin 
		 *
		 * @uses wp_enqueue_style()
		 * @uses C_Router class (part of NextGEN Gallery)
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function nextgen_gallery_admin_enqueue_style() {
			if ( class_exists( 'C_Router' ) ) {
				$router = C_Router::get_instance();
				wp_enqueue_style( 'ngg_attach_to_post_dialog', $router->get_static_url( 'photocrati-attach_to_post#attach_to_post_dialog.css' ) );
			}
		}

		/**
		 * Enqueue NextGEN Gallery frame communication script in Customizer
		 *
		 * @uses is_plugin_active()
		 * @uses add_action()
		 * @uses M_Frame_Communication class (part of NextGEN Gallery)
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function nextgen_gallery_customizer_init() {
			if ( is_plugin_active( 'nextgen-gallery/nggallery.php' ) ) {
				if ( class_exists( 'M_Frame_Communication' ) ) {
					$ngg_module_frame_communication = new M_Frame_Communication();
					add_action( 'customize_controls_enqueue_scripts', array( $ngg_module_frame_communication, 'enqueue_admin_scripts' ) );
				}
			}
		}

		/**
		 * Widget text filter code for NextGEN Gallery plugin
		 *
		 * @uses is_plugin_active()
		 * @uses M_Attach_To_Post class (part of NextGEN Gallery)
		 *
		 * @param string $content
		 * @return string
		 * @since 2.4.0
		 */
		public function widget_text( $content ) {
			if ( is_plugin_active( 'nextgen-gallery/nggallery.php' ) ) {
				if ( class_exists( 'M_Attach_To_Post' ) ) {
					$ngg_module_attach_to_post = new M_Attach_To_Post();
					$content = $ngg_module_attach_to_post->substitute_placeholder_imgs( $content );
				}
			}
			return $content;
		}

	} // END class Black_Studio_TinyMCE_Compatibility_Plugin_Nextgen_Gallery

} // END class_exists check
