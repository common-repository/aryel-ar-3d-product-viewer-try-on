<?php
/**
 * Settings page.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="settings-banner">
	<img src="<?php echo esc_attr( plugins_url( '/resources/images/admin/aryel_logo.png', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ) ); ?>" alt="">
</div>

<h1 class="main-heading"><?php esc_html_e( 'AR/3D Product Viewer & Try-On: basic setup and configuration', 'aryel' ); ?></h1>

<div class="settings-content">
	<h2>
		<?php esc_html_e( 'Connect Aryel', 'aryel' ); ?>
		<?php if ( ! empty( $api_key_field_value ) ) : ?>
			<span class="status-badge">
				<?php esc_attr_e( 'Connected', 'aryel' ); ?>
			</span>
		<?php endif; ?>
	</h2>
	<p class="plugin-description">
		<?php esc_html_e( 'With Aryel’s AR/3D Product Viewer & Try-On your customers can access realistic and true-to-size product previews in just 1 click.', 'aryel' ); ?>
		<br>
		<?php esc_html_e( 'To get started, you need to connect your Aryel account. Paste your API key in the field below.', 'aryel' ); ?>
	</p>
	<form method="POST" action="options.php">
		<?php settings_fields( 'aryel-ar-3d-product-viewer' ); ?>
		
		<div class="input-wrap">
			<label for="aryel-ar-3d-product-viewer-api-key"><?php esc_html_e( 'API Key', 'aryel' ); ?></label>
			<input type="text" id="aryel-ar-3d-product-viewer-api-key" name="<?php echo esc_attr( $api_key_field_name ); ?>" value="<?php echo esc_attr( $api_key_field_value ); ?>">
			<a href="http://jump.aryel.io/api_key" class="help-tip" target="_blank"></a>
		</div>
	
		<?php submit_button(); ?>
	</form>

	<div class="instructions-row">
		<div class="img-wrap">
			<img src="<?php echo esc_attr( plugins_url( '/resources/images/admin/Instruction01.png', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ) ); ?>" alt="">
		</div>
		<div class="content-wrap">
			<h3><?php esc_html_e( 'Assign the experience to a product', 'aryel' ); ?></h3>
			<p>
				<?php esc_html_e( 'To see the 3D preview on your product pages, you need to link your Aryel experiences to related products. To do so, simply go to the product page, look for the AR/3D Product Viewer & Try-On section, and type in the campaign ID.', 'aryel' ); ?>
			</p>
			<a href="<?php echo esc_attr( admin_url( 'edit.php?post_type=product', 'https' ) ); ?>"><?php esc_html_e( 'Go to products', 'aryel' ); ?></a>
		</div>
	</div>

	<div class="instructions-row">
		<div class="img-wrap">
			<img src="<?php echo esc_attr( plugins_url( '/resources/images/admin/Instruction02.png', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ) ); ?>" alt="">
		</div>
		<div class="content-wrap">
			<h3><?php esc_html_e( 'Enable blocks', 'aryel' ); ?></h3>
			<p>
				<?php esc_html_e( 'You can add the 3D embed viewer in different ways: product gallery, additional tab, product thumbnail.', 'aryel' ); ?>
				<br>
				<?php esc_html_e( 'The AR button instead will allow a direct link to the AR experience. Enable one or more of them from the product page as per your preference and don’t forget you can disable them at any time!', 'aryel' ); ?>
			</p>
		</div>
	</div>

	<div class="instructions-row">
		<div class="img-wrap">
			<img src="<?php echo esc_attr( plugins_url( '/resources/images/admin/Instruction03.png', ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_FILE ) ); ?>" alt="">
		</div>
		<div class="content-wrap">
			<h3><?php esc_html_e( 'Setup blocks', 'aryel' ); ?></h3>
			<p>
				<?php esc_html_e( 'You can setup the style and text of the buttons included in both your AR button and 3D embed viewer (if enabled).', 'aryel' ); ?>
				<br>
				<?php esc_html_e( 'The viewer also allows you to customize colors of the loader, initial position of the product, camera settings, output encoding and to enable & disable gestures and auto rotation of the 3D model. Play with the values to get the expected result. The preview of the viewer is automatically updating at every step helping you to find your perfect fit!', 'aryel' ); ?>
			</p>
		</div>
	</div>
</div>

