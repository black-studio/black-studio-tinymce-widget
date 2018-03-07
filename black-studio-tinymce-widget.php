<?php
/**
 * Black Studio TinyMCE Widget - Main plugin file
 *
 * @package Black_Studio_TinyMCE_Widget
 */

/*
Plugin Name: Black Studio TinyMCE Widget
Plugin URI: https://wordpress.org/plugins/black-studio-tinymce-widget/
Description: Adds a new "Visual Editor" widget type based on the native WordPress TinyMCE editor.
Version: 2.6.2
Author: Black Studio
Author URI: https://www.blackstudio.it
Requires at least: 3.3
Tested up to: 4.8
License: GPLv3
Text Domain: black-studio-tinymce-widget
Domain Path: /languages
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/class-black-studio-tinymce-plugin.php';

if ( ! function_exists( 'bstw' ) ) {

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

} // END function_exists bstw check
else {

	/* Check for multiple plugin instances */
	if ( ! function_exists( 'bstw_multiple_notice' ) ) {

		/**
		 * Show admin notice when multiple instances of the plugin are detected
		 *
		 * @return void
		 * @since 2.1.0
		 */
		function bstw_multiple_notice() {
			global $pagenow;
			if ( 'widgets.php' === $pagenow ) {
				echo '<div class="error">';
				/* translators: error message shown when multiple instance of the plugin are detected */
				echo '<p>' . esc_html( __( 'ERROR: Multiple instances of the Black Studio TinyMCE Widget plugin were detected. Please activate only one instance at a time.', 'black-studio-tinymce-widget' ) ) . '</p>';
				echo '</div>';
			}
		}
		add_action( 'admin_notices', 'bstw_multiple_notice' );

	} // END function_exists bstw_multiple_notice check
} // END else function_exists bstw check
