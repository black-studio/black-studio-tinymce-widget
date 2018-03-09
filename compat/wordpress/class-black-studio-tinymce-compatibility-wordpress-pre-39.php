<?php
/**
 * Black Studio TinyMCE Widget - Compatibility with WordPress versions prior to 3.9
 *
 * @package Black_Studio_TinyMCE_Widget
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Black_Studio_TinyMCE_Compatibility_WordPress_Pre_39' ) ) {

	/**
	 * Class that provides compatibility code for WordPress versions prior to 3.9
	 *
	 * @package Black_Studio_TinyMCE_Widget
	 * @since 3.0.0
	 */
	final class Black_Studio_TinyMCE_Compatibility_WordPress_Pre_39 {

		/**
		 * The single instance of the class
		 *
		 * @var object
		 * @since 3.0.0
		 */
		protected static $_instance = null;

		/**
		 * Return the single class instance
		 *
		 * @return object
		 * @since 3.0.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Class constructor
		 *
		 * @uses is_admin()
		 * @uses add_action()
		 *
		 * @since 3.0.0
		 */
		protected function __construct() {
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ), 39 );
			}
		}

		/**
		 * Prevent the class from being cloned
		 *
		 * @return void
		 * @since 3.0.0
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; uh?' ), '3.0' );
		}

		/**
		 * Admin init
		 *
		 * @uses add_action()
		 * @uses remove_action()
		 * @uses add_filter()
		 * @uses get_bloginfo()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function admin_init() {
			$wp_version = get_bloginfo( 'version' );
			if ( bstw()->admin()->enabled() ) {
				add_filter( 'black_studio_tinymce_widget_script', array( $this, 'handle' ), 61 );
				add_filter( 'black_studio_tinymce_widget_script_path', array( $this, 'path' ), 61 );
				add_filter( 'tiny_mce_before_init', array( $this, 'tiny_mce_before_init' ), 61 );
				add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
				remove_action( 'admin_print_footer_scripts', array( bstw()->admin(), 'admin_print_footer_scripts' ) );
				if ( version_compare( $wp_version, '3.3', '<' ) ) {
					remove_filter( 'black_studio_tinymce_widget_script_path', array( bstw()->compatibility()->module( 'pre_33' ), 'path' ), 67 );
				}
				add_action( 'black_studio_tinymce_editor', array( $this, 'editor' ), 10, 4 );
				remove_action( 'black_studio_tinymce_editor', array( bstw()->admin(), 'editor' ), 10, 3 );
			}
		}

		/**
		 * Filter to enqueue style / script
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function handle() {
			return 'black-studio-tinymce-widget-pre39';
		}

		/**
		 * Filter for styles / scripts path
		 *
		 * @param string $path Path for styles / scripts.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function path( $path ) {
			return 'compat/wordpress/' . $path;
		}

		/**
		 * TinyMCE initialization
		 *
		 * @param mixed[] $settings Array of settings.
		 * @return mixed[]
		 * @since 3.0.0
		 */
		public function tiny_mce_before_init( $settings ) {
			$custom_settings = array(
				'remove_linebreaks'       => false,
				'convert_newlines_to_brs' => false,
				'force_p_newlines'        => true,
				'force_br_newlines'       => false,
				'remove_redundant_brs'    => false,
				'forced_root_block'       => 'p',
				'apply_source_formatting' => true,
			);
			// Return modified settings.
			return array_merge( $settings, $custom_settings );
		}

		/**
		 * Enqueue footer scripts
		 *
		 * @uses wp_editor()
		 *
		 * @return void
		 * @since 3.0.0
		 */
		public function admin_print_footer_scripts() {
			if ( function_exists( 'wp_editor' ) ) {
				wp_editor( '', 'black-studio-tinymce-widget' );
			}
		}

		/**
		 * Output the visual editor code
		 *
		 * @uses esc_attr()
		 * @uses esc_textarea()
		 * @uses esc_html_e()
		 * @uses do_action()
		 *
		 * @param string $text Widget text.
		 * @param string $id   Widget ID.
		 * @param string $name Widget name.
		 * @param string $type Widget type.
		 * @return void
		 * @since 3.0.0
		 */
		public function editor( $text, $id, $name = '', $type = 'visual' ) {
			$switch_class = 'visual' === $type ? 'html-active' : 'tmce-active';
			?>
			<div id="<?php echo esc_attr( $id ); ?>-wp-content-wrap" class="wp-core-ui wp-editor-wrap <?php echo esc_attr( $switch_class ); ?> has-dfw">
				<div id="<?php echo esc_attr( $id ); ?>-wp-content-editor-tools" class="wp-editor-tools hide-if-no-js">
					<div class="wp-editor-tabs">
						<a id="<?php echo esc_attr( $id ); ?>-content-html" class="wp-switch-editor switch-html"><?php esc_html_e( 'HTML' ); ?></a>
						<a id="<?php echo esc_attr( $id ); ?>-content-tmce" class="wp-switch-editor switch-tmce"><?php esc_html_e( 'Visual' ); ?></a>
					</div>
					<div id="<?php esc_attr( $id ); ?>-wp-content-media-buttons" class="wp-media-buttons">
						<?php do_action( 'media_buttons', $id ); ?>
					</div>
				</div>
				<div class="wp-editor-container">
					<textarea class="widefat" rows="20" cols="40" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_textarea( $text ); ?></textarea>
				</div>
			</div>
			<?php
		}

	} // END class Black_Studio_TinyMCE_Compatibility_WordPress_Pre_39

} // END class_exists check
