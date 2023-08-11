const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/bootstrap.js', 'public/js')
    .sass('resources/css/app.scss', 'public/css')
    .sass('resources/sass/app2.scss', 'public/css')
    .sass('resources/css/liqcalc.scss', 'public/css')
    .copyDirectory('vendor/fa6', 'public/fa6')
    .setPublicPath('public')
    .webpackConfig({
        resolve: {
            extensions: ['.js', '.jsx', '.json']
        }
    });