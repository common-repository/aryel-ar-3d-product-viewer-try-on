<?php
/**
 * Abstract util class.
 *
 * @package AryelAR3DProductViewer
 */

namespace Aryel\AryelAR3DProductViewer\Utils;

use AryelAR3DProductViewer;

/**
 * Abstract util class.
 */
abstract class AbstractUtil {

	/**
	 * Prepare multiple options.
	 *
	 * @param string  $type The component type.
	 * @param WP_Post $post The post object.
	 *
	 * @return array
	 */
	abstract public static function render( $type, $post );

	/**
	 * Prepare multiple options.
	 *
	 * @param string $type The component type.
	 * @param int    $post_id The ID of the post.
	 *
	 * @return array
	 */
	protected static function prepare_viewer_attributes( $type, $post_id ) {
		$attributes = array(
			'scale'       => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_spr_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_spr_scale", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_spr_scale', true ),
			'position'    => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_spr_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_spr_position", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_spr_position', true ),
			'rotation'    => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_spr_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_spr_rotation", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_spr_rotation', true ),
			'loader'      => self::prepare_multiple_options(
				array(
					'color' => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_loader_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_loader_color", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_loader_color', true ),
				)
			),
			'camera'      => self::prepare_multiple_options(
				array(
					'fov'      => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_fov", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_camera_fov', true ),
					'near'     => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_near", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_camera_near', true ),
					'far'      => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_far", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_camera_far', true ),
					'position' => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_camera_position", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_camera_position', true ),
				)
			),
			'renderer'    => self::prepare_multiple_options(
				array(
					'output-encoding' => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_output_encoding", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_advanced_output_encoding', true ),
				)
			),
			'gestures'    => self::prepare_multiple_options(
				array(
					'pan'         => ( (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle", true ) ? (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture", true ) : (bool) get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_advanced_gesture', true ) ) ? 'true' : 'false',
					'zoom'        => ( (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle", true ) ? (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture", true ) : (bool) get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_advanced_gesture', true ) ) ? 'true' : 'false',
					'rotate'      => ( (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle", true ) ? (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture", true ) : (bool) get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_advanced_gesture', true ) ) ? 'true' : 'false',
					'damping'     => ( (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle", true ) ? (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_gesture", true ) : (bool) get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_advanced_gesture', true ) ) ? 'true' : 'false',
					'auto-rotate' => ( (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_toggle", true ) ? (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_advanced_autorotate", true ) : (bool) get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_advanced_autorotate', true ) ) ? '2' : 0,
				)
			),
			'campaign'    => get_post_meta( $post_id, AryelAR3DProductViewer::CAMPAIGN_ID_KEY, true ),
			'show_button' => (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_enabled", true ),
			'button'      => self::prepare_button_attributes( $type, $post_id ),
		);

		// Lets check for hdr image.
		$image_id              = (bool) get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_environment_toggle", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_environment_image_id", true ) : get_post_meta( $post_id, '_aryel_ar_3d_product_viewer_general_settings_environment_image_id', true );
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
	 * Prepare button attributes
	 *
	 * @param string $type The type of the component.
	 * @param int    $post_id The Post ID.
	 *
	 * @return array
	 */
	protected static function prepare_button_attributes( $type, $post_id ) {
		$attributes = array(
			'container_class' => implode(
				' ',
				array_filter(
					array(
						'aryel-ar-3d-product-viewer-view-in-ar-button-container',
						in_array( $type, array( 'additional_product_tab', 'product_gallery' ), true ) ? 'aryel-ar-3d-product-viewer-embed-ui-button' : '',
						'text-' . ( get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_text_alignment", true ) ? get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_text_alignment", true ) : 'left' ),
					)
				)
			),
			'text'            => get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_label", true ),
			'attributes'      => array(
				'target' => '_blank',
				'href'   => self::get_campaign_url( $post_id ),
			),
			'style'           => self::prepare_multiple_options(
				array(
					'color'            => get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_text_color", true ),
					'background-color' => get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_background_color", true ),
				)
			),
			'class'           => implode(
				' ',
				array_filter(
					array(
						'aryel-ar-3d-product-viewer-view-in-ar-button',
						get_post_meta( $post_id, "_aryel_ar_3d_product_viewer_{$type}_button_css_class", true ),
					)
				)
			),
			'is_embed'        => in_array( $type, array( 'additional_product_tab', 'product_gallery' ), true ),
		);

		return $attributes;
	}

	/**
	 * Prepare multiple options
	 *
	 * @param array $values The values of the options.
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
	 * Get campaign url
	 *
	 * @param int $post_id The Post ID.
	 *
	 * @return string
	 */
	protected static function get_campaign_url( $post_id ) {
		return get_post_meta( $post_id, AryelAR3DProductViewer::CAMPAIGN_URL_KEY, true );
	}
}
