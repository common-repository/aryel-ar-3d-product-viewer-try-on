<?php
/**
 * Admin settings.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer;

use AryelAR3DProductViewer;

/**
 * Admin settings class.
 */
class AdminSettings {

	/**
	 * Init the settings page.
	 *
	 * @return void
	 */
	public static function init() {
		self::init_hooks();
	}

	/**
	 * Init the hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'admin_menu', array( self::class, 'register_menu' ) );
		add_action( 'admin_init', array( self::class, 'register_settings' ) );
	}

	/**
	 * Register the menu.
	 *
	 * @return void
	 */
	public static function register_menu() {
		add_menu_page(
			__( 'Aryel settings', 'aryel' ),
			__( 'Aryel', 'aryel' ),
			'manage_options',
			'aryel-ar-3d-product-viewer',
			array( self::class, 'output_settings_page' ),
			plugins_url( '/resources/images/admin/aryel_icon.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
		);
	}

	/**
	 * Output the settings page.
	 *
	 * @return void
	 */
	public static function output_settings_page() {
		$api_key_field_name  = AryelAR3DProductViewer::API_KEY;
		$api_key_field_value = get_option( AryelAR3DProductViewer::API_KEY );

		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/settings.php';
	}

	/**
	 * Register the settings.
	 *
	 * @return void
	 */
	public static function register_settings() {
		register_setting( 'aryel-ar-3d-product-viewer', AryelAR3DProductViewer::API_KEY );
	}
}
