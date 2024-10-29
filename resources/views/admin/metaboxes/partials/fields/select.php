<?php
/**
 * Select field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="form-field">
	<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	
	<select 
		name="<?php echo esc_attr( $field['name'] ); ?>" 
		id="<?php echo esc_attr( $field['name'] ); ?>" 
		class="select short"
		<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
			<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
		<?php endforeach; ?>
	>
		<?php foreach ( $field['options'] as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" 
										<?php
										if ( $field['value'] === $value ) :
											?>
				selected="selected"<?php endif; ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
</p>