<?php
/**
 * Preview template.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php if ( $show_embed_viewer ) : ?>
	<?php include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/embed-viewer.php'; ?>
<?php endif; ?>

<?php if ( $show_view_in_ar ) : ?>
	<?php include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/button.php'; ?>
	<?php
endif;
