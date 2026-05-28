const path = require('path');

module.exports = {
  pages: {
    index: {
      entry: 'resources/js/main.js',
      template: 'resources/views/spa-template.html',
    }
  },
  outputDir: 'dist',
  filenameHashing: false,
  publicPath: '/',
  transpileDependencies: [
    'vuetify'
  ],
  lintOnSave: false,

  chainWebpack: config => {
    config.performance
      .maxEntrypointSize(10000000)
      .maxAssetSize(10000000)
    // Don't copy files from public/ directory (Laravel's web root)
    config.plugins.delete('copy');
  },

  configureWebpack: {
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'resources/js/'),
        '@c': path.resolve(__dirname, 'resources/js/components/'),
      },
    },
  },

  devServer: {
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
      },
    },
  },
};