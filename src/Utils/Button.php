<?php
/**
 * Button util.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Utils;

/**
 * Button util class.
 */
class Button extends AbstractUtil {

	/**
	 * Enqueued flag.
	 *
	 * @var bool
	 */
	protected static $enqueued = false;

	/**
	 * Render the button.
	 *
	 * @param string  $type The button type.
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public static function render( $type, $post ) {
		$button = self::prepare_button_attributes( $type, $post->ID );

		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/button.php';
	}
}
