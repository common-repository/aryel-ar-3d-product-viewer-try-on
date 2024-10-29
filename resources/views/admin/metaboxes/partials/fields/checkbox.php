<?php
/**
 * Checkbox field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>


<p class="form-field">
	<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<input 
		type="checkbox" 
		class="checkbox" 
		id="<?php echo esc_attr( $field['name'] ); ?>" 
		name="<?php echo esc_attr( $field['name'] ); ?>" 
		<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
			<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
		<?php endforeach; ?>
		<?php
		if ( $field['value'] ) :
			?>
		checked<?php endif; ?>
	>
</p>