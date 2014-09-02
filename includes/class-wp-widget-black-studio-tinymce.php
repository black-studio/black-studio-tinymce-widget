<?php

/**
 * Widget class
 *
 * @package Black_Studio_TinyMCE_Widget
 * @since 0.5
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Widget_Black_Studio_TinyMCE' ) ) {

	class WP_Widget_Black_Studio_TinyMCE extends WP_Widget {

		/**
		 * Widget Class constructor
		 *
		 * @uses WP_Widget::__construct()
		 * @return void
		 * @since 0.5
		 */
		public function __construct() {
			$widget_ops = array( 'classname' => 'widget_black_studio_tinymce', 'description' => __( 'Arbitrary text or HTML with visual editor', 'black-studio-tinymce-widget' ) );
			$control_ops = array( 'width' => 800, 'height' => 600 );
			parent::__construct( 'black-studio-tinymce', __( 'Visual Editor', 'black-studio-tinymce-widget' ), $widget_ops, $control_ops );
		}

		/**
		 * Output widget HTML code
		 *
		 * @uses apply_filters()
		 * @uses wp_kses_post()
		 * @uses WP_Widget::$id_base
		 *
		 * @param string[] $args
		 * @param mixed[] $instance
		 * @return void
		 * @since 0.5
		 */
		public function widget( $args, $instance ) {
			$before_widget = $args['before_widget'];
			$after_widget = $args['after_widget'];
			$before_title = $args['before_title'];
			$after_title = $args['after_title'];
			$before_text = apply_filters( 'black_studio_tinymce_before_text', '<div class="textwidget">', $instance );
			$after_text = apply_filters( 'black_studio_tinymce_after_text', '</div>', $instance );
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance, $this );
			$output = $before_widget;
			if ( ! empty( $title ) ) {
				$output .= $before_title . $title . $after_title;
			}
			$output .= $before_text . $text . $after_text;
			$output .= $after_widget;
			echo wp_kses_post( $output );
		}

		/**
		 * Update widget data
		 *
		 * @uses current_user_can()
		 * @uses wp_filter_post_kses()
		 * @uses apply_filters()
		 *
		 * @param mixed[] $new_instance
		 * @param mixed[] $old_instance
		 * @return mixed[]
		 * @since 0.5
		 */
		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['text'] = $new_instance['text'];
			}
			else {
				$instance['text'] = stripslashes( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) ); // wp_filter_post_kses() expects slashed
			}
			$instance['type'] = strip_tags( $new_instance['type'] );
			$instance = apply_filters( 'black_studio_tinymce_widget_update',  $instance, $this );
			return $instance;
		}

		/**
		 * Output widget form
		 *
		 * @uses wp_parse_args()
		 * @uses apply_filters()
		 * @uses esc_attr()
		 * @uses esc_textarea()
		 * @uses WP_Widget::get_field_id()
		 * @uses WP_Widget::get_field_name()
		 * @uses _e()
		 * @uses do_action()
		 * @uses apply_filters()
		 *
		 * @param mixed[] $instance
		 * @return void
		 * @since 0.5
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'type' => 'visual' ) );
			$title = strip_tags( $instance['title'] );
			$text = $instance['text'];
			$type = $instance['type'];
			$switch_class = $type == 'visual' ? 'html-active' : 'tmce-active';
			?>
			<input id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" type="hidden" value="<?php echo esc_attr( $type ); ?>" />
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<?php /*
            <div id="<?php echo $this->get_field_id( 'text' ); ?>-wp-content-wrap" class="wp-core-ui wp-editor-wrap <?php echo esc_attr( $switch_class ); ?> has-dfw">
				<div id="<?php echo $this->get_field_id( 'text' ); ?>-wp-content-editor-tools" class="wp-editor-tools hide-if-no-js">
					<div class="wp-editor-tabs">
						<a id="<?php echo $this->get_field_id( 'text' ); ?>-content-html" class="wp-switch-editor switch-html"><?php _e( 'HTML' ); ?></a>
						<a id="<?php echo $this->get_field_id( 'text' ); ?>-content-tmce" class="wp-switch-editor switch-tmce"><?php _e( 'Visual' ); ?></a>
					</div>
					<div id="<?php echo $this->get_field_id( 'text' ); ?>-wp-content-media-buttons" class="wp-media-buttons">
						<?php do_action( 'media_buttons', $this->get_field_id( 'text' ) ); ?>
					</div>
				</div>
				<div class="wp-editor-container">
					<textarea class="widefat" rows="20" cols="40" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
				</div>
			</div>
			*/
			?>
			<?php
			$editor_settings = array(
				'default_editor' => 'html',
				'tinymce' => array( 'wp_skip_init' => true ),
				'textarea_name' => $this->get_field_name( 'text' ),
				'editor_height' => 250,
			);
            wp_editor( $text, $this->get_field_id( 'text' ), $editor_settings );
			?>
			<div class="bstw-links">
				<?php if ( false == apply_filters( 'black_studio_tinymce_whitelabel', false ) ) { // consider donating if you whitelabel ?>
				<a href="http://www.blackstudio.it/en/wordpress-plugins/black-studio-tinymce-widget/" target="_blank"><?php _e( 'Donate', 'black-studio-tinymce-widget' ); ?></a> |
				<a href="http://wordpress.org/support/plugin/black-studio-tinymce-widget" target="_blank"><?php _e( 'Support', 'black-studio-tinymce-widget' ); ?></a> |
				<a href="http://wordpress.org/support/view/plugin-reviews/black-studio-tinymce-widget" target="_blank"><?php _e( 'Rate', 'black-studio-tinymce-widget' ); ?></a> |
				<a href="https://twitter.com/blackstudioita" target="_blank"><?php _e( 'Follow', 'black-studio-tinymce-widget' ); ?></a>
				<?php } // END if | filter whitelabel ?>
			</div>
			<?php
		}

	} // END class WP_Widget_Black_Studio_TinyMCE

} // class_exists check
