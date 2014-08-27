<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Global var with plugin version for backward compatibility
 *
 * @deprecated 2.0.0
 */
$black_studio_tinymce_widget_version = Black_Studio_TinyMCE_Plugin::get_version();

/**
 * @deprecated 2.0.0
 */
function black_studio_tinymce_load_tiny_mce() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->enqueue_media()' );
	bstw()->enqueue_media();
}

/**
 * @deprecated 2.0.0
 */
function black_studio_tinymce_init_editor( $arg ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->tiny_mce_before_init( ... )' );
	return bstw()->tiny_mce_before_init( $arg );
}

/**
 * @deprecated 2.0.0
 */
function black_studio_tinymce_styles() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin_print_styles()' );
	bstw()->admin_print_styles();
}

/**
 * @deprecated 2.0.0
 */
function black_studio_tinymce_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin_print_scripts()' );
	bstw()->admin_print_scripts();
}

/**
 * @deprecated 2.0.0
 */
function black_studio_tinymce_footer_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->admin_print_footer_scripts()' );
	bstw()->admin_print_footer_scripts();
}

/**
 * @deprecated 2.0.0
 */
function black_studio_tinymce_get_version() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'bstw()->get_version()' );
	bstw()->get_version();
}
