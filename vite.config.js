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

import path from "path";
// import { fileURLToPath } from 'url';

// const __filename = fileURLToPath(import.meta.url);
// const __dirname = path.dirname(__filename);

// Detectar si es build SSR
const isSSR = process.argv.includes('--ssr');

export default defineConfig({
  optimizeDeps: {
    // Excluir @tiptap de optimización
    exclude: ['@tiptap/*', 'prosemirror-*'],
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
      external: isSSR ? (id) => {
        // Solo marcar @tiptap y prosemirror como externos en SSR
        return id.includes('@tiptap/') || id.includes('prosemirror-');
      } : undefined,
      output: isSSR ? {} : {
        manualChunks(id) {
          // ESTRATEGIA SIMPLIFICADA Y ROBUSTA
          // Solo separamos lo que realmente es pesado y no tiene dependencias circulares

          if (id.includes('node_modules')) {
            // VUE ECOSYSTEM: Todo junto para evitar dependencias circulares
            // Incluye: vue, @vue/*, @inertiajs, vue-router, @vueuse, @iconify/vue, etc.
            if (id.includes('vue') || id.includes('@vue/') ||
                id.includes('@inertiajs/') || id.includes('@vueuse/') ||
                id.includes('@iconify/vue')) {
              return null;
            }

            // TIPTAP: Editor rico (pesado, ~300KB) - solo en build cliente
            if (!isSSR && id.includes('@tiptap/')) {
              return 'vendor-tiptap';
            }

            // MD-EDITOR: Editor markdown (pesado, ~200KB)
            if (id.includes('md-editor-v3')) {
              return 'vendor-markdown';
            }

            // RESTO: axios y otras libs pequeñas
            return null;
          }

          // COMPONENTES DE LA APP - Agrupación estratégica por funcionalidad
          if (id.includes('/resources/js/')) {

            // 1. LAYOUT CORE: Componentes de estructura de página (carga temprana)
            // Agrupa: Page*, Header, Footer, AppLayout (total ~2-3 KB)
            if (
                id.includes('LazyHydrationWrapper') ||
                id.includes('/Components/Header.vue') ||
                id.includes('/Components/Nav') ||
                id.includes('/Components/GlobalSearch.vue') ||
                id.includes('/Components/Link') ||
                // id.includes('Icon') ||
                id.includes('/Components/Image') ||
                id.includes('/Components/DropDown') ||
                id.includes('/Layouts/AppLayout.vue')) {
              return 'layout-core';
            }


            if(id.includes('/Components/Page')||
               id.includes('/Sections/Section.vue') ||
               id.includes('/Sections/FullPage.vue') ||
                id.includes('/Sections/Sections.vue')
            ) {
                return 'components-pack'
            }

            /*if(
               id.includes('/Sections/Hero')||
               id.includes('/Sections/TextText') ||
               id.includes('/Sections/TextImage')
            ) {
                return 'componentes-hero'
            }*/

            /* if(
               id.includes('/Components/Card')
            ) {
                return 'componentes-card'
            }*/

/*
             if(id.includes('/Components/TextText')||

            ) {
                return 'componentes-hero'
            }
                */

            // 2. UI FORMS: Inputs, Buttons, Checkboxes (carga temprana)
            // Agrupa todos los controles de formulario (total ~5-7 KB)
            /*if (id.includes('/Components/Input') ||
                id.includes('/Components/TextInput.vue') ||
                id.includes('/Components/TextArea.vue') ||
                id.includes('/Components/PasswordInput.vue') ||
                id.includes('/Components/PrimaryButton.vue') ||
                id.includes('/Components/SecondaryButton.vue') ||
                id.includes('/Components/DangerButton.vue') ||
                id.includes('/Components/Checkbox.vue') ||
                id.includes('/Components/InputLabel.vue')) {
              return 'components-forms';
            }*/

            return null;

          }

          return null;
        }
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
      // Usar stub de TipTapEditor en SSR
      ...(isSSR ? {
        "@/Components/TipTapEditor.vue": path.resolve(__dirname, "./resources/js/Components/TipTapEditor.ssr.vue"),
        "@/Components/TipTapFullMenuBar.vue": path.resolve(__dirname, "./resources/js/Components/TipTapEditor.ssr.vue"),
        "@/Components/TipTapSimpleMenuBar.vue": path.resolve(__dirname, "./resources/js/Components/TipTapEditor.ssr.vue"),
      } : {}),
    },
  },
  plugins: [
    tailwindcss(),
    // Plugin para reemplazar TipTap con stub en SSR
    isSSR && {
      name: 'replace-tiptap-in-ssr',
      enforce: 'pre',
      resolveId(id, importer) {
        // Reemplazar imports de TipTap con el stub
        if (id.includes('TipTapEditor.vue') ||
            id.includes('TipTapFullMenuBar.vue') ||
            id.includes('TipTapSimpleMenuBar.vue')) {
          return path.resolve(__dirname, './resources/js/Components/TipTapEditor.ssr.vue');
        }
        // Reemplazar imports relativos de TipTap
        if (importer && id.startsWith('./TipTap') && id.endsWith('.vue')) {
          return path.resolve(__dirname, './resources/js/Components/TipTapEditor.ssr.vue');
        }
      }
    },
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
      // Filtrar componentes TipTap durante build SSR
      exclude: isSSR ? [/TipTap.*\.vue$/] : [],
      // Añade esta sección para resolver componentes
      resolvers: [
        (name) => {
          if (name === "ClientOnly") {
            return { name: "default", from: "@duannx/vue-client-only" };
          }
          // Reemplazar TipTap con stub en SSR
          if (isSSR && name.startsWith('TipTap')) {
            return {
              name: "default",
              from: path.resolve(__dirname, "./resources/js/Components/TipTapEditor.ssr.vue")
            };
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
            "unref",
            "reactive",
            "nextTick",
            "computed",
            "watch",
            "onMounted",
            "onBeforeUnmount",
            "defineAsyncComponent",
            "onUnmounted",
            "defineEmits",
            "useSlots",
            "TransitionGroup",
            "provide",
            "inject",
          ],
          "@inertiajs/vue3": ["router", "usePage", "useForm"],
          "@/Stores/nav.js": [["default", "useNav"]],
          "@/Stores/ui.js": [["default", "useUi"]],
        },
      ],
    }),
    // Deshabilita VitePWA en build SSR para evitar duplicados
    isSSR ? null : VitePWA({
      registerType: 'autoUpdate',
      filename: 'tseyor-sw.js',
  // Cambiado a nueva versión para forzar invalidación de caché del manifest
  manifestFilename: 'tseyor-manifest.v2.json',
      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff,woff2,ttf,eot}'],
        // Configuración específica para Laravel/Inertia.js SPA
        navigateFallback: null, // Deshabilita el fallback automático a index.html
        additionalManifestEntries: [
          { url: '/', revision: '1' }
        ],
        runtimeCaching: [
          {
            // Cache prioritario para la página principal - usar cache primero
            urlPattern: ({ url }) => url.pathname === '/',
            handler: 'CacheFirst',
            options: {
              cacheName: 'homepage-cache',
              expiration: {
                maxEntries: 1, // Solo la página principal
                maxAgeSeconds: 24 * 60 * 60, // 24 horas
              },
            },
          },
          {
            // Cache de navegación (otras rutas de tu app)
            urlPattern: ({ request }) => request.mode === 'navigate',
            handler: 'NetworkFirst',
            options: {
              cacheName: 'pages-cache',
              networkTimeoutSeconds: 10,
              expiration: {
                maxEntries: 50,
                maxAgeSeconds: 24 * 60 * 60, // 24 horas
              },
            },
          },
          {
            // Cache de assets estáticos (CSS, JS)
            urlPattern: /\.(?:js|css)$/,
            handler: 'StaleWhileRevalidate',
            options: {
              cacheName: 'assets-cache',
              expiration: {
                maxEntries: 100,
                maxAgeSeconds: 7 * 24 * 60 * 60, // 7 días
              },
            },
          },
          {
            // Cache de imágenes
            urlPattern: /\.(?:png|jpg|jpeg|svg|gif|webp|ico)$/,
            handler: 'CacheFirst',
            options: {
              cacheName: 'images-cache',
              expiration: {
                maxEntries: 200,
                maxAgeSeconds: 30 * 24 * 60 * 60, // 30 días
              },
            },
          },
          {
            // Cache de fuentes
            urlPattern: /\.(?:woff|woff2|ttf|eot)$/,
            handler: 'CacheFirst',
            options: {
              cacheName: 'fonts-cache',
              expiration: {
                maxEntries: 50,
                maxAgeSeconds: 365 * 24 * 60 * 60, // 1 año
              },
            },
          },
        ],
      },
      manifest: {
        name: 'Tseyor.org',
        short_name: 'Tseyor',
        id: 'org.tseyor.main',
        description: 'TSEYOR - Preparándonos para el Salto Cuántico y la creación de las Sociedades Armónicas',
        theme_color: '#60a5fa',
        background_color: '#0a2245',
        display: 'standalone',
        orientation: 'portrait-primary',
        scope: '/',
        start_url: '/novedades',
        icons: [
          // Iconos para Android
          {
            src: '/ic/android/android-launchericon-48-48.png',
            sizes: '48x48',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-72-72.png',
            sizes: '72x72',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-96-96.png',
            sizes: '96x96',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-144-144.png',
            sizes: '144x144',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-192-192.png',
            sizes: '192x192',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/android/android-launchericon-512-512.png',
            sizes: '512x512',
            type: 'image/png',
            purpose: 'any'
          },
          // Iconos para iOS
          {
            src: '/ic/ios/128.png',
            sizes: '128x128',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/ios/152.png',
            sizes: '152x152',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/ios/180.png',
            sizes: '180x180',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/ios/256.png',
            sizes: '256x256',
            type: 'image/png',
            purpose: 'any'
          },
          // Iconos específicos para Windows (tamaños targetsize - más importantes para splash screen)
          {
            src: '/ic/windows11/Square44x44Logo.targetsize-44.png',
            sizes: '44x44',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/Square44x44Logo.targetsize-48.png',
            sizes: '48x48',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/Square150x150Logo.scale-100.png',
            sizes: '150x150',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/Square150x150Logo.scale-200.png',
            sizes: '300x300',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/Wide310x150Logo.scale-100.png',
            sizes: '310x150',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/LargeTile.scale-100.png',
            sizes: '310x310',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/LargeTile.scale-200.png',
            sizes: '620x620',
            type: 'image/png',
            purpose: 'any'
          },
          // Iconos adicionales para mejor compatibilidad
          {
            src: '/ic/windows11/Square44x44Logo.scale-200.png',
            sizes: '88x88',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/Square44x44Logo.scale-400.png',
            sizes: '176x176',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/Square150x150Logo.scale-400.png',
            sizes: '600x600',
            type: 'image/png',
            purpose: 'any'
          },
          {
            src: '/ic/windows11/LargeTile.scale-400.png',
            sizes: '1240x1240',
            type: 'image/png',
            purpose: 'any'
          },
          // Icono principal (importante para splash screen)
          {
            src: '/ic/android/android-launchericon-512-512.png',
            sizes: '512x512',
            type: 'image/png',
            purpose: 'any'
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
  ].filter(Boolean),
  define: {
    __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: true,
  },
  ssr: {
    noExternal: [
      // Removido @tiptap/* y prosemirror-* ya que están en ClientOnly
    ],
    external: [
      '@tiptap/*',
      'prosemirror-*',
        // 'PWANotifications',
        /*'ImagesViewer',
        'ModalDropZone',
        'AudioVideoPlayer',
        'ShareNetwork',
        'Tools',
        'ToolTextSearch',
        'ToolTip',
        'TipTapSimpleMenuBar',
        'AdminLinks',
        'AudioStateIcon',
        'ProcesarImagen',
        'TipTapEditor',
        'TipTapFullMenuBar',*/
    ],
  },
});

