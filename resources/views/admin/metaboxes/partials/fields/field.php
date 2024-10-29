<?php
/**
 * Field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

switch ( $field['type'] ?? 'text' ) {
	case 'text':
	case 'number':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/input.php';
		break;

	case 'checkbox':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/checkbox.php';
		break;

	case 'select':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/select.php';
		break;

	case 'vector3':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/vector3.php';
		break;

	case 'color-picker':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/color-picker.php';
		break;

	case 'toggle':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/toggle.php';
		break;

	case 'wysiwyg':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/wysiwyg.php';
		break;

	case 'media':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/media.php';
		break;

	case 'preview':
		include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/preview.php';
		break;
}
