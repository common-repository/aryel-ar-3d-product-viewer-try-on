<?php
/**
 * Preview field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fields_to_watch = $field['fields_to_watch'] ?? array();
foreach ( $tab_item['sections'] as $section ) {
	$fields_to_watch = array_merge(
		$fields_to_watch,
		array_map(
			function ( $item ) {
				return $item['name'] ?? null;
			},
			$section['fields']
		)
	);
}
$fields_to_watch = array_filter( $fields_to_watch );
$height          = (int) $field['height'] ?? 400;
?>

<div
	class="preview-field"
	x-data="PreviewState"
	data-ajax-url="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>"
	data-params="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $field['params'] ) ) ); ?>"
	data-fields-to-watch="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $fields_to_watch ) ) ); ?>"
	data-toggle="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $field['toggle'] ?? null ) ) ); ?>"
	data-tab-name="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $field['tab_name'] ) ) ); ?>"
	style="height: <?php echo esc_attr( $height + 12 ); ?>px;"
>
	<template x-if="isPreviewEnabled && isContentShown">
		<div style="height: <?php echo esc_attr( $height ); ?>px;" x-html="content"></div>
	</template>
</div>