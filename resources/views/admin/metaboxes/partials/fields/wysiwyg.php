<?php
/**
 * WYSIWYG field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="form-field">
	<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<?php
		wp_editor(
			$field['value'],
			$field['name'],
			array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => $field['name'],
				'textarea_rows' => 10,
			)
		);
		?>
</div>