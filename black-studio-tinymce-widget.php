<?php
/*
Plugin Name: Black Studio TinyMCE Widget
Plugin URI: http://www.blackstudio.it
Description: This plugins adds a new widget type, which allows you to insert html rich text using the visual TinyMCE editor in a WYSIWYG way.
Version: 0.5
Author: Black Studio
Author URI: http://www.blackstudio.it
License: GPL2
*/


/* Widget class */
class WP_Widget_Black_Studio_TinyMCE extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_black_studio_tinymce', 'description' => __('Arbitrary text or HTML with visual editor', 'black-studio-tinymce-widget'));
		$control_ops = array('width' => 600, 'height' => 500);
		parent::__construct('black-studio-tinymce', __('Black Studio TinyMCE', 'black-studio-tinymce-widget'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters( 'widget_text', $instance['text'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['type'] = strip_tags($new_instance['type']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'type' => 'visual' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
		$type = esc_textarea($instance['type']);
?>
		<input id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" type="hidden" value="<?php echo esc_attr($type); ?>" />
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
        <div class="editor_toggle_buttons">
            <a id="widget-<?php echo $this->id_base; ?>-<?php echo $this->number; ?>-html"<?php if ($type == 'html') {?> class="active"<?php }?>><?php _e('HTML'); ?></a>
            <a id="widget-<?php echo $this->id_base; ?>-<?php echo $this->number; ?>-visual"<?php if($type == 'visual') {?> class="active"<?php }?>><?php _e('Visual'); ?></a>
        </div>
		<div class="editor_container">
			<textarea class="widefat" rows="16" cols="40" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
        </div>
        <?php
		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && $type == 'visual') {
?>
		<script type="text/javascript" language="javascript">
			/* <![CDATA[ */
			black_studio_activate_visual_editor('<?php echo $this->get_field_id('text'); ?>');
			/* ]]> */
        </script>
<?php
		}
	}
}

/* Instantiate tinyMCE editor */
add_action('admin_head', 'black_studio_tinymce_load_tiny_mce');
function black_studio_tinymce_load_tiny_mce() {
	remove_all_filters('mce_external_plugins');
	wp_tiny_mce(false, array('height' => 350));
}

/* tinyMCE setup customization */
add_filter('tiny_mce_before_init', 'black_studio_tinymce_init_editor');
function black_studio_tinymce_init_editor($initArray) {
	// Remove WP fullscreen mode and set the native tinyMCE one
	$plugins = explode(',', $initArray['plugins']);
	if (isset($plugins['wpfullscreen'])) {
		unset($plugins['wpfullscreen']);
	}
	if (!isset($plugins['fullscreen'])) {
		$plugins[] = 'fullscreen';
	}
	$initArray['plugins'] = implode(',', $plugins);
	// add "image" button and remove "more"
	$initArray['theme_advanced_buttons1'] = str_replace("wp_more", "image", $initArray['theme_advanced_buttons1']);
	// return modified settings
	return $initArray;
}

/* Widget initialization */
add_action('widgets_init', 'black_studio_tinymce_init');
function black_studio_tinymce_init() {
	if ( !is_blog_installed() )
		return;
	register_widget('WP_Widget_Black_Studio_TinyMCE');
}

/* Widget js loading */
add_action("admin_print_scripts", "black_studio_tinymce_scripts");
function black_studio_tinymce_scripts() {
    wp_enqueue_script('tiny_mce');
    wp_enqueue_script('black-studio-tinymce-widget', plugins_url('black-studio-tinymce-widget.js', __FILE__));
}

/* Widget css loading */
add_action("admin_print_styles", "black_studio_tinymce_styles");
function black_studio_tinymce_styles() {
    wp_enqueue_style('black-studio-tinymce-widget', plugins_url('black-studio-tinymce-widget.css', __FILE__));
}

/* Load translations */
load_plugin_textdomain('black-studio-tinymce-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
?>