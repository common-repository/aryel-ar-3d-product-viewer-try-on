import Alpine from 'alpinejs'
import axios from 'axios'

Alpine.data('PreviewState', () => ({
    init() {
        this.ajaxUrl = this.$el.dataset.ajaxUrl
        this.params = JSON.parse(this.$el.dataset.params)
        this.fieldsToWatch = JSON.parse(this.$el.dataset.fieldsToWatch)
        this.toggle = JSON.parse(this.$el.dataset.toggle)
        this.tabName = JSON.parse(this.$el.dataset.tabName)

        this.initObserverForToggle()
        this.initObserverForTabActiveStatus()
        this.initObserverForFields()
    },

    initObserverForToggle() {
        if (!this.toggle) {
            this.isToggleChecked = true
            return
        }
        
        this.$watch(this.toggle, async (value) => {
            this.isToggleChecked = value
            if (this.isToggleChecked) {
                this.refresh()
            }
        })

        // First check
        this.isToggleChecked = this[this.toggle]
    },

    initObserverForTabActiveStatus() {
        const vm = this
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                vm.isTabActive = mutation.target.classList.contains('active')
                if (vm.isTabActive) {
                    vm.refresh()
                }
            });
        });

        const el = document.getElementsByClassName(`aryel_ar_3d_product_viewer_${this.tabName}_tab`)[0]

        observer.observe(el, {
            attributes: true,
            attributeFilter: ['class']
        });
    },

    initObserverForFields() {
        this.fieldsToWatch.forEach(field => {
            jQuery(`#${field}`).on('change', () => this.refresh())
            jQuery(`#${field}`).on('input', () => this.refresh())
            jQuery(`#${field}`).on('irischange', () => setTimeout(() => this.refresh(), 50))
        })
    },

    /**
     * Refetch the preview
     */
    async refresh() {
        if (!this.isPreviewEnabled) {
            return
        }

        // Hide the iframe
        this.isContentShown = false

        // Get the new preview url
        const previewUrl = this.buildPreviewUrl()

        // Fetch the preview
        const reponse = await axios.get(previewUrl)
        this.content = reponse.data

        // Show the iframe after 100ms
        setTimeout(() => this.isContentShown = true, 50)
    },

    buildPreviewUrl() {
        let params = this.params
        this.fieldsToWatch.forEach(field => {
            const input = jQuery(`#${field}`)
            params[field] = input.attr('type') === 'checkbox' ? input.is(':checked') : input.val()
        })
        
        return this.ajaxUrl + '?' + (new URLSearchParams(params).toString())
    },

    get isPreviewEnabled() {
        return this.isTabActive && this.isToggleChecked
    },

    ajaxUrl: '',
    params: {},
    fieldsToWatch: [],
    toggle: '',
    tabName: '',

    isToggleChecked: false,
    isTabActive: false,

    isContentShown: false,
    content: '',
}))