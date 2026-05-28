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

    // Inject backend env vars into frontend as VUE_APP_* (avoids duplicating in .env)
    config.plugin('define').tap(args => {
      const env = args[0]['process.env'];
      env.VUE_APP_CABINET_APP_URL = JSON.stringify(process.env.CABINET_APP_URL);
      env.VUE_APP_CABINET_APP_TOKEN = JSON.stringify(process.env.CABINET_APP_TOKEN);
      env.VUE_APP_CABINET_APP_SERVICE = JSON.stringify(process.env.CABINET_APP_SERVICE || 'index/service/');
      return args;
    });
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