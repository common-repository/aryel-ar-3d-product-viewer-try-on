<?php
/**
 * Button partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div 
	class="<?php echo esc_html( $button['container_class'] ); ?>" 
	<?php
	if ( $button['is_embed'] ?? false ) :
		?>
		aryel-embed-button<?php endif; ?>
>
	<a 
		<?php foreach ( $button['attributes'] ?? array() as $key => $value ) : ?>
			<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
		<?php endforeach; ?>
		style="<?php echo esc_attr( $button['style'] ?? '' ); ?>" 
		class="<?php echo esc_attr( $button['class'] ); ?>"
	>
		<?php echo esc_html( $button['text'] ); ?>
	</a>
</div>