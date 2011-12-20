=== Black Studio TinyMCE Widget ===
Contributors: marcochiesi, thedarkmist
Donate link: http://www.blackstudio.it/pagamento/
Tags: wysiwyg, widget, tinymce, editor, image, media, rich text, rich text editor, visual editor, wysiwyg editor, tinymce editor, widget editor, html editor, wysiwyg widget, html widget, editor widget, text widget, rich text widget, enhanced text widget, tinymce widget, visual widget, image widget, media widget
Requires at least: 3.0
Tested up to: 3.3
Stable tag: 0.8.1

Adds a WYSIWYG widget based on the standard TinyMCE WordPress visual editor.

== Description ==
This plugin adds a WYSIWYG text widget based on the standard TinyMCE WordPress visual editor. This is intended to overcome the limitations of the default WordPress text widget, so that you can visually add rich text contents to your sidebars, with no knowledge of HTML required.

= Features =

* Add rich text widgets to your sidebar using visual editor
* Switch between Visual mode and HTML mode
* Insert images/videos from Wordpress Media Library
* Insert links to existing Wordpress pages/posts
* Fullscreen editing mode supported
* Wordpress networks (Multisite) supported
* No annoying ads/nag-screens

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the entire `black-studio-tinymce-widget` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Widgets Screen
4. Drag Widget to desired sidebar
5. Fill in the widget title and (rich) text

== Screenshots ==

1. A screenshot of the TinyMCE Widget

== Changelog ==

= 0.8.1 =
* Fixed issue when inserting images on Wordpress 3.3

= 0.8 =
* Added support for Wordpress networks (Multisite)

= 0.7 =
* Added compatibility for upcoming Wordpress 3.3
* Added compatibility for previous Wordpress 3.0 and 3.1
* Optimization/compression of javascript code

= 0.6.5 =
* Forced TinyMCE editor to not automatically add/remove paragraph tags when switching to HTML mode (you may need to re-edit your widgets to adjust linebreaks, if you were using multiple paragraphs)

= 0.6.4 =
* Fixed compatibility issue with "Jetpack / After the Deadline" plugin
* Optimization of javascript/css loading

= 0.6.3 =
* Fixed javascript issue preventing the plugin from working correctly with some browsers

= 0.6.2 =
* Fixed javascript issue with Wordpress Media Library inserts in HTML mode

= 0.6.1 =
* Fixed javascript issue preventing editor to show up in some cases

= 0.6 =
* Added support for Wordpress Media Library

= 0.5 =
* First Beta release