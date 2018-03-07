<?php
/**
 * Black Studio TinyMCE Widget - Deprecated functions for backward compatibility
 *
 * @package Black_Studio_TinyMCE_Widget
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global var with plugin version for backward compatibility
 *
 * @since 0.6.3
 * @deprecated 2.0.0
 */
global $black_studio_tinymce_widget_version; // global is necessary because this file is included in a non-global context.
$black_studio_tinymce_widget_version = Black_Studio_TinyMCE_Plugin::$version;

/**
 * Global var used for development
 *
 * @since 0.9.4
 * @deprecated 1.4
 */
global $black_studio_tinymce_widget_dev_mode; // global is necessary because this file is included in a non-global context.
$black_studio_tinymce_widget_dev_mode = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

/**
 * Get plugin version
 *
 * @since 1.4.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_get_version() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->get_version()' );
	bstw()->get_version();
}

/**
 * Widget initialization
 *
 * @since 0.7.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_widgets_init() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->widgets_init()' );
	bstw()->widgets_init();
}

/**
 * Admin initialization
 *
 * @since 0.8.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_admin_init() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin()->admin_init()' );
	bstw()->admin()->admin_init();
}

/**
 * Enqueue TinyMCE stuff
 *
 * @since 0.5.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_load_tiny_mce() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin()->enqueue_media()' );
	bstw()->admin()->enqueue_media();
}

/**
 * TinyMCE editor initialization
 *
 * @param mixed[] $arg Array of arguments.
 * @since 0.5.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_init_editor( $arg ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
	return $arg;
}

/**
 * Enqueue plugin's admin styles
 *
 * @since 0.5.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_styles() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin()->admin_print_styles()' );
	bstw()->admin()->admin_print_styles();
}

/**
 * Enqueue plugin's admin scripts
 *
 * @since 0.5.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin()->admin_print_scripts()' );
	bstw()->admin()->admin_print_scripts();
}

/**
 * Enqueue plugin's admin footer scripts
 *
 * @since 0.7.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_footer_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin()->admin_print_footer_scripts()' );
	bstw()->admin()->admin_print_footer_scripts();
}

/**
 * Enqueue media dialog stuff
 *
 * @since 0.6.0
 * @deprecated 0.7.0
 */
function black_studio_tinymce_preload_dialogs() {
	_deprecated_function( __FUNCTION__, '0.7', 'bstw()->admin()->admin_print_footer_scripts()' );
	bstw()->admin()->admin_print_footer_scripts();
}

/**
 * Apply smilies to widget text
 *
 * @uses get_option()
 *
 * @param string $text Widget text.
 * @since 1.3.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_apply_smilies_to_widget_text( $text ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->text_filters()->convert_smilies( ... )' );
	if ( get_option( 'use_smilies' ) ) {
		$text = bstw()->text_filters()->convert_smilies( $text );
	}
	return $text;
}

/**
 * Enable full media options in upload dialog for WordPress prior to 3.5
 *
 * @param mixed[] $arg Array of arguments.
 * @since 1.0.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_upload_iframe_src( $arg ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->compatibility()->wordpress()->wp_pre_35_upload_iframe_src( ... )' );
	bstw()->compatibility()->wordpress()->wp_pre_35_upload_iframe_src( $arg );
}

/**
 * Enable accessibility mode
 *
 * @param mixed[] $editor Editor instance.
 * @since 1.2.0
 * @deprecated 2.0.0
 */
function black_studio_tinymce_editor_accessibility_mode( $editor ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin()->editor_accessibility_mode( ... )' );
	bstw()->admin()->editor_accessibility_mode( $editor );
}

/**
 * Compatibility with Page Builder by SiteOrigin
 *
 * @param object $the_widget Widget object.
 * @since 1.4.5
 * @deprecated 2.0.0
 */
function black_studio_tinymce_siteorigin_panels_widget_object( $the_widget ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()-compatibility()->plugins()->siteorigin_panels_widget_object( ... )' );
	bstw()->compatibility()->plugins()->siteorigin_panels_widget_object( $the_widget );
}
