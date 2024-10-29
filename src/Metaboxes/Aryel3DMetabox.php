<?php
/**
 * Aryel's AR/3D Product Viewer & Try-On metabox.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Metaboxes;

use AryelAR3DProductViewer;

/**
 * Aryel 3D metabox.
 */
class Aryel3DMetabox {

	/**
	 * Init the metabox.
	 *
	 * @return void
	 */
	public static function init() {
		self::init_hooks();
	}

	/**
	 * Initialize the metabox hooks.
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_action( 'add_meta_boxes', array( self::class, 'add' ), 30 );
		add_action( 'save_post', array( self::class, 'save' ), 10, 2 );

		add_action( 'wp_ajax_aryel_ar_3d_product_viewer_preview', array( self::class, 'preview' ) );
		add_action( 'wp_ajax_aryel_ar_3d_product_viewer_validate_campaign_id', array( self::class, 'validate_campaign_id' ) );
	}

	/**
	 * Add the metabox.
	 *
	 * @return void
	 */
	public static function add() {
		add_meta_box( 'aryel-ar-3d-product-viewer', __( 'Aryel\'s AR/3D Product Viewer & Try-On', 'aryel' ), array( self::class, 'output' ), 'product', 'normal', 'default' );
	}

	/**
	 * Output the metabox.
	 *
	 * @param WP_Post $post Post object.
	 */
	public static function output( $post ) {
		$api_key       = get_option( AryelAR3DProductViewer::API_KEY, '' );
		$campaign_id   = get_post_meta( $post->ID, AryelAR3DProductViewer::CAMPAIGN_ID_KEY, true );
		$campaign_type = get_post_meta( $post->ID, AryelAR3DProductViewer::CAMPAIGN_TYPE_KEY, true );
		$tabs          = self::get_tabs( $post->ID );
		$nonce_metabox = wp_create_nonce( 'aa3dpv_metabox' );
		$nonce_ajax    = wp_create_nonce( 'aa3dpv_ajax' );

		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/aryel-3d-metabox.php';
	}

	/**
	 * Save the metabox fields.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public static function save( $post_id, $post ) {
		$nonce = sanitize_text_field( wp_unslash( $_POST['_aryel_ar_3d_product_viewer_nonce'] ?? '' ) );

		if ( ! wp_verify_nonce( $nonce, 'aa3dpv_metabox' ) ) {
			die( 'Wrong request' );
		}

		if ( 'product' !== $post->post_type ) {
			return;
		}

		// Save the metakey.
		if ( isset( $_POST[ AryelAR3DProductViewer::CAMPAIGN_ID_KEY ] ) ) {
			$api_key     = get_option( AryelAR3DProductViewer::API_KEY );
			$campaign_id = sanitize_text_field( wp_unslash( $_POST[ AryelAR3DProductViewer::CAMPAIGN_ID_KEY ] ?? '' ) );
			update_post_meta( $post_id, AryelAR3DProductViewer::CAMPAIGN_ID_KEY, $campaign_id );

			$campaign_type = sanitize_text_field( wp_unslash( $_POST[ AryelAR3DProductViewer::CAMPAIGN_TYPE_KEY ] ?? '' ) );
			update_post_meta( $post_id, AryelAR3DProductViewer::CAMPAIGN_TYPE_KEY, $campaign_type );

			// Get campaign url.
			$campaign_url = self::get_campaign_url( $campaign_id, $api_key );
			update_post_meta( $post_id, AryelAR3DProductViewer::CAMPAIGN_URL_KEY, sanitize_text_field( $campaign_url ) );
		}

		$tabs = self::get_tabs( $post_id );
		foreach ( $tabs as &$tab_data ) {
			if ( isset( $tab_data['toggle'] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST[ $tab_data['toggle']['name'] ] ?? $tab_data['toggle']['default'] ) );
				update_post_meta( $post_id, $tab_data['toggle']['name'], $value );
			}

			foreach ( $tab_data['sections'] as &$section ) {
				foreach ( $section['fields'] as &$field ) {
					if ( 'preview' === $field['type'] ) {
						continue;
					}

					$value = sanitize_text_field( wp_unslash( $_POST[ $field['name'] ] ?? $field['default'] ) );

					if ( 'checkbox' === $field['type'] ) {
						$value = isset( $_POST[ $field['name'] ] );
					} elseif ( 'wysiwyg' === $field['type'] ) {
						$value = wp_kses_post( wp_unslash( $_POST[ $field['name'] ] ?? $field['default'] ) );
					}

					update_post_meta( $post_id, $field['name'], $value );
				}
			}
		}
	}

	/**
	 * Render the component preview
	 *
	 * @return void
	 */
	public static function preview() {
		$api_key = get_option( AryelAR3DProductViewer::API_KEY, '' );
		$type    = sanitize_text_field( wp_unslash( $_GET['type'] ?? '' ) );
		$nonce   = sanitize_text_field( wp_unslash( $_GET['_nonce'] ?? '' ) );

		if ( ! wp_verify_nonce( $nonce, "aa3dpv_preview_{$type}" ) ) {
			die( 'Wrong request' );
		}

		if ( ! $api_key || ! $type ) {
			die( 'Wrong request' );
		}

		$show_embed_viewer = false;
		$show_view_in_ar   = false;
		$viewer            = array();
		$button            = array();
		switch ( $type ) {
			case 'product_gallery':
			case 'product_miniature':
				$show_embed_viewer = true;
				$viewer            = self::prepare_viewer_attributes( $type );
				break;

			case 'additional_product_tab':
				$show_embed_viewer = true;
				$viewer            = self::prepare_viewer_attributes( $type );
				break;

			case 'view_in_ar':
				$show_view_in_ar = true;
				$button          = self::prepare_button_attributes( $type );
				break;

			case 'general_settings':
				$show_embed_viewer = true;
				$viewer            = self::prepare_viewer_attributes( $type );
				break;
		}

		$viewer = self::prepare_viewer_attributes( $type );

		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/preview.php';
		die();
	}

