<?php
/**
 * Aryel 3D metabox.
 *
 * @package AryelAR3DProductViewer
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div 
	class="aryel-metabox panel-wrap" 
	<?php if ( $api_key ) : ?>
		x-data="Aryel3DMetaboxState"
		data-ajax-url="<?php echo esc_attr( admin_url( 'admin-ajax.php' ) ); ?>"
		data-ajax-nonce=<?php echo esc_attr( $nonce_ajax ); ?>
		data-post-id="<?php echo esc_attr( get_the_ID() ); ?>"
	<?php endif; ?>
>
	<input type="hidden" name="_aryel_ar_3d_product_viewer_nonce" value="<?php echo esc_attr( $nonce_metabox ); ?>">

	<?php if ( ! $api_key ) : ?>
		<p class="error no-api-key-error">
			<?php /* translators: %s: open tag with url %s: close tag */ ?>
			<?php echo wp_kses_post( wp_sprintf( __( 'Before using this plugin you need to connect your Aryel account by inserting your API key in the %1$sbasic setup and configuration page%2$s.', 'aryel' ), '<a href="' . admin_url( 'admin.php?page=aryel-ar-3d-product-viewer&tab=aryel' ) . '">', '</a>' ) ); ?></p>
	<?php else : ?>

		<div class="metabox-header hidden" x-cloak> &mdash;
			<p class="form-field">
				<label for="aryel-ar-3d-product-viewer-campaign-id"><?php esc_html_e( 'Campaign ID', 'aryel' ); ?></label>
				<input type="text" name="_aryel_ar_3d_product_viewer_campaign_id" id="_aryel_ar_3d_product_viewer_campaign_id" value="<?php echo esc_attr( $campaign_id ); ?>">
			</p>
		</div>

		<input type="hidden" name="_aryel_ar_3d_product_viewer_campaign_type" :value="campaignType">

		<p class="error" x-show="!isCampaignIDSet" x-cloak><?php echo wp_kses_post( __( 'Before integrating Aryel’s experiences, you must identify the Campaign ID. <a href="http://jump.aryel.io/campaign_id" target="_blank">Learn how to get it</a>.', 'aryel' ) ); ?></p>
		<p class="error" x-show="isCampaignIDSet && !isCampaignIDValid && !!errorMessage" x-html="errorMessage" x-cloak></p>
		<p class="error" x-show="isCampaignIDSet && !isCampaignIDValid && !errorMessage" x-cloak><?php esc_html_e( 'We encountered an unexpected error validating the campaign ID. Try again later.', 'aryel' ); ?></p>
			
		<div x-show="isCampaignIDSet && isCampaignIDValid" x-cloak>
			<ul class="wca-tabs">
				<?php foreach ( $tabs as $tab_item ) : ?>
					<li 
					<?php
					if ( $tab_item['show_for_surface'] ?? false ) :
						?>
						x-show="isSurface"<?php endif; ?> class="aryel_ar_3d_product_viewer_<?php echo esc_attr( $tab_item['name'] ); ?>_options aryel_ar_3d_product_viewer_<?php echo esc_attr( $tab_item['name'] ); ?>_tab <?php echo esc_attr( isset( $tab_item['class'] ) ? implode( ' ', (array) $tab_item['class'] ) : '' ); ?>">
						<a href="#aryel_ar_3d_product_viewer_<?php echo esc_attr( $tab_item['name'] ); ?>">
							<img class="active" src="<?php echo esc_attr( $tab_item['icon'] ); ?>" alt="icon" />
							<img class="inactive" src="<?php echo esc_attr( $tab_item['icon_inactive'] ); ?>" alt="icon" />
							<span><?php echo esc_html( $tab_item['label'] ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
				
			<?php foreach ( $tabs as $tab_item ) : ?>
				<?php include ARYEL_AR_3D_PRODUCT_VIEWER_PLUGIN_DIR . '/resources/views/admin/metaboxes/aryel-3d-metabox-tab-content.php'; ?>
			<?php endforeach; ?>
			<div class="clear"></div>
		</div>
	<?php endif; ?>
</div>
