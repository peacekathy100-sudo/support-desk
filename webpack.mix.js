const mix = require('laravel-mix');
const lodash = require("lodash");

const folder = {
    src: "resources/",
    dist: "public/",
    dist_assets: "public/assets/"
};

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/chat.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
   ]);

// SCSS compilation
mix.sass('resources/scss/layouts/layout_2/compile/all.scss', folder.dist_assets + "css")
   .minify(folder.dist_assets + "css/all.css");

mix.sass('resources/scss/layouts/layout_2/compile/bootstrap.scss', folder.dist_assets + "css")
   .minify(folder.dist_assets + "css/bootstrap.css");

mix.sass('resources/scss/layouts/layout_2/compile/components.scss', folder.dist_assets + "css")
   .options({ processCssUrls: false })
   .minify(folder.dist_assets + "css/components.css");

mix.sass('resources/scss/layouts/layout_2/compile/layout.scss', folder.dist_assets + "css")
   .options({ processCssUrls: false })
   .minify(folder.dist_assets + "css/layout.css");

// JS + bundle
mix.styles([
       'public/assets/css/all.min.css',
       'public/assets/css/layout.min.css',
       'public/assets/css/components.min.css'
   ], 'public/css/limitless-bundle.css');

// Performance optimizations
mix.disableNotifications();
mix.webpackConfig({
    stats: 'none',
    performance: {
        maxEntrypointSize: 512000,
        maxAssetSize: 512000
    },
    optimization: {
        minimize: true,
        splitChunks: {
            chunks: 'all',
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name: 'vendors',
                    priority: 10,
                    reuseExistingChunk: true,
                },
            },
        },
    }
});

// RTL FIX (MODERN WAY)
mix.options({
    postCss: [
        require('postcss-rtlcss')
    ]
});

// Enable source maps in development only
if (!mix.inProduction()) {
    mix.sourceMaps();
}

