import Alpine from 'alpinejs'

import './components/metaboxes/aryel-3d-metabox'
import './components/metaboxes/partials/fields/media'
import './components/metaboxes/partials/fields/preview'
import './components/metaboxes/partials/fields/toggle'
import './components/metaboxes/partials/fields/vector3'

// Init color pickers
jQuery(document).ready(function($) {
    $('.color-picker').wpColorPicker()
})

Alpine.start()