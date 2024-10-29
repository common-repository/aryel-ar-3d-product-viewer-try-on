<?php
/**
 * Embed viewer partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Retrieve the button configurations.
$show_button = (bool) ( $viewer['show_button'] ?? false );
$button      = $viewer['button'] ?? array();

unset( $viewer['show_button'] );
unset( $viewer['button'] );

?>

<aryel-embed 
	<?php foreach ( $viewer ?? array() as $key => $value ) : ?>
		<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
	<?php endforeach; ?>
>
	<?php if ( $show_button ) : ?>
		<?php include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/button.php'; ?>
	<?php endif; ?>
</aryel-embed>