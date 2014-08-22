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
		}

	} // class declaration

} // class_exists check
