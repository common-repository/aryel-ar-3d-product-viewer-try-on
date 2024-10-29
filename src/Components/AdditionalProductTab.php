<?php
/**
 * Additional product tab component.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Components;

use Aryel\AryelAR3DProductViewer\Utils\EmbedViewer;

/**
 * Additional product tab component.
 */
class AdditionalProductTab extends AbstractComponent {

	/**
	 * Initialize the component.
	 *
	 * @return void
	 */
	public static function initialize() {
		global $post;

		$is_additional_tab_enabled = (bool) get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_additional_product_tab_toggle', true );
		if ( $is_additional_tab_enabled && self::is_surface() && self::is_campaign_id_set() ) {
			add_filter(
				'woocommerce_product_tabs',
				function ( $tabs ) use ( $post ) {
					$tab_title = get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_additional_product_tab_content_title', true );

					$tabs['aryel_ar_3d_product_viewer'] = array(
						'title'    => $tab_title,
						'callback' => array( self::class, 'render' ),
					);

					return $tabs;
				},
				20,
				1
			);
		}
	}

	/**
	 * Render the component.
	 *
	 * @return void
	 */
	public static function render() {
		global $post;

		$tab_content      = get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_additional_product_tab_content_content', true );
		$container_height = get_post_meta( $post->ID, '_aryel_ar_3d_product_viewer_additional_product_tab_container_height', true );

		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/components/additional-product-tab.php';
	}

	/**
	 * Render the embed viewer.
	 *
	 * @return void
	 */
	public static function render_embed_viewer() {
		global $post;

		EmbedViewer::render( 'additional_product_tab', $post );
	}
}
