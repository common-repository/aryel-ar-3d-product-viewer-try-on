const mix = require('laravel-mix');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/admin.js', 'js/admin.js')
    .sass('resources/scss/admin.scss', 'css/admin.css')

    .js('resources/js/front.js', 'js/front.js')
    .sass('resources/scss/front.scss', 'css/front.css')

    .setPublicPath('dist')
