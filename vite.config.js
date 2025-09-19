import { defineConfig } from "vite";
import tailwindcss from "@tailwindcss/vite";
import viteCompression from "vite-plugin-compression";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import Components from "unplugin-vue-components/vite";
import AutoImport from "unplugin-auto-import/vite";
import { visualizer } from "rollup-plugin-visualizer";
import { VitePWA } from "vite-plugin-pwa";
// import ssr from 'vite-plugin-ssr/plugin'
// import commonjs from 'vite-plugin-commonjs';
import asyncComponentsPlugin from "./vite-plugin-async-components.js";

import path from "path";
// import { fileURLToPath } from 'url';

// const __filename = fileURLToPath(import.meta.url);
// const __dirname = path.dirname(__filename);

export default defineConfig({
  optimizeDeps: {
    // Evitar bundling de estas dependencias
    // exclude: ["@tiptap/starter-kit", "vue-advanced-cropper", "md-editor-v3"],
    include: [
        // Aquí incluye las dependencias que quieres pre-empaquetar
        'vue',
        'vue-router',
        '@inertiajs/vue3/server',
        'axios'
        // ... otras dependencias críticas
    ],
  },
  build: {
    sourcemap: false,
    // minify: "terser", // Menos intensivo que 'esbuild'
    terserOptions: {
      compress: {
        drop_console: true, // Reduce tamaño final
      },
    },
    rollupOptions: {
      output: {
        // format: 'cjs'
      }
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
      ziggy: path.resolve(__dirname, "vendor/tightenco/ziggy/dist/vue.es.js"),
      "@": path.resolve(__dirname, "./resources/js"),
    },
  },
  plugins: [
    tailwindcss(),
    // commonjs(),
    laravel({
      input: [
        "resources/js/app.js",
        "resources/js/backpack/app.js",
        "resources/css/app.css",
        "resources/css/admin.css", // Asegúrate de que admin.css no se importe desde JS
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
          if (name === "ClientOnly") {
            return { name: "default", from: "@duannx/vue-client-only" };
          }
        },
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
            "TransitionGroup",
          ],
          "@inertiajs/vue3": ["router", "usePage", "useForm"],
          "@/Stores/nav.js": ["useNav"],
          "@/Stores/api.js": ["getApiUrl"],
        },
      ],
    }),
    asyncComponentsPlugin(),
    VitePWA({
      registerType: 'autoUpdate',
      filename: 'sw.js',
      manifestFilename: 'pwa-manifest.json',
      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff,woff2,ttf,eot}']
      },
      manifest: {
        name: 'Tseyor.org',
        short_name: 'Tseyor',
        description: 'TSEYOR - Preparándonos para el Salto Cuántico y la creación de las Sociedades Armónicas',
        theme_color: '#1e40af',
        background_color: '#ffffff',
        display: 'standalone',
        orientation: 'portrait-primary',
        scope: '/',
        start_url: '/',
        icons: [
          {
            src: '/ic/android/android-launchericon-48-48.png',
            sizes: '48x48',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/android/android-launchericon-72-72.png',
            sizes: '72x72',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/android/android-launchericon-96-96.png',
            sizes: '96x96',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/ios/128.png',
            sizes: '128x128',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/android/android-launchericon-144-144.png',
            sizes: '144x144',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/ios/152.png',
            sizes: '152x152',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/ios/180.png',
            sizes: '180x180',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-192-192.png',
            sizes: '192x192',
            type: 'image/png',
            purpose: 'maskable any'
          },
          {
            src: '/ic/ios/256.png',
            sizes: '256x256',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-512-512.png',
            sizes: '512x512',
            type: 'image/png',
            purpose: 'maskable any'
          }
        ]
      },
      devOptions: {
        enabled: false
      }
    }),
    viteCompression({
      filter: /bootstrap\/ssr/, // Excluye TODOS los archivos en esta ruta
      threshold: 1024, // Mínimo 1KB para comprimir
      algorithm: "gzip", // Algoritmo principal
      deleteOriginFile: false, // Mantiene los originales
    }),
    visualizer(),
  ],
  define: {
    __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: true,
  },
  ssr: {
    // noExternal: true,
    external: [
      "Modal",
      "Page",
      "Footer",
      "NavAside",
      "ProcesarImagen",
      "TipTapEditor",
      "TipTapFullMenuBar",
    ],
  },
});
