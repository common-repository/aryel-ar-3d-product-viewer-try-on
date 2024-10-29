<?php
/**
 * Abstract component class.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Components;

use Aryel\AryelAR3DProductViewer\Utils\EmbedViewer;
use AryelAR3DProductViewer;

/**
 * Abstract component class.
 */
abstract class AbstractComponent {

	/**
	 * Initialize the component.
	 *
	 * @return void
	 */
	public static function init() {
		if ( is_admin() || ! self::is_api_key_set() ) {
			return;
		}

		self::init_hooks();
	}

	/**
	 * Initialize the component hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'wp', array( static::class, 'initialize' ) );
		add_action( 'wp_enqueue_scripts', array( EmbedViewer::class, 'enqueue_assets' ) );
	}

	/**
	 * Check if the campaign ID is set.
	 *
	 * @return bool
	 */
	public static function is_campaign_id_set() {
		global $post;
		return ! empty( get_post_meta( $post->ID, AryelAR3DProductViewer::CAMPAIGN_ID_KEY, true ) );
	}

	/**
	 * Check if the campaign type is surface.
	 *
	 * @return bool
	 */
	public static function is_surface() {
		global $post;

		return get_post_meta( $post->ID, AryelAR3DProductViewer::CAMPAIGN_TYPE_KEY, true ) === 'surface';
	}

	/**
	 * Check if the API key is set.
	 *
	 * @return bool
	 */
	protected static function is_api_key_set() {
		$api_key = get_option( AryelAR3DProductViewer::API_KEY, '' );
		return ! empty( $api_key );
	}
}
