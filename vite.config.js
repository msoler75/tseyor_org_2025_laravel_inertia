import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import Components from 'unplugin-vue-components/vite'
import AutoImport from 'unplugin-auto-import/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
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
        Components({
            dts: true,
            dirs: ['resources/js/Components', 'resources/js/Sections', 'resources/js/Layouts'],

         }),
         AutoImport({
            dts: true,
            dirs: ['resources/js/composables'],
            imports:[
                {
                    vue: ['ref', 'computed', 'watch', 'onMounted', 'defineEmits'],
                }
            ]
          })
    ],
});
