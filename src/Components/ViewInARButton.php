<?php
/**
 * View in AR button component.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Components;

use Aryel\AryelAR3DProductViewer\Utils\Button;

/**
 * View in AR button component class.
 */
class ViewInARButton extends AbstractComponent {

	/**
	 * Initialize the component.
	 *
	 * @return void
	 */
	public static function initialize() {
		global $post;

		$is_view_in_ar_enabled = (bool) get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_view_in_ar_toggle', true );
		if ( $is_view_in_ar_enabled && self::is_campaign_id_set() ) {
			$hook = get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_view_in_ar_button_hook', true );
			self::register_button_hook( $hook );
		}
	}

	/**
	 * Register the button hook.
	 *
	 * @param string $hook The hook.
	 * @return void
	 */
	public static function register_button_hook( $hook ) {
		switch ( $hook ) {
			case 'woocommerce_product_meta_end':
				add_action(
					'woocommerce_product_meta_end',
					function () {
						self::render();
					}
				);
				break;

			case 'woocommerce_short_description':
				add_filter(
					'woocommerce_short_description',
					function ( $desc ) {
						return $desc . self::get_render();
					},
					20,
					1
				);
				break;
		}
	}

	/**
	 * Render the component.
	 *
	 * @return void
	 */
	public static function render() {
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/components/view-in-ar-button.php';
	}

	/**
	 * Render the button.
	 *
	 * @return void
	 */
	public static function render_button() {
		global $post;

		Button::render( 'view_in_ar', $post );
	}

	/**
	 * Get the rendered component.
	 *
	 * @return string
	 */
	public static function get_render() {
		ob_start();
		self::render();
		return ob_get_clean();
	}
}
