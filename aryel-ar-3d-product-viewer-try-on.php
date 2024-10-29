<?php
/**
 * Plugin Name: Aryel AR/3D Product Viewer & Try-On
 * Description: Simple integration of Aryel into WooCommerce
 * Version: 1.0.1
 * Author: Aryel S.r.l.
 * Author URI: https://aryel.io
 * Text Domain: aryel
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package AryelAR3DProductViewer
 */

use Aryel\AryelAR3DProductViewer\AdminSettings;
use Aryel\AryelAR3DProductViewer\Components\AdditionalProductTab;
use Aryel\AryelAR3DProductViewer\Components\ProductGallery;
use Aryel\AryelAR3DProductViewer\Components\ProductMiniature;
use Aryel\AryelAR3DProductViewer\Components\ViewInARButton;
use Aryel\AryelAR3DProductViewer\Metaboxes\Aryel3DMetabox;

defined( 'ABSPATH' ) || exit;

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

if ( ! defined( 'ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR' ) ) {
	define( 'ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR', __DIR__ );
}

if ( ! defined( 'ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE' ) ) {
	define( 'ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE', __FILE__ );
}

if ( ! class_exists( 'AryelAR3DProductViewer' ) ) {

	/**
	 * Aryel AR/3D Product Viewer & Try-On main class.
	 */
	class AryelAR3DProductViewer {

		public const API_KEY           = 'aryel_ar_3d_product_viewer_api_key';
		public const CAMPAIGN_ID_KEY   = '_aryel_ar_3d_product_viewer_campaign_id';
		public const CAMPAIGN_TYPE_KEY = '_aryel_ar_3d_product_viewer_campaign_type';
		public const CAMPAIGN_URL_KEY  = '_aryel_ar_3d_product_viewer_campaign_url';

		/**
		 * Initialize the plugin.
		 *
		 * @return void
		 */
		public static function init() {
			AdminSettings::init();
			Aryel3DMetabox::init();

			// Init components.
			ProductGallery::init();
			AdditionalProductTab::init();
			ProductMiniature::init();
			ViewInARButton::init();

			self::init_hooks();
		}

		/**
		 * Initialize the plugin hooks.
		 *
		 * @return void
		 */
		public static function init_hooks() {
			add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_assets' ) );
			add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_admin_assets' ) );

			add_filter( 'upload_mimes', array( self::class, 'add_additional_mine_types' ) );
		}

		/**
		 * Enqueue the plugin assets.
		 *
		 * @return void
		 */
		public static function enqueue_assets() {
			wp_register_style( 'aryel-front-style', plugins_url( '/dist/css/front.css', __FILE__ ), array(), '1.0.1' );
			wp_register_script( 'aryel-front-script', plugins_url( '/dist/js/front.js', __FILE__ ), array(), '1.0.1', array( 'in_footer' => false ) );

			wp_enqueue_style( 'aryel-front-style' );
			wp_enqueue_script( 'aryel-front-script' );
		}

		/**
		 * Enqueue the plugin admin assets.
		 *
		 * @return void
		 */
		public static function enqueue_admin_assets() {
			$api_key = get_option( self::API_KEY, '' );

			wp_register_style( 'aryel-front-style', plugins_url( '/dist/css/front.css', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ), array(), '1.0.1' );
			wp_register_style( 'aryel-admin-style', plugins_url( '/dist/css/admin.css', __FILE__ ), array( 'wp-color-picker' ), '1.0.1' );
			wp_register_script( 'aryel-admin-script', plugins_url( '/dist/js/admin.js', __FILE__ ), array( 'wp-color-picker' ), '1.0.1', array( 'in_footer' => false ) );

			if ( ! empty( $api_key ) ) {
				wp_register_script( 'aryel-lib-script', "https://assets.aryel.io/embed-viewer/aryel-loader.js?apiKey={$api_key}&v=2", array(), '1.0.1', array( 'in_footer' => false ) );

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

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'aryel-front-style' );
			wp_enqueue_style( 'aryel-admin-style' );
			wp_enqueue_script( 'aryel-admin-script' );

			if ( ! empty( $api_key ) ) {
				wp_enqueue_script( 'aryel-lib-script' );
			}

			wp_enqueue_media();
		}

		/**
		 * Add additional mime types.
		 *
		 * @param array $mimes Mime types.
		 *
		 * @return array
		 */
		public static function add_additional_mine_types( $mimes ) {
			$mimes['hdr'] = 'application/octet-stream';

			return $mimes;
		}
	}

	AryelAR3DProductViewer::init();
}
