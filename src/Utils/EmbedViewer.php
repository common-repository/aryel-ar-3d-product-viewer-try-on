<?php
/**
 * Embed viewer util.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Utils;

use AryelAR3DProductViewer;

/**
 * Embed viewer util class.
 */
class EmbedViewer extends AbstractUtil {

	/**
	 * Enqueued flag.
	 *
	 * @var bool
	 */
	protected static $enqueued = false;

	/**
	 * Enqueue the assets.
	 *
	 * @return void
	 */
	public static function enqueue_assets() {
		// We need to eneuque the scripts only once.
		if ( self::$enqueued ) {
			return;
		}

		$api_key = get_option( AryelAR3DProductViewer::API_KEY );
		wp_register_script( 'aryel-lib-script', "https://assets.aryel.io/embed-viewer/aryel-loader.js?apiKey={$api_key}&v=2", array(), '1.0.1', array( 'in_footer' => false ) );
		wp_enqueue_script( 'aryel-lib-script' );

		add_filter(
			'script_loader_tag',
			function ( $tag, $handle ) {
				if ( 'aryel-lib-script' !== $handle ) {
					return $tag;
				}

				return str_replace( ' src', ' aryel-embed-loader src', $tag );
			},
			10,
			2
		);
	}

	/**
	 * Render the viewer.
	 *
	 * @param string  $type The viewer type.
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public static function render( $type, $post ) {
		$viewer = self::prepare_viewer_attributes( $type, $post->ID );
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/embed-viewer.php';
	}
}
