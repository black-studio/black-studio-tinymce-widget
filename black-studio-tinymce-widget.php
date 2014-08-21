<?php
/*
Plugin Name: Black Studio TinyMCE Widget
Plugin URI: http://wordpress.org/extend/plugins/black-studio-tinymce-widget/
Description: Adds a WYSIWYG widget based on the standard TinyMCE WordPress visual editor.
Version: 2.0.0
Author: Black Studio
Author URI: http://www.blackstudio.it
License: GPLv3
Text Domain: black-studio-tinymce-widget
Domain Path: /languages
*/

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/* Get plugin version */
function black_studio_tinymce_get_version() {
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_version = $plugin_data['Version'];
	return $plugin_version;
}

define( 'BLACK_STUDIO_TINYMCE_DIR', plugin_dir_path( __FILE__ ) );
define( 'BLACK_STUDIO_TINYMCE_URL', plugin_dir_url( __FILE__ ) );

require_once 'classes/class-wp-widget-black-studio-tinymce.php';
require_once 'classes/class-black-studio-tinymce.php';

$black_studio_tinymce = new Black_Studio_TinyMCE();
