<?php
/**
 * Product miniature component.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Components;

use Aryel\AryelAR3DProductViewer\Utils\EmbedViewer;

/**
 * Product miniature component.
 */
class ProductMiniature extends AbstractComponent {

	protected const HOOK          = 'woocommerce_before_shop_loop_item_title';
	protected const HOOK_PRIORITY = 10;

	/**
	 * Initialize the component.
	 *
	 * @return void
	 */
	public static function initialize() {
		global $wp_filter;

		// Remove hooks registered by WooCommerce and or other plugins/themes.
		$callbacks = array();
		foreach ( $wp_filter[ self::HOOK ]->callbacks[ self::HOOK_PRIORITY ] as $callback ) {
			$callbacks[] = $callback;
			unset( $wp_filter[ self::HOOK ]->callbacks[ self::HOOK_PRIORITY ] );
		}

		add_action(
			'woocommerce_before_shop_loop_item_title',
			function () use ( $callbacks ) {
				global $post;

				$is_product_miniature_enabled = (bool) get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_product_miniature_toggle', true );
				if ( ! $is_product_miniature_enabled || ! self::is_surface() || ! self::is_campaign_id_set() ) {
					foreach ( $callbacks as $callback ) {
						$callback['function']();
					}
					return;
				}

				self::render();
			},
			10
		);

		add_filter(
			'woocommerce_post_class',
			function ( $classes, $product ) {
				$is_product_miniature_enabled = (bool) get_post_meta( $product->get_id(), '_aryel_ar_3d_product_viewer_product_miniature_toggle', true );
				if ( $is_product_miniature_enabled && self::is_surface() && self::is_campaign_id_set() ) {
					$classes[] = 'aryel-ar-3d-product-viewer-product-miniature';
				}

				return $classes;
			},
			10,
			2
		);
	}

	/**
	 * Render the component.
	 *
	 * @return void
	 */
	public static function render() {
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/components/product-miniature.php';
	}

	/**
	 * Render the embed viewer.
	 *
	 * @return void
	 */
	public static function render_embed_viewer() {
		global $post;

		EmbedViewer::render( 'product_miniature', $post );
	}
}
