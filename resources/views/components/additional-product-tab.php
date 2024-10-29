<?php
/**
 * Additional product tab partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div x-data="AdditionalProductTabState">
	<?php if ( $tab_content ) : ?>
		<?php echo esc_html( $tab_content ); ?>
	<?php endif; ?>

	<template x-if="show">
		<div class="aryel-ar-3d-product-viewer-additional-product-tab" style="width: 100%; height: <?php echo esc_attr( $container_height ); ?>px">
			<?php self::render_embed_viewer(); ?>
		</div>
	</template>
</div>