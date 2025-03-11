import { defineConfig } from "vite";
import viteCompression from "vite-plugin-compression";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";
import AutoImport from "unplugin-auto-import/vite";
import { visualizer } from "rollup-plugin-visualizer";
// import ssr from 'vite-plugin-ssr/plugin'
import asyncComponentsPlugin from './vite-plugin-async-components.js';



import path from 'path';
// import { fileURLToPath } from 'url';

// const __filename = fileURLToPath(import.meta.url);
// const __dirname = path.dirname(__filename);



export default defineConfig({
  build : {
    rollupOptions: {
      // maxParallelFileOps: 2 // un intento de que funcione en dreamhost
    }
  },
  /* build: {
    outDir: 'dist',
    rollupOptions: {
     // Deshabilitamos inlineDynamicImports para evitar este error
     inlineDynamicImports: false
    },
    lib: {
        entry: "./resources/js/backpack/index.js",
      formats: ['es', 'umd'],
      name: 'components'
    }
  }, */
  resolve: {
    alias: {
        ziggy: path.resolve(__dirname, 'vendor/tightenco/ziggy/dist/vue.es.js'),
        '@': path.resolve(__dirname, './resources/js')
    },
  },
  plugins: [
    laravel({
      input: [
        "resources/js/app.js",
        "resources/js/backpack/app.js",
        "resources/css/admin.css",
      ],
      ssr: "resources/js/ssr.js",
      refresh: true,
    }),
    {
      name: "ziggy",
      enforce: "post",
      handleHotUpdate({ server, file }) {
        if (file.includes("/routes/") && file.endsWith(".php")) {
          exec(
            "php artisan ziggy:generate",
            (error, stdout) =>
              error === null && console.log(`  > Ziggy routes generated!`)
          );
        }
      },
    },
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
      dirs: [
        "resources/js/Components",
        "resources/js/Components/Comentarios",
        "resources/js/Components/FolderExplorer",
        "resources/js/Icons",
        "resources/js/Sections",
        "resources/js/Layouts",
      ],
      // Añade esta sección para resolver componentes
      resolvers: [
        (name) => {
          if (name === 'ClientOnly') {
            return { name: 'default', from: '@duannx/vue-client-only' }
          }
        }
      ],
    }),
    AutoImport({
      dts: true,
      dirs: ["resources/js/composables"],
      imports: [
        {
          vue: [
            "ref",
            "reactive",
            "nextTick",
            "computed",
            "watch",
            "onMounted",
            "onBeforeUnmount",
            "onUnmounted",
            "defineEmits",
            "useSlots",
            "defineAsyncComponent",
            "TransitionGroup"
          ],
          "@inertiajs/vue3": ["router", "usePage", "useForm",],
          "@/Stores/nav.js":  ["useNav"],
        },
      ],
    }),
    asyncComponentsPlugin(),
    viteCompression({
        filter: /bootstrap\/ssr/, // Excluye TODOS los archivos en esta ruta
        threshold: 1024, // Mínimo 1KB para comprimir
        algorithm: 'gzip', // Algoritmo principal
        deleteOriginFile: false // Mantiene los originales
      }),
    visualizer(),
  ],
  define: {
    __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: true
  },
  ssr: {
    external : ['Modal', 'Page', 'Footer', 'NavAside', 'ProcesarImagen','TipTapEditor', 'TipTapFullMenuBar']
  },
});
