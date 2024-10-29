<?php
/**
 * Product gallery component.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Components;

use Aryel\AryelAR3DProductViewer\Utils\EmbedViewer;

/**
 * Product gallery component.
 */
class ProductGallery extends AbstractComponent {

	/**
	 * Initialize the component.
	 *
	 * @return void
	 */
	public static function initialize() {
		global $post;

		$is_product_gallery_enabled = (bool) get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_product_gallery_toggle', true );
		if ( $is_product_gallery_enabled && self::is_surface() && self::is_campaign_id_set() ) {
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			add_action(
				'woocommerce_before_single_product_summary',
				function () use ( $post ) {
					self::render();
				},
				20
			);
		}
	}

	/**
	 * Render the component.
	 *
	 * @return void
	 */
	public static function render() {
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/components/product-gallery.php';
	}

	/**
	 * Render the embed viewer.
	 *
	 * @return void
	 */
	public static function render_embed_viewer() {
		global $post;

		EmbedViewer::render( 'product_gallery', $post );
	}
}
