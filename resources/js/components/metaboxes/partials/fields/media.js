import Alpine from 'alpinejs'

Alpine.data('MediaState', () => ({
    init() {
        this.imageId = this.$el.dataset.imageId
        this.$input = this.$el.querySelector('[type="hidden"]')
        this.$button = this.$el.querySelector('button')

        this.initButtonClicklistener()
        this.initObserver()
    },

    initButtonClicklistener() {
        const vm = this

        jQuery(this.$button).click(function (e) {
            e.preventDefault()

            let image_frame;
            if (image_frame) {
                image_frame.open();
            }

            // Define image_frame as wp.media object
            image_frame = wp.media({
                title: 'Select environment',
                multiple : false,
                library : {
                    type : 'application/octet-stream',
                }
            });

            image_frame.on('close', function () {
                // On close, get selections and save to the hidden input
                // plus other AJAX stuff to refresh the image preview
                let selection = image_frame.state().get('selection')
                let ids = []
                selection.each(function(attachment) {
                    ids.push(attachment['id'])
                });

                if (ids.length === 0) {
                    return
                }

                vm.imageId = ids[0]
            });

            image_frame.on('open', function () {
                // On open, get the id from the hidden input
                // and select the appropiate images in the media manager
                let selection = image_frame.state().get('selection');
                
                let attachment = wp.media.attachment(vm.imageId);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });

            image_frame.open();
        });
    },

    initObserver() {
        this.$watch('imageId', () => {
            this.$input.dispatchEvent(new Event('change'))
        })
    },

    removeImage() {
        this.imageId = null
    },

    $input: null,
    $button: null,
    imageId: null,
}))