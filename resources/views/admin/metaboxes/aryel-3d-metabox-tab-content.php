<?php
/**
 * Tab content partial.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div 
<?php
if ( $tab_item['show_for_surface'] ?? false ) :
	?>
	x-show="isSurface"<?php endif; ?> id="<?php echo esc_attr( "aryel_ar_3d_product_viewer_{$tab_item['name']}" ); ?>" class="panel hidden" 
	<?php
	if ( isset( $tab_item['toggle'] ) ) :
		?>
	x-data="{<?php echo esc_attr( $tab_item['toggle']['name'] ); ?>: false}"<?php endif; ?>>
	<p class="panel_description"><?php echo esc_html( $tab_item['description'] ); ?></p>

	<?php if ( isset( $tab_item['toggle'] ) ) : ?>
		<div class="options_group">
			<p class="form-field">
				<label for="<?php echo esc_attr( $tab_item['toggle']['name'] ); ?>"><?php echo esc_html( $tab_item['toggle']['label'] ); ?></label>
				<input type="checkbox" class="checkbox" id="<?php echo esc_attr( $tab_item['toggle']['name'] ); ?>" name="<?php echo esc_attr( $tab_item['toggle']['name'] ); ?>" x-model="<?php echo esc_attr( $tab_item['toggle']['name'] ); ?>" 
																		<?php
																		if ( $tab_item['toggle']['value'] ) :
																			?>
					x-init="<?php echo esc_attr( $tab_item['toggle']['name'] ); ?> = true"<?php endif; ?>>
			</p>
		</div>
	<?php endif; ?>

	<div 
	<?php
	if ( isset( $tab_item['toggle'] ) ) :
		?>
		x-show="<?php echo esc_attr( $tab_item['toggle']['name'] ); ?>"<?php endif; ?>>
		<?php foreach ( $tab_item['sections'] as $section ) : ?>
			<div class="options_group" 
			<?php
			if ( $section['id'] ?? false ) :
				?>
				id="<?php echo esc_attr( "option_group{$section['id']}" ); ?>"<?php endif; ?>>
				<?php if ( isset( $section['title'] ) ) : ?>
					<h3 class="options_group_title"><?php echo esc_html( $section['title'] ); ?></h3>
				<?php endif; ?>

				<?php
				foreach ( $section['fields'] as $field ) {
					include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/partials/fields/field.php';
				}
				?>
			</div>
		<?php endforeach; ?>

	</div>
</div>