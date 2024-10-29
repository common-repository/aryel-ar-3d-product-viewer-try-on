import Alpine from 'alpinejs';
import axios from 'axios';

const VALIDATE_CAMPAIGN_ID_ACTION = 'aryel_ar_3d_product_viewer_validate_campaign_id'

Alpine.data('Aryel3DMetaboxState', () => ({
    init() {
        this.ajaxUrl = this.$el.dataset.ajaxUrl
        this.ajaxNonce = this.$el.dataset.ajaxNonce
        this.postId = this.$el.dataset.postId

        this.moveMetaboxHeader()
        this.setCampaignIdObserver()
        
        this.initWCATabs()
        this.updateCampaignId()
    },

    moveMetaboxHeader() {
        const customMetaboxHeader = jQuery(this.$el).find('.metabox-header')
        const metaboxTitle = jQuery('#aryel-ar-3d-product-viewer .postbox-header h2');
        metaboxTitle.after(customMetaboxHeader)

        customMetaboxHeader.removeClass('hidden')
    },

    setCampaignIdObserver() {
        const vm = this
        let timeout = null
        jQuery('#_aryel_ar_3d_product_viewer_campaign_id').on('input', function () {
            clearTimeout(timeout)
            timeout = setTimeout(() => vm.updateCampaignId(), 500)
        })
    },

    initWCATabs() {
        jQuery('ul.wca-tabs').show();
        jQuery('ul.wca-tabs a').on('click', function (e) {
            e.preventDefault();
            var panel_wrap = jQuery(this).closest('div.panel-wrap');
            jQuery('ul.wca-tabs li', panel_wrap).removeClass('active');
            jQuery(this ).parent().addClass('active');
            jQuery('div.panel', panel_wrap).hide();
            jQuery(jQuery(this).attr('href')).show();
        });
    },
    
    async updateCampaignId() {
        this.campaignId = this.getCampaignId()
        if (!this.campaignId) {
            return
        }

        // Reset the error message
        this.errorMessage = 'Loading...'
        this.isCampaignIDValid = false

        // Let's check if the campaign ID has been already used
        try {
            const params = {
                'campaign_id': this.campaignId, 
                'post_id': this.postId,
                'action': VALIDATE_CAMPAIGN_ID_ACTION,
                '_nonce': this.ajaxNonce,
            }

            const ajaxUrl = this.ajaxUrl + '?' + (new URLSearchParams(params).toString())
            const response = await axios.get(ajaxUrl)

            if (response.data.valid) {
                this.isCampaignIDValid = true
                this.campaignType = response.data.trigger ?? 'face'
                this.setActiveTab()
            } else {
                this.errorMessage = response.data.message
                this.isCampaignIDValid = false
            }
        } catch (error) {
            this.isCampaignIDValid = false
            this.errorMessage = null;
        }
    },

    setActiveTab() {
        const allTabs = jQuery('#aryel-ar-3d-product-viewer .wca-tabs li')
        allTabs.each(function () {
            jQuery(this).removeClass('active')
        })

        const allPanel = jQuery('#aryel-ar-3d-product-viewer .panel')
        allPanel.each(function () {
            jQuery(this).addClass('hidden')
        })

        setTimeout(() => {
            const visibleTabs = jQuery('#aryel-ar-3d-product-viewer .wca-tabs li:visible')
            visibleTabs.first().addClass('active')

            visibleTabs.first().find('a').trigger('click')
        }, 100)
    },

    getCampaignId() {
        return jQuery('#_aryel_ar_3d_product_viewer_campaign_id').val()
    },

    get isCampaignIDSet () {
        return !!this.campaignId
    },

    get isSurface() {
        return this.campaignType === 'surface'
    },

    campaignId: '',
    campaignType: 'face',
    ajaxUrl: '',
    ajaxNonce: '',
    postId: '',
    isCampaignIDValid: false,
    errorMessage: 'Loading...',
}));