<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $black_studio_tinymce_widget_version;
$black_studio_tinymce_widget_version = BSTW()->get_version();

function black_studio_tinymce_load_tiny_mce() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'BSTW()->load_tiny_mce()' );
	BSTW()->load_tiny_mce();
}

function black_studio_tinymce_init_editor( $arg ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'BSTW()->tiny_mce_before_init( ... )' );
	return BSTW()->tiny_mce_before_init( $arg );
}

function black_studio_tinymce_styles() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'BSTW()->admin_print_styles()' );
	BSTW()->admin_print_styles();
}

function black_studio_tinymce_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'BSTW()->admin_print_scripts()' );
	BSTW()->admin_print_scripts();
}

function black_studio_tinymce_footer_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'BSTW()->admin_print_footer_scripts()' );
	BSTW()->admin_print_footer_scripts();
}

function black_studio_tinymce_get_version() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'BSTW()->get_version()' );
	BSTW()->get_version();
}
