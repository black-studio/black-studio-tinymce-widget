<?php
/**
 * Black Studio TinyMCE Widget - Admin features
 *
 * @package Black_Studio_TinyMCE_Widget
 */

namespace Black_Studio_TinyMCE_Widget\Admin;

use WP_Query;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Widget\\Admin\\Admin', false ) ) {

	/**
	 * Class that provides admin functionalities
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 2.0.0
	 */
	final class Admin {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 2.0.0
		 */
		protected static $_instance = null;

		/**
		 * Array containing the plugin links
		 *
		 * @var array
		 * @since 2.0.0
		 */
		protected $links;

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
		 * @uses add_action()
		 * @uses add_filter()
		 * @uses get_option()
		 * @uses get_bloginfo()
		 *
		 * @since 2.0.0
		 */
		protected function __construct() {
			// Register action and filter hooks.
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ), 20 );
			add_action( 'init', array( $this, 'register_dummy_post_type' ) );
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 2.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; uh?' ), '2.0' );
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
			load_plugin_textdomain( 'black-studio-tinymce-widget', false, dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/' );
		}

		/**
		 * Checks if the plugin admin code should be loaded
		 *
		 * @uses apply_filters()
		 *
		 * @global string $pagenow
		 * @return boolean
		 * @since 2.0.0
		 */
		public function enabled() {
			global $pagenow;
			$enabled_pages = apply_filters( 'black_studio_tinymce_enable_pages', array( 'widgets.php', 'customize.php', 'admin-ajax.php' ) );
			return apply_filters( 'black_studio_tinymce_enable', in_array( $pagenow, $enabled_pages, true ) );
		}

		/**
		 * Add actions and filters (only in widgets admin page)
		 *
		 * @uses add_action()
		 * @uses add_filter()
		 * @uses do_action()
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function admin_init() {
			$this->init_links();
			add_action( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			if ( $this->enabled() ) {
				add_action( 'black_studio_tinymce_before_editor', array( $this, 'display_links' ) );
				add_action( 'black_studio_tinymce_editor', array( $this, 'editor' ), 10, 4 );
				add_action( 'black_studio_tinymce_after_editor', array( $this, 'fix_the_editor_content_filter' ) );
				if ( ! user_can_richedit() ) {
					add_action( 'admin_notices', array( $this, 'visual_editor_disabled_notice' ) );
				}
				add_action( 'wp_ajax_bstw_visual_editor_disabled_dismiss_notice', array( $this, 'visual_editor_disabled_dismiss_notice' ) );
				do_action( 'black_studio_tinymce_load' );
			}
		}

		/**
		 * Output the visual editor
		 *
		 * @uses wp_editor()
		 *
		 * @param string $text      Text inside the editor.
		 * @param string $editor_id Editor instance ID.
		 * @param string $name      Editor instance name.
		 * @param string $type      Editor instance type.
		 * @return void
		 * @since 2.0.0
		 */
		public function editor( $text, $editor_id, $name = '', $type = 'visual' ) {
			wp_editor( $text, $editor_id, array(
				'textarea_name'  => $name,
				'default_editor' => 'visual' === $type ? 'tmce' : 'html',
			) );
		}

		/**
		 * Remove editor content filters for multiple editor instances
		 * Workaround for WordPress Core bug #28403 https://core.trac.wordpress.org/ticket/28403
		 *
		 * @uses remove_filter
		 *
		 * @return void
		 * @since 2.1.7
		 */
		public function fix_the_editor_content_filter() {
			remove_filter( 'the_editor_content', 'wp_htmledit_pre' );
			remove_filter( 'the_editor_content', 'wp_richedit_pre' );
		}

		/**
		 * Initialize plugin links
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function init_links() {
			$this->links = array(
				/* translators: text used for plugin home link */
				'https://wordpress.org/plugins/black-studio-tinymce-widget/' => __( 'Home', 'black-studio-tinymce-widget' ),
				/* translators: text used for support faq link */
				'https://wordpress.org/plugins/black-studio-tinymce-widget/faq/' => __( 'FAQ', 'black-studio-tinymce-widget' ),
				/* translators: text used for support forum link */
				'https://wordpress.org/support/plugin/black-studio-tinymce-widget' => __( 'Support', 'black-studio-tinymce-widget' ),
				/* translators: text used for reviews link */
				'https://wordpress.org/support/view/plugin-reviews/black-studio-tinymce-widget' => __( 'Rate', 'black-studio-tinymce-widget' ),
				/* translators: text used for follow on twitter link */
				'https://twitter.com/blackstudioita' => __( 'Follow', 'black-studio-tinymce-widget' ),
				/* translators: text used for donation link */
				'https://www.blackstudio.it/en/wordpress-plugins/black-studio-tinymce-widget/' => __( 'Donate', 'black-studio-tinymce-widget' ),
			);
		}

		/**
		 * Display plugin links
		 *
		 * @return void
		 * @since 2.0.0
		 */
		public function display_links() {
			echo "\t<div class='bstw-links'>\n";
			echo "\t\t<span class='bstw-links-list'>\n";
			$counter = count( $this->links ) - 1;
			foreach ( $this->links as $url => $label ) {
				$separator = ( $counter-- > 0 ? ' | ' : '' );
				echo "\t\t\t<a href='" . esc_url( $url ) . "' target='_blank'>" . esc_html( $label ) . "</a>$separator\n"; // xss ok.
			}
			echo "\t\t</span>\n";
			/* translators: text used for the icon that shows the plugin links */
			echo "\t\t<a class='bstw-links-icon icon16 icon-plugins' href='#' title='" . esc_attr( __( 'About Black Studio TinyMCE Widget plugin', 'black-studio-tinymce-widget' ) ) . "'></a>\n";
			echo "\t</div>\n";
		}

		/**
		 * Show row meta on the plugin screen
		 *
		 * @uses esc_html()
		 * @uses esc_url()
		 *
		 * @param string[] $links Array of links.
		 * @param string   $file  Plugin's filename.
		 * @return string[]
		 * @since 2.0.0
		 */
		public function plugin_row_meta( $links, $file ) {
			if ( bstw()->get_basename() === $file ) {
				foreach ( $this->links as $url => $label ) {
					$links[ $label ] = '<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html( $label ) . '</a>';
				}
			}
			return $links;
		}

		/**
		 * Show admin notice when visual editor is disabled in current user's profile settings
		 *
		 * @uses get_user_meta()
		 * @uses get_current_user_id()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function visual_editor_disabled_notice() {
			global $pagenow;
			$dismissed = false;
			if ( function_exists( 'get_user_meta' ) ) {
				$dismissed = get_user_meta( get_current_user_id(), '_bstw_visual_editor_disabled_notice_dismissed', true );
			}
			if ( 'widgets.php' === $pagenow && empty( $dismissed ) ) {
				echo '<div class="bstw-visual-editor-disabled-notice notice notice-warning is-dismissible">';
				/* translators: warning message shown when when visual editor is disabled in current user's profile settings */
				echo '<p>' . esc_html( __( 'Visual Editor is disabled in your Profile settings. You need to enable it in order to use the Visual Editor widget at its full potential.', 'black-studio-tinymce-widget' ) ) . '</p>';
				echo '</div>';
			}
		}

		/**
		 * Store dismission of the "Visual Editor disabled" notice for the current user
		 *
		 * @uses add_user_meta()
		 * @uses get_current_user_id()
		 *
		 * @return void
		 * @since 2.4.0
		 */
		public function visual_editor_disabled_dismiss_notice() {
			if ( function_exists( 'add_user_meta' ) ) {
				add_user_meta( get_current_user_id(), '_bstw_visual_editor_disabled_notice_dismissed', true );
			}
		}

		/**
		 * Register a private custom post type to be used for link embed previews
		 *
		 * @uses register_post_type()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function register_dummy_post_type() {
			$args = array(
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => false,
				'query_var'          => false,
				'rewrite'            => false,
				'capability_type'    => 'post',
				'hierarchical'       => false,
				'menu_position'      => null,
				'show_in_nav_menus'  => false,
				'has_archive'        => false,
			);
			register_post_type( 'bstw_dummy', $args );
		}

		/**
		 * Get dummy post ID for link embed previews
		 *
		 * @uses WP_Query()
		 * @uses wp_insert_post()
		 * @uses update_option()
		 * @uses get_option()
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_dummy_post_id() {
			$query_post = new WP_Query( 'post_type=bstw_dummy' );
			if ( $query_post->post_count > 0 ) {
				$dummy_post_id = $query_post->post->ID;
			} else {
				$dummy_post_id = wp_insert_post( array( 'post_type' => 'bstw_dummy' ) );
			}
			return $dummy_post_id;
		}

	} // END class

} // END class_exists check
