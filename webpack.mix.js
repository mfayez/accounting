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

mix.js('resources/js/app.js', 'public/js').vue()
  .postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
  ])

  .sass('resources/scss/pdf/pdf.scss', 'public/css/pdf')

  .sass('resources/scss/pdf/ar.scss', 'public/css/pdf')

  .sass('resources/scss/pdf/en.scss', 'public/css/pdf')
  .webpackConfig(require('./webpack.config'));

if (mix.inProduction()) {
  mix.version();
}

/*
mix.webpackConfig ({
  plugins: [
    new webpack.DefinePlugin({
      __VUE_OPTIONS_API__: true,
      __VUE_PROD_DEVTOOLS__: true,
    }),
  ],
})
*/
