<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class that applies widget text filters
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 2.0.0
 */

if ( ! class_exists( 'Black_Studio_TinyMCE_Text_Filters' ) ) {

	final class Black_Studio_TinyMCE_Text_Filters {

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
		 * @uses add_filter()
		 * @uses get_option()
		 *
		 * @global object $wp_embed
		 * @return void
		 * @since 2.0.0
		 */
		protected function __construct() {
			// Support for autoembed urls in widget text
			if ( get_option( 'embed_autourls' ) ) {
				add_filter( 'widget_text', array( $this, 'autoembed' ), 20, 3 );
			}
			// Support for smilies in widget text
			if ( get_option( 'use_smilies' ) ) {
				add_filter( 'widget_text', array( $this, 'convert_smilies' ), 30, 3 );
			}
			// Support for wpautop in widget text
			add_filter( 'widget_text', array( $this, 'wpautop' ), 40, 3 );
			// Support for shortcodes in widget text
			add_filter( 'widget_text', array( $this, 'do_shortcode' ), 50, 3 );
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
		 * Apply wp_kses to widget text
		 *
		 * @uses wp_kses_post()
		 * 
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 * @deprecated 2.0.3
		 */
		public function wp_kses_post( $text, $instance = null, $widget = null ) {
			_deprecated_function( __METHOD__, '2.0.3' );
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = wp_kses_post( $text );
			}
			return $text;
		}

		/**
		 * Apply auto_embed to widget text
		 *
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function autoembed( $text, $instance = null, $widget = null ) {
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
		 * @uses convert_smilies()
		 * 
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function convert_smilies( $text, $instance = null, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = convert_smilies( $text );
			}
			return $text;
		}

		/**
		 * Apply automatic paragraphs in widget text
		 *
		 * @uses wpautop()
		 * 
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function wpautop( $text, $instance = null, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = wpautop( $text );
			}
			return $text;
		}

		/**
		 * Process shortcodes in widget text
		 *
		 * @uses do_shortcode()
		 * 
		 * @param string $text
		 * @return string
		 * @since 2.0.0
		 */
		public function do_shortcode( $text, $instance = null, $widget = null ) {
			if ( bstw()->check_widget( $widget ) && ! empty( $instance ) ) {
				$text = do_shortcode( $text );
			}
			return $text;
		}

	} // END class Black_Studio_TinyMCE_Text_Filters

} // class_exists check
