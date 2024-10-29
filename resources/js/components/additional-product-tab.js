import Alpine from 'alpinejs'

const DELAY_INIT = 50
const DELAY_INIT_OBSERVER = 200;

Alpine.data('AdditionalProductTabState', () => ({
    init() {     
        setTimeout(() => this.render(), DELAY_INIT)

        // When the additional tab is visible
        // we need to trigger a resize event
        // to make sure the embed viewer is loaded
        setTimeout(() => this.initEmbedViewerVisibilityObserver(), DELAY_INIT_OBSERVER)
    },

    initEmbedViewerVisibilityObserver() {
        const $element = document.querySelector('.aryel-ar-3d-product-viewer-additional-product-tab');

        var options = {
            root: document.documentElement
        };
    
        var observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.intersectionRatio > 0) {
                    window.dispatchEvent(new Event('resize'))
                }
            });
        }, options);
    
        observer.observe($element);
    },

    render() {
        this.show = true
    },
    
    show: false,
}))