	/**
	 * Validate the capaign id.
	 *
	 * @return void
	 */
	public static function validate_campaign_id() {
		$api_key     = get_option( AryelAR3DProductViewer::API_KEY, '' );
		$nonce       = sanitize_text_field( wp_unslash( $_GET['_nonce'] ?? '' ) );
		$campaign_id = sanitize_text_field( wp_unslash( $_GET['campaign_id'] ?? '' ) );
		$post_id     = sanitize_text_field( wp_unslash( $_GET['post_id'] ?? '' ) );

		if ( ! wp_verify_nonce( $nonce, 'aa3dpv_ajax' ) ) {
			die( 'Wrong request' );
		}

		if ( ! $api_key ) {
			die( 'Wrong request' );
		}

		$campaign_data = self::get_campaign_data( $campaign_id, $api_key );
		if ( ! $campaign_data ) {
			$result = array(
				'valid'   => false,
				'message' => __( 'The Campaign ID or the API Key is not valid.', 'aryel' ),
			);

			wp_send_json( $result );
			die();
		}

		// Let's check that no other product is using this campaign id.
		// phpcs:disable WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		$posts = get_posts(
			array(
				'post_type'      => 'product',
				'meta_query'     => array(
					array(
						'key'   => AryelAR3DProductViewer::CAMPAIGN_ID_KEY,
						'value' => $campaign_id,
					),
				),
				'post__not_in'   => array( $post_id ), // Exclude the current product if we are editing it.
				'posts_per_page' => 1,
				'cache_results'  => false,
			)
		);

		if ( $posts ) {
			$result = array(
				'valid'   => false,
				'message' => __( 'This Campaign ID is already used by another product.', 'aryel' ),
			);

			wp_send_json( $result );
			die();
		}

		$result = array(
			'valid'   => true,
			'trigger' => $campaign_data['trigger'],
		);

