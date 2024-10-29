import Alpine from 'alpinejs'

Alpine.data('ToggleState', () => ({
    init() {
        this.sections = JSON.parse(this.$el.dataset.sections)

        this.toggleSections()
        this.$watch('open', () => { this.toggleSections() })
    },

    toggleSections() {
        this.sections.forEach((section) => {
            jQuery(`#option_group${section}`).toggle(this.open)
        })
    },

    sections: [],
    open: false,
}))