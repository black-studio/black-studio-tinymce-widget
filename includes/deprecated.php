<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

global $black_studio_tinymce_widget_version;
$black_studio_tinymce_widget_version = BLACK_STUDIO_TINYMCE_WIDGET_VERSION;

function black_studio_tinymce_load_tiny_mce() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'global $black_studio_tinymce; $black_studio_tinymce->load_tiny_mce()' );
	global $black_studio_tinymce;
	$black_studio_tinymce->load_tiny_mce();
}

function black_studio_tinymce_init_editor( $arg ) {
	_deprecated_function( __FUNCTION__, '2.0.0', 'global $black_studio_tinymce; $black_studio_tinymce->init_editor()' );
	global $black_studio_tinymce;
	return $black_studio_tinymce->init_editor( $arg );
}

function black_studio_tinymce_styles() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'global $black_studio_tinymce; $black_studio_tinymce->admin_print_styles()' );
	global $black_studio_tinymce;
	$black_studio_tinymce->admin_print_styles();
}

function black_studio_tinymce_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'global $black_studio_tinymce; $black_studio_tinymce->admin_print_scripts()' );
	global $black_studio_tinymce;
	$black_studio_tinymce->admin_print_scripts();
}

function black_studio_tinymce_footer_scripts() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'global $black_studio_tinymce; $black_studio_tinymce->admin_print_footer_scripts()' );
	global $black_studio_tinymce;
	$black_studio_tinymce->admin_print_footer_scripts();
}

function black_studio_tinymce_get_version() {
	_deprecated_function( __FUNCTION__, '2.0.0', 'global $black_studio_tinymce; $black_studio_tinymce->get_version()' );
	global $black_studio_tinymce;
	$black_studio_tinymce->get_version();
}