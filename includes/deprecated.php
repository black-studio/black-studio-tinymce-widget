<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

function black_studio_tinymce_load_tiny_mce() {
	global $black_studio_tinymce;
	$black_studio_tinymce->load_tiny_mce();
}

function black_studio_tinymce_init_editor( $arg ) {
	global $black_studio_tinymce;
	$black_studio_tinymce->init_editor( $arg );
}

function black_studio_tinymce_scripts() {
	global $black_studio_tinymce;
	$black_studio_tinymce->admin_print_scripts();
}

function black_studio_tinymce_styles() {
	global $black_studio_tinymce;
	$black_studio_tinymce->admin_print_styles();
}

function black_studio_tinymce_footer_scripts() {
	global $black_studio_tinymce;
	$black_studio_tinymce->admin_print_footer_scripts();
}

function black_studio_tinymce_get_version() {
	global $black_studio_tinymce;
	$black_studio_tinymce->load_tiny_mce();
}