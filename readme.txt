=== Black Studio TinyMCE Widget ===
Contributors: marcochiesi, thedarkmist
Donate link: http://www.blackstudio.it/en/wordpress-plugins/black-studio-tinymce-widget/
Tags: wysiwyg, widget, tinymce, editor, image, media, rich text, rich text editor, visual editor, wysiwyg editor, tinymce editor, widget editor, html editor, wysiwyg widget, html widget, editor widget, text widget, rich text widget, enhanced text widget, tinymce widget, visual widget, image widget, media widget
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 1.2.0

Adds a WYSIWYG widget based on the standard TinyMCE WordPress visual editor.

== Description ==
This plugin adds a WYSIWYG text widget based on the standard TinyMCE WordPress visual editor. This is intended to overcome the limitations of the default WordPress text widget, so that you can visually add rich text contents to your sidebars, with no knowledge of HTML required.

= Features =

* Add rich text widgets to your sidebar using visual editor
* Switch between Visual mode and HTML mode
* Insert images/videos from Wordpress Media Library
* Insert links to existing Wordpress pages/posts
* Fullscreen editing mode supported
* Widgets accessibility mode supported
* Wordpress networks (Multi-site) supported
* WPML (Multi-language) supported

= Links =

* [Plugin's web page](http://www.blackstudio.it/en/wordpress-plugins/black-studio-tinymce-widget/)
* [Support forum](http://wordpress.org/support/plugin/black-studio-tinymce-widget)
* [Follow us on Twitter](https://twitter.com/blackstudioita)

== Installation ==

This section describes how to install and use the plugin.

1. Install automatically through the `Plugins` menu and `Add New` button (or upload the entire `black-studio-tinymce-widget` folder to the `/wp-content/plugins/` directory)
2. Activate the plugin
3. Go to `Appearance` -> `Widgets`
4. Drag and drop the `Black Studio TinyMCE Widget` to the desired sidebar (or, if using Accesibility mode, click the `Add` link)
5. Fill in title and (rich) text

== Screenshots ==

1. Black Studio TinyMCE Widget editor window
2. Black Studio TinyMCE Widget combined with Ultimate TinyMCE plugin

== Frequently Asked Questions ==

= How to translate widgets using WPML =

The current version of `Black Studio TinyMCE Widget` plugin works together with the `WPML String translation` plugin. To translate a widget created with `Black Studio TinyMCE Widget` plugin you have to create it on the widgets panel and then go to `WPML` -> `String translation` and translate the title and text from there (unfortunately the translation textarea has no wysywyg editor).
Note: If you installed WPML after the creation of the widgets, just re-save them and they will appear on the String translation list.

= The plugin doesn't work as expected =

Please post a message in the [Support forum](http://wordpress.org/support/plugin/black-studio-tinymce-widget), providing the following information:

* Description of the problem and steps to reproduce it
* Error messages if any (in particular in browser's javascript console)
* Browser / OS in use
* Wordpress version in use
* Wordpress theme in use
* List of other Wordpress plugins installed

= How to embed video and other contents =

Wordpress has a nice [autoembed feature](http://codex.wordpress.org/Embeds) that allows you to embed videos and other stuff in an easy way, by just putting the URL in the content area. This is also possible for widgets created with this plugin, but for best results it is recommended to put the URL inside an `[embed]` shortcode. Example: `[embed]http://www.youtube.com/watch?v=XXXXXXXXXXX[/embed]`. Ensure that the URL has not an hyperlink on it.
Alternatively, if you don't want to use `[embed]` shortcode, ensure that the URL is not surrounded by a `<p>` tag, which is added automatically by the WYSIWYG editor (switch to HTML mode to remove enclosing `<p>` tags).

== Changelog ==

= 1.2.0 =
* Fixed issue with Wordpress widgets accessibility mode
* Fixed compatibility issue with WPML plugin generating an error in debug mode
* Fixed compatibility issue with WP Page Widget plugin
* Added slovak translation (Contributor: Branco Radenovich - [WebHostingGeeks.com](http://webhostinggeeks.com/user-reviews/))
* Tested compatibility with Worpdress 3.6 beta

= 1.1.1 =
* Fixed editor issue when dragging widgets from a sidebar to another

= 1.1.0 =
* Compatibility fixes for upcoming Wordpress 3.5
* Added support for the new Wordpress media library dialog
* Enhanced javascript event handling using jquery .on(...) method

= 1.0.0 =
* Added full image options when adding content from media library
* Added german translation (Contributor: Christian Foellmann)
* Overall Javascript code optimization
* Better Javascript compression
* Fixed editor background color
* Fixed compatibility issue with "WP Page Widget" plugin
* Fixed issue about editor partially hidden on narrow screens

= 0.9.5 =
* Added support for autoembed urls (youtube, etc)

= 0.9.4 =
* Bug fixes

= 0.9.3 =
* Added support for accessibility mode

= 0.9.2 =
* Optimized for use in conjunction with Ultimate TinyMCE plugin

= 0.9.1 =
* Added spanish translation (Contributor: Lucia García Martínez)
* Increased width of editor window

= 0.9 =
* Added support for WPML plugin (for multilanguage sites)

= 0.8.2 =
* Added support for shortcodes in widget text

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