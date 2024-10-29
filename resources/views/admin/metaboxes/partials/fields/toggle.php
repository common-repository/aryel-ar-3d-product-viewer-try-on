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

<p class="form-field" x-data="ToggleState" data-sections="<?php echo esc_attr( htmlspecialchars( wp_json_encode( $field['sections_to_hide'] ) ) ); ?>">
	<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<input 
		type="checkbox" 
		class="checkbox" 
		id="<?php echo esc_attr( $field['name'] ); ?>" 
		name="<?php echo esc_attr( $field['name'] ); ?>" 
		<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
			<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
		<?php endforeach; ?>
		x-model="open" 
		<?php
		if ( $field['value'] ) :
			?>
			x-init="open = true"<?php endif; ?>
	>
</p>