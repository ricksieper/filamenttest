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

mix.js('resources/js/filament-multi-side-select.js', 'resources/dist/js')
    .js('resources/js/components/multi-side-select.js', 'resources/dist/components')
    .js('resources/js/utils/debounce.js', 'resources/dist/utils')
    .postCss('resources/css/filament-multi-side-select.css', 'resources/dist/css')
    .disableNotifications();

// If in production mode, version the files
if (mix.inProduction()) {
    mix.version();
}