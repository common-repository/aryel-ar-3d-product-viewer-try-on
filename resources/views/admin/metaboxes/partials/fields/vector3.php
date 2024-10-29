<?php
/**
 * Vector3 field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$values = explode( ' ', $field['value'] );
if ( count( $values ) < 3 ) {
	$values = array_pad( $values, 3, '0' );
}
?>

<p class="form-field" x-data="Vector3State" data-x="<?php echo esc_attr( $values[0] ); ?>" data-y="<?php echo esc_attr( $values[1] ); ?>" data-z="<?php echo esc_attr( $values[2] ); ?>">
	<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<input type="hidden" name="<?php echo esc_attr( $field['name'] ); ?>" id="<?php echo esc_attr( $field['name'] ); ?>" :value="x + ' ' + y + ' ' + z">
	<span class="three-values">
		<input 
			type="number" 
			x-model="x"
			<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
				<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
			<?php endforeach; ?>
		> 
		<input 
			type="number" 
			x-model="y"
			<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
				<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
			<?php endforeach; ?>
		> 
		<input 
			type="number" 
			x-model="z" 
			<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
				<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
			<?php endforeach; ?>
		> 
	</span> 
</p>