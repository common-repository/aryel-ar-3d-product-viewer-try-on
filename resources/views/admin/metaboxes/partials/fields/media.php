<?php
/**
 * Input field partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="form-field media" x-data="MediaState" data-image-id="<?php echo esc_attr( $field['value'] ); ?>">
	<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<input 
		type="hidden" 
		name="<?php echo esc_attr( $field['name'] ); ?>" 
		id="<?php echo esc_attr( $field['name'] ); ?>" 
		<?php foreach ( $field['attributes'] ?? array() as $key => $value ) : ?>
			<?php echo esc_attr( $key ); ?>="<?php echo esc_attr( $value ); ?>"
		<?php endforeach; ?> 
		:value="imageId"
	>
	
	<span class="buttons">
		<button type="button" class="button"><span class="wp-media-buttons-icon"></span><?php esc_html_e( 'Choose environment', 'aryel' ); ?></button>
		<span class="woocommerce-help-tip" tabindex="0" for="content">
			<span><?php esc_html_e( 'Accepted file type: .hdr', 'aryel' ); ?></span>
		</span>

		<a class="remove" x-show="!!imageId" x-on:click.prevent="removeImage()"><?php esc_html_e( 'Remove', 'aryel' ); ?></a>
	</span>
</p>