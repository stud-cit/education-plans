import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    return {
        plugins: [
            laravel({
                input: ['resources/js/main.js'],
                refresh: true,
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js/'),
                '@c': path.resolve(__dirname, 'resources/js/components/'),
                'vue': 'vue/dist/vue.esm.js',
            },
            extensions: ['.mjs', '.js', '.ts', '.jsx', '.tsx', '.json', '.vue'],
        },
        define: {
            'process.env': {
                VUE_APP_CABINET_APP_URL: env.VUE_APP_CABINET_APP_URL || env.CABINET_APP_URL,
                VUE_APP_CABINET_APP_TOKEN: env.VUE_APP_CABINET_APP_TOKEN || env.CABINET_APP_TOKEN,
                VUE_APP_CABINET_APP_SERVICE: env.VUE_APP_CABINET_APP_SERVICE || env.CABINET_APP_SERVICE || 'index/service/',
                VUE_APP_NAME_APP: env.VUE_APP_NAME_APP,
                VUE_APP_DEBUG: env.VUE_APP_DEBUG,
                VUE_APP_BY_CREATED_PDF: env.VUE_APP_BY_CREATED_PDF,
                VUE_APP_FEEDBACK: env.VUE_APP_FEEDBACK,
            }
        },
        css: {
            preprocessorOptions: {
                sass: {
                    api: 'legacy',
                },
                scss: {
                    api: 'legacy',
                },
            },
        },
        server: {
            host: '0.0.0.0',
            port: parseInt(env.VITE_PORT) || 5173,
            strictPort: true,
            hmr: {
                host: 'localhost',
                port: parseInt(env.VITE_PORT) || 5173,
            },
            watch: {
                usePolling: true,
            },
        },
    };
});
