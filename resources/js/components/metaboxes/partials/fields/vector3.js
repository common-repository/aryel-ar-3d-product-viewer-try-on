import Alpine from 'alpinejs'

Alpine.data('Vector3State', () => ({
    init() {
        this.x = +this.$el.dataset.x
        this.y = +this.$el.dataset.y
        this.z = +this.$el.dataset.z

        this.hiddenEl = this.$el.querySelector('[type="hidden"]')

        this.initObserver()
    },

    initObserver() {
        this.$watch(['x', 'y', 'z'], () => {
            this.hiddenEl.dispatchEvent(new Event('change'))
        })
    },

    x: 0,
    y: 0,
    z: 0,
    hiddenEl: null,
}))