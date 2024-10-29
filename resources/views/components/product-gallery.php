<?php
/**
 * Product gallery partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="aryel-ar-3d-product-viewer-embed-viewer-container product-gallery">
	<div class="embed-viewer-container">
		<div class="embed-viewer">
			<?php self::render_embed_viewer(); ?>
		</div>
	</div>
</div>