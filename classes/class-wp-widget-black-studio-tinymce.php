<?php

/**
* Widget class
*
* @package Black Studio TinyMCE Widget
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Widget_Black_Studio_TinyMCE' ) ) {

	class WP_Widget_Black_Studio_TinyMCE extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname' => 'widget_black_studio_tinymce', 'description' => __( 'Arbitrary text or HTML with visual editor', 'black-studio-tinymce-widget' ) );
			$control_ops = array( 'width' => 800, 'height' => 800 );
			parent::__construct( 'black-studio-tinymce', __( 'Visual Editor', 'black-studio-tinymce-widget' ), $widget_ops, $control_ops );
		}

		function widget( $args, $instance ) {
			if ( get_option( 'embed_autourls' ) ) {
				$wp_embed = $GLOBALS['wp_embed'];
				add_filter( 'widget_text', array( $wp_embed, 'run_shortcode' ), 8 );
				add_filter( 'widget_text', array( $wp_embed, 'autoembed' ), 8 );
			}
			$before_widget = $args['before_widget'];
			$after_widget = $args['after_widget'];
			$before_title = $args['before_title'];
			$after_title = $args['after_title'];
			$before_text = apply_filters( 'black_studio_tinymce_before_text', '<div class="textwidget">', $instance );
			$after_text = apply_filters( 'black_studio_tinymce_after_text', '</div>', $instance );
			$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base );
			$text = apply_filters( 'widget_text', $instance['text'], $instance );
			if ( function_exists( 'icl_t' )  && ! empty( $this->number ) ) {
				$title = icl_t( 'Widgets', 'widget title - ' . md5( $title ), $title );
				$text = icl_t( 'Widgets', 'widget body - ' . $this->id_base . '-' . $this->number, $text );
			}
			$text = do_shortcode( $text );
			$output = '';
			$output .= $before_widget;
			if ( ! empty( $title ) ) {
				$output .= $before_title . $title . $after_title;
			}
			$output .= $before_text . $text . $after_text;
			$output .= $after_widget;
			echo wp_kses_post( $output );
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['text'] = $new_instance['text'];
			}
			else {
				$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) ); // wp_filter_post_kses() expects slashed
			}
			$instance['type'] = strip_tags( $new_instance['type'] );
			if ( function_exists( 'icl_register_string' ) && ! empty( $this->number ) ) {
				//icl_register_string( "Widgets", 'widget title - ' . $this->id_base . '-' . $this->number /* md5 ( apply_filters( 'widget_title', $instance['title'] ))*/, apply_filters( 'widget_title', $instance['title'] ) ); // This is handled automatically by WPML
				icl_register_string( 'Widgets', 'widget body - ' . $this->id_base . '-' . $this->number, apply_filters( 'widget_text', $instance['text'] ) );
			}
			return $instance;
		}

		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'type' => 'visual' ) );
			$title = strip_tags( $instance['title'] );
			$text = $instance['text'];
			$type = $instance['type'];
			$switch_class = $type == 'visual' ? 'html-active' : 'tmce-active';
			if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
				$toggle_buttons_class = 'editor_toggle_buttons_legacy';
				$media_buttons_class = 'editor_media_buttons_legacy';
			}
			else if ( version_compare( get_bloginfo( 'version' ), '3.8', '<' ) ) {
				$toggle_buttons_class = 'wp-toggle-buttons';
				$media_buttons_class = 'wp-media-buttons';
			}
			else {
				$toggle_buttons_class = 'wp-editor-tabs';
				$media_buttons_class = 'wp-media-buttons';
			}

			?>
			<input id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" type="hidden" value="<?php echo esc_attr( $type ); ?>" />
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<div id="wp-<?php echo $this->get_field_id( 'text' ); ?>-wrap" class="wp-core-ui wp-editor-wrap <?php echo esc_attr( $switch_class ); ?>">
				<div class="wp-editor-tools hide-if-no-js">
					<div class="<?php echo esc_attr( $toggle_buttons_class ); ?>">
						<a id="<?php echo $this->get_field_id( 'text' ); ?>-html" class="wp-switch-editor switch-html"><?php _e( 'HTML' ); ?></a>
						<a id="<?php echo $this->get_field_id( 'text' ); ?>-tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
					</div>
					<div class="<?php echo esc_attr( $media_buttons_class ); ?>">
						<?php do_action( 'media_buttons', $this->get_field_id( 'text' ) ); ?>
					</div>
				</div>
				<div class="wp-editor-container">
					<textarea class="widefat" rows="20" cols="40" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
				</div>
			</div>
			<div class="wp-editor-bstw-links" style="font-size: 10px; text-align: right; padding: 5px 0;">
				<a href="http://www.blackstudio.it/en/wordpress-plugins/black-studio-tinymce-widget/" target="_blank"><?php _e( 'Donate', 'black-studio-tinymce-widget' ); ?></a> | <a href="http://wordpress.org/support/plugin/black-studio-tinymce-widget" target="_blank"><?php _e( 'Support', 'black-studio-tinymce-widget' ); ?></a> | <a href="http://wordpress.org/support/view/plugin-reviews/black-studio-tinymce-widget" target="_blank"><?php _e( 'Rate', 'black-studio-tinymce-widget' ); ?></a> | <a href="https://twitter.com/blackstudioita" target="_blank"><?php _e( 'Follow', 'black-studio-tinymce-widget' ); ?></a>
			</div>	<?php
		}

	} // class declaration

} // class_exists check