		wp_send_json( $result );
		die();
	}

	/**
	 * Prepare the tab for the given post_id.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	protected static function get_tabs( $post_id ) {
		$prefix = 'aryel_ar_3d_product_viewer_';

		$tabs = self::get_tabs_definition();
		foreach ( $tabs as &$tab_data ) {
			if ( isset( $tab_data['toggle'] ) ) {
				$tab_data['toggle']['value'] = metadata_exists( 'post', $post_id, $tab_data['toggle']['name'] )
					? get_post_meta( $post_id, $tab_data['toggle']['name'], true )
					: $tab_data['toggle']['default'];
			}
			foreach ( $tab_data['sections'] as &$section ) {
				foreach ( $section['fields'] as &$field ) {
					if ( 'preview' === $field['type'] ) {
						continue;
					}

					$field['value'] = metadata_exists( 'post', $post_id, $field['name'] )
						? get_post_meta( $post_id, $field['name'], true )
						: $field['default'];
				}
			}
		}

		return $tabs;
	}

	/**
	 * Retuns the tab definition.
	 *
	 * @return array
	 */
	protected static function get_tabs_definition() {
		$general_settings_tab = array(
			'show_for_surface' => true,
			'name'             => 'general_settings',
			'label'            => __( 'General settings', 'aryel' ),
			'description'      => __( 'Define the general settings for the 3D product viewer. They will be applied to any enabled block, where applicable.', 'aryel' ),
			'icon'             => plugins_url( '/resources/images/metabox/general_settings.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'icon_inactive'    => plugins_url( '/resources/images/metabox/inactive_general_settings.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'sections'         => array(
				array(
					'title'  => __( 'Loader', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_general_settings_loader_color',
							'type'    => 'color-picker',
							'label'   => __( 'Color', 'aryel' ),
							'default' => '#FFFFFF',
						),
					),
				),
				array(
					'title'  => __( 'Scale/position/rotation', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_spr_scale',
							'type'       => 'vector3',
							'label'      => __( 'Scale (x, y, z)', 'aryel' ),
							'default'    => '1 1 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_spr_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_spr_rotation',
							'type'       => 'vector3',
							'label'      => __( 'Rotation (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'title'  => __( 'Camera', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_camera_fov',
							'type'       => 'number',
							'label'      => __( 'Fov', 'aryel' ),
							'default'    => 50,
							'attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_camera_near',
							'type'       => 'number',
							'label'      => __( 'Near', 'aryel' ),
							'default'    => 0.1,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_camera_far',
							'type'       => 'number',
							'label'      => __( 'Far', 'aryel' ),
							'default'    => 1000,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_general_settings_camera_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'title'  => __( 'Environment', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_general_settings_environment_image_id',
							'type'    => 'media',
							'label'   => __( 'Environment', 'aryel' ),
							'default' => null,
						),
					),
				),
				array(
					'title'  => __( 'Extras', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_general_settings_advanced_gesture',
							'type'    => 'checkbox',
							'label'   => __( 'Gestures', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_general_settings_advanced_autorotate',
							'type'    => 'checkbox',
							'label'   => __( 'Autorotate', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_general_settings_advanced_output_encoding',
							'type'    => 'select',
							'label'   => __( 'Output encoding', 'aryel' ),
							'default' => 'linear',
							'options' => array(
								'linear' => __( 'Linear', 'aryel' ),
								'sRGB'   => __( 'sRGB', 'aryel' ),
							),
						),
					),
				),
				array(
					'title'  => __( 'Preview', 'aryel' ),
					'fields' => array(
						array(
							'type'            => 'preview',
							'params'          => array(
								'action' => 'aryel_ar_3d_product_viewer_preview',
								'type'   => 'general_settings',
								'_nonce' => wp_create_nonce( 'aa3dpv_preview_general_settings' ),
							),
							'fields_to_watch' => array(
								AryelAR3DProductViewer::CAMPAIGN_ID_KEY,
							),
							'tab_name'        => 'general_settings',
							'height'          => 400, // Height in px.
						),
					),
				),
			),
		);

		$product_gallery_tab = array(
			'show_for_surface' => true,
			'name'             => 'product_gallery',
			'label'            => __( 'Product gallery', 'aryel' ),
			'description'      => __( 'Replace the product gallery with the 3D embed viewer.', 'aryel' ),
			'icon'             => plugins_url( '/resources/images/metabox/gallery.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'icon_inactive'    => plugins_url( '/resources/images/metabox/inactive_gallery.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'toggle'           => array(
				'name'    => '_aryel_ar_3d_product_viewer_product_gallery_toggle',
				'label'   => __( 'Enabled', 'aryel' ),
				'default' => false,
			),
			'sections'         => array(
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_gallery_loader_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize loader settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_gallery_loader_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_gallery_loader_section',
					'title'  => __( 'Loader', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_loader_color',
							'type'    => 'color-picker',
							'label'   => __( 'Color', 'aryel' ),
							'default' => '#FFFFFF',
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_gallery_spr_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize scale/position/rotation', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_gallery_spr_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_gallery_spr_section',
					'title'  => __( 'Scale/position/rotation', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_spr_scale',
							'type'       => 'vector3',
							'label'      => __( 'Scale (x, y, z)', 'aryel' ),
							'default'    => '1 1 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_spr_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_spr_rotation',
							'type'       => 'vector3',
							'label'      => __( 'Rotation (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_gallery_camera_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize camera settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_gallery_camera_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_gallery_camera_section',
					'title'  => __( 'Camera', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_camera_fov',
							'type'       => 'number',
							'label'      => __( 'Fov', 'aryel' ),
							'default'    => 50,
							'attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_camera_near',
							'type'       => 'number',
							'label'      => __( 'Near', 'aryel' ),
							'default'    => 0.1,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_camera_far',
							'type'       => 'number',
							'label'      => __( 'Far', 'aryel' ),
							'default'    => 1000,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_gallery_camera_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_gallery_environment_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize environment settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_gallery_environment_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_gallery_environment_section',
					'title'  => __( 'Environment', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_environment_image_id',
							'type'    => 'media',
							'label'   => __( 'Environment', 'aryel' ),
							'default' => null,
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_gallery_advanced_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize extras settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_gallery_advanced_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_gallery_advanced_section',
					'title'  => __( 'Extras', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_advanced_gesture',
							'type'    => 'checkbox',
							'label'   => __( 'Gestures', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_advanced_autorotate',
							'type'    => 'checkbox',
							'label'   => __( 'Autorotate', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_advanced_output_encoding',
							'type'    => 'select',
							'label'   => __( 'Output encoding', 'aryel' ),
							'default' => 'linear',
							'options' => array(
								'linear' => __( 'Linear', 'aryel' ),
								'sRGB'   => __( 'sRGB', 'aryel' ),
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_gallery_button_enabled',
							'type'             => 'toggle',
							'label'            => __( 'Show view in AR button', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_gallery_button',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_gallery_button',
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_button_label',
							'type'    => 'text',
							'label'   => __( 'Label', 'aryel' ),
							'default' => __( 'View in AR', 'aryel' ),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_button_text_color',
							'type'    => 'color-picker',
							'label'   => __( 'Text color', 'aryel' ),
							'default' => '#FFFFFF',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_button_background_color',
							'type'    => 'color-picker',
							'label'   => __( 'Background color', 'aryel' ),
							'default' => '#000000',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_button_text_alignment',
							'type'    => 'select',
							'label'   => __( 'Text alignment', 'aryel' ),
							'default' => 'left',
							'options' => array(
								'left'   => __( 'Left', 'aryel' ),
								'center' => __( 'Center', 'aryel' ),
								'right'  => __( 'Right', 'aryel' ),
							),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_gallery_button_css_class',
							'type'    => 'text',
							'label'   => __( 'Custom CSS class', 'aryel' ),
							'default' => '',
						),
					),
				),
				array(
					'title'  => __( 'Preview', 'aryel' ),
					'fields' => array(
						array(
							'type'            => 'preview',
							'params'          => array(
								'action' => 'aryel_ar_3d_product_viewer_preview',
								'type'   => 'product_gallery',
								'_nonce' => wp_create_nonce( 'aa3dpv_preview_product_gallery' ),
							),
							'fields_to_watch' => array(
								AryelAR3DProductViewer::CAMPAIGN_ID_KEY,
								'_aryel_ar_3d_product_viewer_general_settings_loader_color',
								'_aryel_ar_3d_product_viewer_general_settings_spr_scale',
								'_aryel_ar_3d_product_viewer_general_settings_spr_position',
								'_aryel_ar_3d_product_viewer_general_settings_spr_rotation',
								'_aryel_ar_3d_product_viewer_general_settings_camera_fov',
								'_aryel_ar_3d_product_viewer_general_settings_camera_near',
								'_aryel_ar_3d_product_viewer_general_settings_camera_far',
								'_aryel_ar_3d_product_viewer_general_settings_camera_position',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_gesture',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_autorotate',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_output_encoding',
								'_aryel_ar_3d_product_viewer_general_settings_environment_image_id',
							),
							'toggle'          => '_aryel_ar_3d_product_viewer_product_gallery_toggle',
							'tab_name'        => 'product_gallery',
							'height'          => 400, // Height in px.
						),
					),
				),
			),
		);

		$additional_product_tab_tab = array(
			'show_for_surface' => true,
			'name'             => 'additional_product_tab',
			'label'            => __( 'Additional tab', 'aryel' ),
			'description'      => __( 'Include Aryel 3D embed viewer in a new tab of the page.', 'aryel' ),
			'icon_inactive'    => plugins_url( '/resources/images/metabox/additional_tab.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'icon'             => plugins_url( '/resources/images/metabox/inactive_additional_tab.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'toggle'           => array(
				'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_toggle',
				'label'   => __( 'Enabled', 'aryel' ),
				'default' => false,
			),
			'sections'         => array(
				array(
					'title'  => __( 'Tab', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_content_title',
							'type'    => 'text',
							'label'   => __( 'Title', 'aryel' ),
							'default' => '',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_content_content',
							'type'    => 'wysiwyg',
							'label'   => __( 'Content', 'aryel' ),
							'default' => '',
						),
					),
				),
				array(
					'title'  => __( 'Dimensions', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_container_height',
							'type'       => 'number',
							'label'      => __( 'Height', 'aryel' ),
							'default'    => 100,
							'attributes' => array(
								'min' => 100,
								'max' => 1500,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_additional_product_tab_loader_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize loader settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_additional_product_tab_loader_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_additional_product_tab_loader_section',
					'title'  => __( 'Loader', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_loader_color',
							'type'    => 'color-picker',
							'label'   => __( 'Color', 'aryel' ),
							'default' => '#FFFFFF',
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_additional_product_tab_spr_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize scale/position/rotation', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_additional_product_tab_spr_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_additional_product_tab_spr_section',
					'title'  => __( 'Scale/position/rotation', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_spr_scale',
							'type'       => 'vector3',
							'label'      => __( 'Scale (x, y, z)', 'aryel' ),
							'default'    => '1 1 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_spr_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_spr_rotation',
							'type'       => 'vector3',
							'label'      => __( 'Rotation (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_additional_product_tab_camera_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize camera settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_additional_product_tab_camera_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_additional_product_tab_camera_section',
					'title'  => __( 'Camera', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_camera_fov',
							'type'       => 'number',
							'label'      => __( 'Fov', 'aryel' ),
							'default'    => 50,
							'attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_camera_near',
							'type'       => 'number',
							'label'      => __( 'Near', 'aryel' ),
							'default'    => 0.1,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_camera_far',
							'type'       => 'number',
							'label'      => __( 'Far', 'aryel' ),
							'default'    => 1000,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_additional_product_tab_camera_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_additional_product_tab_environment_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize environment settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_additional_product_tab_environment_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_additional_product_tab_environment_section',
					'title'  => __( 'Environment', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_environment_image_id',
							'type'    => 'media',
							'label'   => __( 'Environment', 'aryel' ),
							'default' => null,
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_additional_product_tab_advanced_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize extras settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_additional_product_tab_advanced_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_additional_product_tab_advanced_section',
					'title'  => __( 'Extras', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_advanced_gesture',
							'type'    => 'checkbox',
							'label'   => __( 'Gestures', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_advanced_autorotate',
							'type'    => 'checkbox',
							'label'   => __( 'Autorotate', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_advanced_output_encoding',
							'type'    => 'select',
							'label'   => __( 'Output encoding', 'aryel' ),
							'default' => 'linear',
							'options' => array(
								'linear' => __( 'Linear', 'aryel' ),
								'sRGB'   => __( 'sRGB', 'aryel' ),
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_additional_product_tab_button_enabled',
							'type'             => 'toggle',
							'label'            => __( 'Show view in AR button', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_additional_product_tab_button',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_additional_product_tab_button',
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_button_label',
							'type'    => 'text',
							'label'   => __( 'Label', 'aryel' ),
							'default' => __( 'View in AR', 'aryel' ),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_button_text_color',
							'type'    => 'color-picker',
							'label'   => __( 'Text color', 'aryel' ),
							'default' => '#FFFFFF',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_button_background_color',
							'type'    => 'color-picker',
							'label'   => __( 'Background color', 'aryel' ),
							'default' => '#000000',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_button_text_alignment',
							'type'    => 'select',
							'label'   => __( 'Text alignment', 'aryel' ),
							'default' => 'left',
							'options' => array(
								'left'   => __( 'Left', 'aryel' ),
								'center' => __( 'Center', 'aryel' ),
								'right'  => __( 'Right', 'aryel' ),
							),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_additional_product_tab_button_css_class',
							'type'    => 'text',
							'label'   => __( 'Custom CSS class', 'aryel' ),
							'default' => '',
						),

					),
				),
				array(
					'title'  => __( 'Preview', 'aryel' ),
					'fields' => array(
						array(
							'type'            => 'preview',
							'params'          => array(
								'action' => 'aryel_ar_3d_product_viewer_preview',
								'type'   => 'additional_product_tab',
								'_nonce' => wp_create_nonce( 'aa3dpv_preview_additional_product_tab' ),
							),
							'fields_to_watch' => array(
								AryelAR3DProductViewer::CAMPAIGN_ID_KEY,
								'_aryel_ar_3d_product_viewer_general_settings_loader_color',
								'_aryel_ar_3d_product_viewer_general_settings_spr_scale',
								'_aryel_ar_3d_product_viewer_general_settings_spr_position',
								'_aryel_ar_3d_product_viewer_general_settings_spr_rotation',
								'_aryel_ar_3d_product_viewer_general_settings_camera_fov',
								'_aryel_ar_3d_product_viewer_general_settings_camera_near',
								'_aryel_ar_3d_product_viewer_general_settings_camera_far',
								'_aryel_ar_3d_product_viewer_general_settings_camera_position',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_gesture',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_autorotate',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_output_encoding',
								'_aryel_ar_3d_product_viewer_general_settings_environment_image_id',
							),
							'toggle'          => '_aryel_ar_3d_product_viewer_additional_product_tab_toggle',
							'tab_name'        => 'additional_product_tab',
							'height'          => 400, // Height in px.
						),
					),
				),
			),
		);

		$view_in_ar_tab = array(
			'name'          => 'view_in_ar',
			'label'         => __( 'View in AR button', 'aryel' ),
			'description'   => __( 'Add a button in the product page to launch the AR experience.', 'aryel' ),
			'icon_inactive' => plugins_url( '/resources/images/metabox/view.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'icon'          => plugins_url( '/resources/images/metabox/inactive_view.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'toggle'        => array(
				'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_toggle',
				'label'   => __( 'Enabled', 'aryel' ),
				'default' => false,
			),
			'sections'      => array(
				array(
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_button_hook',
							'type'    => 'select',
							'label'   => __( 'Hook', 'aryel' ),
							'default' => 'woocommerce_product_meta_end',
							'options' => array(
								'woocommerce_product_meta_end' => __( 'woocommerce_product_meta_end', 'aryel' ),
								'woocommerce_short_description' => __( 'woocommerce_short_description', 'aryel' ),
							),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_button_label',
							'type'    => 'text',
							'label'   => __( 'Label', 'aryel' ),
							'default' => __( 'View in AR', 'aryel' ),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_button_text_color',
							'type'    => 'color-picker',
							'label'   => __( 'Text color', 'aryel' ),
							'default' => '#FFFFFF',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_button_background_color',
							'type'    => 'color-picker',
							'label'   => __( 'Background color', 'aryel' ),
							'default' => '#000000',
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_button_text_alignment',
							'type'    => 'select',
							'label'   => __( 'Text alignment', 'aryel' ),
							'default' => 'left',
							'options' => array(
								'left'   => __( 'Left', 'aryel' ),
								'center' => __( 'Center', 'aryel' ),
								'right'  => __( 'Right', 'aryel' ),
							),
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_view_in_ar_button_css_class',
							'type'    => 'text',
							'label'   => __( 'Custom CSS class', 'aryel' ),
							'default' => '',
						),

					),
				),
				array(
					'title'  => __( 'Preview', 'aryel' ),
					'fields' => array(
						array(
							'type'            => 'preview',
							'params'          => array(
								'action' => 'aryel_ar_3d_product_viewer_preview',
								'type'   => 'view_in_ar',
								'_nonce' => wp_create_nonce( 'aa3dpv_preview_view_in_ar' ),
							),
							'fields_to_watch' => array(
								AryelAR3DProductViewer::CAMPAIGN_ID_KEY,
							),
							'toggle'          => '_aryel_ar_3d_product_viewer_view_in_ar_toggle',
							'tab_name'        => 'view_in_ar',
							'height'          => 50, // Height in px.
						),
					),
				),
			),
		);

		$product_miniature_tab = array(
			'show_for_surface' => true,
			'name'             => 'product_miniature',
			'label'            => __( 'Product thumbnail', 'aryel' ),
			'description'      => __( 'Use the 3D viewer instead of the product thumbnail.', 'aryel' ),
			'icon_inactive'    => plugins_url( '/resources/images/metabox/miniature.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'icon'             => plugins_url( '/resources/images/metabox/inactive_miniature.svg', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ),
			'toggle'           => array(
				'name'    => '_aryel_ar_3d_product_viewer_product_miniature_toggle',
				'label'   => __( 'Enabled', 'aryel' ),
				'default' => false,
			),
			'sections'         => array(
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_miniature_loader_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize loader settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_miniature_loader_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_miniature_loader_section',
					'title'  => __( 'Loader', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_miniature_loader_color',
							'type'    => 'color-picker',
							'label'   => __( 'Color', 'aryel' ),
							'default' => '#FFFFFF',
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_miniature_spr_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize scale/position/rotation', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_miniature_spr_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_miniature_spr_section',
					'title'  => __( 'Scale/position/rotation', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_spr_scale',
							'type'       => 'vector3',
							'label'      => __( 'Scale (x, y, z)', 'aryel' ),
							'default'    => '1 1 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_spr_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_spr_rotation',
							'type'       => 'vector3',
							'label'      => __( 'Rotation (x, y, z)', 'aryel' ),
							'default'    => '0 0 0',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_miniature_camera_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize camera settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_miniature_camera_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_miniature_camera_section',
					'title'  => __( 'Camera', 'aryel' ),
					'fields' => array(
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_camera_fov',
							'type'       => 'number',
							'label'      => __( 'Fov', 'aryel' ),
							'default'    => 50,
							'attributes' => array(
								'min'  => 0,
								'step' => 1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_camera_near',
							'type'       => 'number',
							'label'      => __( 'Near', 'aryel' ),
							'default'    => 0.1,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_camera_far',
							'type'       => 'number',
							'label'      => __( 'Far', 'aryel' ),
							'default'    => 1000,
							'attributes' => array(
								'min'  => 0,
								'step' => 0.1,
							),
						),
						array(
							'name'       => '_aryel_ar_3d_product_viewer_product_miniature_camera_position',
							'type'       => 'vector3',
							'label'      => __( 'Position (x, y, z)', 'aryel' ),
							'default'    => '0 0 1',
							'attributes' => array(
								'step' => 0.01,
							),
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_miniature_environment_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize environment settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_miniature_environment_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_miniature_environment_section',
					'title'  => __( 'Environment', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_miniature_environment_image_id',
							'type'    => 'media',
							'label'   => __( 'Environment', 'aryel' ),
							'default' => null,
						),
					),
				),
				array(
					'fields' => array(
						array(
							'name'             => '_aryel_ar_3d_product_viewer_product_miniature_advanced_toggle',
							'type'             => 'toggle',
							'label'            => __( 'Customize extras settings', 'aryel' ),
							'default'          => false,
							'sections_to_hide' => array(
								'_aryel_ar_3d_product_viewer_product_miniature_advanced_section',
							),
						),
					),
				),
				array(
					'id'     => '_aryel_ar_3d_product_viewer_product_miniature_advanced_section',
					'title'  => __( 'Extras', 'aryel' ),
					'fields' => array(
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_miniature_advanced_gesture',
							'type'    => 'checkbox',
							'label'   => __( 'Gestures', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_miniature_advanced_autorotate',
							'type'    => 'checkbox',
							'label'   => __( 'Autorotate', 'aryel' ),
							'default' => true,
						),
						array(
							'name'    => '_aryel_ar_3d_product_viewer_product_miniature_advanced_output_encoding',
							'type'    => 'select',
							'label'   => __( 'Output encoding', 'aryel' ),
							'default' => 'linear',
							'options' => array(
								'linear' => __( 'Linear', 'aryel' ),
								'sRGB'   => __( 'sRGB', 'aryel' ),
							),
						),
					),
				),
				array(
					'title'  => __( 'Preview', 'aryel' ),
					'fields' => array(
						array(
							'type'            => 'preview',
							'params'          => array(
								'action' => 'aryel_ar_3d_product_viewer_preview',
								'type'   => 'product_miniature',
								'_nonce' => wp_create_nonce( 'aa3dpv_preview_product_miniature' ),
							),
							'fields_to_watch' => array(
								AryelAR3DProductViewer::CAMPAIGN_ID_KEY,
								'_aryel_ar_3d_product_viewer_general_settings_loader_color',
								'_aryel_ar_3d_product_viewer_general_settings_spr_scale',
								'_aryel_ar_3d_product_viewer_general_settings_spr_position',
								'_aryel_ar_3d_product_viewer_general_settings_spr_rotation',
								'_aryel_ar_3d_product_viewer_general_settings_camera_fov',
								'_aryel_ar_3d_product_viewer_general_settings_camera_near',
								'_aryel_ar_3d_product_viewer_general_settings_camera_far',
								'_aryel_ar_3d_product_viewer_general_settings_camera_position',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_gesture',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_autorotate',
								'_aryel_ar_3d_product_viewer_general_settings_advanced_output_encoding',
								'_aryel_ar_3d_product_viewer_general_settings_environment_image_id',
							),
							'toggle'          => '_aryel_ar_3d_product_viewer_product_miniature_toggle',
							'tab_name'        => 'product_miniature',
							'height'          => 400, // Height in px.
						),
					),
				),
			),
		);

		return array(
			$general_settings_tab,
			$product_gallery_tab,
			$additional_product_tab_tab,
			$product_miniature_tab,
			$view_in_ar_tab,
		);
	}

	/**
	 * Get the viewer attributes.
	 *
	 * @param string $type The viewer type.
	 *
	 * @return array
	 */
	protected static function prepare_viewer_attributes( $type ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$attributes = array(
			'scale'       => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_spr_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_spr_scale" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_spr_scale'] ?? '' ) ),
			'position'    => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_spr_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_spr_position" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_spr_position'] ?? '' ) ),
			'rotation'    => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_spr_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_spr_rotation" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_spr_rotation'] ?? '' ) ),
			'loader'      => self::prepare_multiple_options(
				array(
					'color' => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_loader_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_loader_color" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_loader_color'] ?? '' ) ),
				)
			),
			'camera'      => self::prepare_multiple_options(
				array(
					'fov'      => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_fov" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_camera_fov'] ?? '' ) ),
					'near'     => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_near" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_camera_near'] ?? '' ) ),
					'far'      => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_far" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_camera_far'] ?? '' ) ),
					'position' => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_camera_position" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_camera_position'] ?? '' ) ),
				)
			),
			'renderer'    => self::prepare_multiple_options(
				array(
					'output-encoding' => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_output_encoding" ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_advanced_output_encoding'] ?? '' ) ),
				)
			),
			'gestures'    => self::prepare_multiple_options(
				array(
					'pan'         => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture" ] ?? 'false' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_advanced_gesture'] ?? 'false' ) ),
					'zoom'        => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture" ] ?? 'false' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_advanced_gesture'] ?? 'false' ) ),
					'rotate'      => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture" ] ?? 'false' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_advanced_gesture'] ?? 'false' ) ),
					'damping'     => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture" ] ?? 'false' ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_advanced_gesture'] ?? 'false' ) ),
					'auto-rotate' => ( self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle" ] ?? 'false' ) ) ) ? self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_advanced_autorotate" ] ?? 'false' ) ) ) : self::is_true( sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_advanced_autorotate'] ?? 'false' ) ) ) ) ? '2' : 0,
				)
			),
			'campaign'    => sanitize_text_field( wp_unslash( $_GET[ AryelAR3DProductViewer::CAMPAIGN_ID_KEY ] ?? '' ) ),
			'show_button' => self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_button_enabled" ] ?? 'false' ) ) ),
			'button'      => self::prepare_button_attributes( $type ),
		);

		// Lets check for hdr image.
		$image_id              = self::is_true( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_environment_toggle" ] ?? 'false' ) ) ) ? sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_environment_image_id" ] ?? null ) ) : sanitize_text_field( wp_unslash( $_GET['_aryel_ar_3d_product_viewer_general_settings_environment_image_id'] ?? null ) );
		$environment_image_url = wp_get_attachment_url( $image_id );
		if ( $environment_image_url ) {
			$attributes['environment'] = self::prepare_multiple_options(
				array(
					'src'     => $environment_image_url,
					'visible' => 'true',
					'enable'  => 'true',
				)
			);
		}

		return $attributes;
	}

	/**
	 * Prepare multiple options as string
	 *
	 * @param array $values Values to prepare.
	 *
	 * @return string
	 */
	protected static function prepare_multiple_options( $values ) {
		$options = array();
		foreach ( $values as $key => $value ) {
			$options[] = "{$key}: {$value}";
		}

		return implode( '; ', $options );
	}

	/**
	 * Prepare button attributes
	 *
	 * @param string $type Type of component.
	 *
	 * @return array
	 */
	protected static function prepare_button_attributes( $type ) {
		$attributes = array(
			'container_class' => implode(
				' ',
				array_filter(
					array(
						'aryel-ar-3d-product-viewer-view-in-ar-button-container',
						in_array( $type, array( 'additional_product_tab', 'product_gallery' ), true ) ? 'aryel-ar-3d-product-viewer-embed-ui-button' : '',
						'text-' . ( sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_button_text_alignment" ] ?? 'left' ) ) ),
					)
				)
			),
			'text'            => sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_button_label" ] ?? '' ) ),
			'attributes'      => array(
				'target' => '_blank',
			),
			'style'           => self::prepare_multiple_options(
				array(
					'color'            => sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_button_text_color" ] ?? '' ) ),
					'background-color' => sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_button_background_color" ] ?? '' ) ),
				)
			),
			'class'           => implode(
				' ',
				array_filter(
					array(
						'aryel-ar-3d-product-viewer-view-in-ar-button',
						sanitize_text_field( wp_unslash( $_GET[ "_aryel_ar_3d_product_viewer_{$type}_button_css_class" ] ?? '' ) ),
					)
				)
			),
			'is_embed'        => in_array( $type, array( 'additional_product_tab', 'product_gallery' ), true ),
		);

		return $attributes;
	}

	/**
	 * Check if value is true
	 *
	 * @param string $value Value to check.
	 *
	 * @return bool
	 */
	protected static function is_true( $value ) {
		return 'true' === $value;
	}

	/**
	 * Get campaign url
	 *
	 * @param string $campaign_id Campaign ID.
	 * @param string $api_key    API Key.
	 *
	 * @return string
	 */
	protected static function get_campaign_data( $campaign_id, $api_key ) {
		$url     = "https://app.aryel.io/api/v1/campaigns/{$campaign_id}?api-key={$api_key}&embed=3d";
		$headers = get_headers( $url );
		$status  = substr( $headers[0], 9, 3 );

		if ( '200' === $status ) {
			$result = wp_remote_get( "https://app.aryel.io/api/v1/campaigns/{$campaign_id}?api-key={$api_key}&embed=3d" );
			$result = json_decode( wp_remote_retrieve_body( $result ), true );

			return array(
				'trigger'      => $result['trigger'],
				'campaign_url' => $result['campaign_url'],
			);
		}

		return null;
	}

	/**
	 * Get campaign url
	 *
	 * @param string $campaign_id Campaign ID.
	 * @param string $api_key    API Key.
	 *
	 * @return string
	 */
	protected static function get_campaign_url( $campaign_id, $api_key ) {
		$campaign_data = self::get_campaign_data( $campaign_id, $api_key );
		return $campaign_data['campaign_url'] ?? '#';
	}
}
