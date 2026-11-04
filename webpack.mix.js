const mix = require('laravel-mix')
const config = require('./webpack.config')

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

mix
    .js('resources/js/app.js', 'public/js')
    .webpackConfig(config)
    .sass('resources/scss/app.scss', 'public/css')
    .copy('resources/assets', 'public/assets')
    .copy('resources/download', 'public/download')
    .options({
        postCss: [require('autoprefixer'), require('postcss-rtl')]
    })
mix.copy('resources/scss/loader.css', 'public/css');

mix.sass('resources/assets/frontend/scss/custom-style.scss', 'public/assets/frontend/css');

if (mix.inProduction()) {
    mix.version()
}
