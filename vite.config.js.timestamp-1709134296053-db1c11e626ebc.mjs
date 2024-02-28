// vite.config.js
import { defineConfig } from "file:///D:/projects/tseyor/laravel_inertia/node_modules/vite/dist/node/index.js";
import viteCompression from "file:///D:/projects/tseyor/laravel_inertia/node_modules/vite-plugin-compression/dist/index.mjs";
import laravel from "file:///D:/projects/tseyor/laravel_inertia/node_modules/laravel-vite-plugin/dist/index.mjs";
import vue from "file:///D:/projects/tseyor/laravel_inertia/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import Components from "file:///D:/projects/tseyor/laravel_inertia/node_modules/unplugin-vue-components/dist/vite.mjs";
import AutoImport from "file:///D:/projects/tseyor/laravel_inertia/node_modules/unplugin-auto-import/dist/vite.js";
import { visualizer } from "file:///D:/projects/tseyor/laravel_inertia/node_modules/rollup-plugin-visualizer/dist/plugin/index.js";
var vite_config_default = defineConfig({
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
      ziggy: "vendor/tightenco/ziggy/dist/vue.es.js"
    }
  },
  plugins: [
    laravel({
      input: [
        "resources/js/app.js",
        "resources/js/backpack/app.js"
        /*, "resources.js/backpack/components.js"*/
      ],
      // ssr: "resources/js/ssr.js",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    }),
    Components({
      dts: true,
      dirs: [
        "resources/js/Components",
        "resources/js/Components/Comentarios",
        "resources/js/Components/FolderExplorer",
        "resources/js/Icons",
        "resources/js/Sections",
        "resources/js/Layouts"
      ]
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
            "useSlots"
          ],
          "@inertiajs/vue3": ["router", "usePage", "useForm"],
          "@/Stores/nav.js": ["useNav"]
        }
      ]
    }),
    viteCompression(),
    visualizer()
  ]
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxwcm9qZWN0c1xcXFx0c2V5b3JcXFxcbGFyYXZlbF9pbmVydGlhXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJEOlxcXFxwcm9qZWN0c1xcXFx0c2V5b3JcXFxcbGFyYXZlbF9pbmVydGlhXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9EOi9wcm9qZWN0cy90c2V5b3IvbGFyYXZlbF9pbmVydGlhL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSBcInZpdGVcIjtcbmltcG9ydCB2aXRlQ29tcHJlc3Npb24gZnJvbSBcInZpdGUtcGx1Z2luLWNvbXByZXNzaW9uXCI7XG5pbXBvcnQgbGFyYXZlbCBmcm9tIFwibGFyYXZlbC12aXRlLXBsdWdpblwiO1xuaW1wb3J0IHZ1ZSBmcm9tIFwiQHZpdGVqcy9wbHVnaW4tdnVlXCI7XG5pbXBvcnQgQ29tcG9uZW50cyBmcm9tIFwidW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZVwiO1xuaW1wb3J0IEF1dG9JbXBvcnQgZnJvbSBcInVucGx1Z2luLWF1dG8taW1wb3J0L3ZpdGVcIjtcbmltcG9ydCB7IHZpc3VhbGl6ZXIgfSBmcm9tIFwicm9sbHVwLXBsdWdpbi12aXN1YWxpemVyXCI7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gIC8qIGJ1aWxkOiB7XG4gICAgb3V0RGlyOiAnZGlzdCcsXG4gICAgcm9sbHVwT3B0aW9uczoge1xuICAgICAvLyBEZXNoYWJpbGl0YW1vcyBpbmxpbmVEeW5hbWljSW1wb3J0cyBwYXJhIGV2aXRhciBlc3RlIGVycm9yXG4gICAgIGlubGluZUR5bmFtaWNJbXBvcnRzOiBmYWxzZVxuICAgIH0sXG4gICAgbGliOiB7XG4gICAgICAgIGVudHJ5OiBcIi4vcmVzb3VyY2VzL2pzL2JhY2twYWNrL2luZGV4LmpzXCIsXG4gICAgICBmb3JtYXRzOiBbJ2VzJywgJ3VtZCddLFxuICAgICAgbmFtZTogJ2NvbXBvbmVudHMnXG4gICAgfVxuICB9LCAqL1xuICByZXNvbHZlOiB7XG4gICAgYWxpYXM6IHtcbiAgICAgICAgemlnZ3k6ICd2ZW5kb3IvdGlnaHRlbmNvL3ppZ2d5L2Rpc3QvdnVlLmVzLmpzJ1xuICAgIH0sXG59LFxuICBwbHVnaW5zOiBbXG4gICAgbGFyYXZlbCh7XG4gICAgICBpbnB1dDogW1wicmVzb3VyY2VzL2pzL2FwcC5qc1wiLCBcInJlc291cmNlcy9qcy9iYWNrcGFjay9hcHAuanNcIi8qLCBcInJlc291cmNlcy5qcy9iYWNrcGFjay9jb21wb25lbnRzLmpzXCIqL10sXG4gICAgICAvLyBzc3I6IFwicmVzb3VyY2VzL2pzL3Nzci5qc1wiLFxuICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICB9KSxcbiAgICB2dWUoe1xuICAgICAgdGVtcGxhdGU6IHtcbiAgICAgICAgdHJhbnNmb3JtQXNzZXRVcmxzOiB7XG4gICAgICAgICAgYmFzZTogbnVsbCxcbiAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICB9LFxuICAgICAgfSxcbiAgICB9KSxcbiAgICBDb21wb25lbnRzKHtcbiAgICAgIGR0czogdHJ1ZSxcbiAgICAgIGRpcnM6IFtcbiAgICAgICAgXCJyZXNvdXJjZXMvanMvQ29tcG9uZW50c1wiLFxuICAgICAgICBcInJlc291cmNlcy9qcy9Db21wb25lbnRzL0NvbWVudGFyaW9zXCIsXG4gICAgICAgIFwicmVzb3VyY2VzL2pzL0NvbXBvbmVudHMvRm9sZGVyRXhwbG9yZXJcIixcbiAgICAgICAgXCJyZXNvdXJjZXMvanMvSWNvbnNcIixcbiAgICAgICAgXCJyZXNvdXJjZXMvanMvU2VjdGlvbnNcIixcbiAgICAgICAgXCJyZXNvdXJjZXMvanMvTGF5b3V0c1wiLFxuICAgICAgXSxcbiAgICB9KSxcbiAgICBBdXRvSW1wb3J0KHtcbiAgICAgIGR0czogdHJ1ZSxcbiAgICAgIGRpcnM6IFtcInJlc291cmNlcy9qcy9jb21wb3NhYmxlc1wiXSxcbiAgICAgIGltcG9ydHM6IFtcbiAgICAgICAge1xuICAgICAgICAgIHZ1ZTogW1xuICAgICAgICAgICAgXCJyZWZcIixcbiAgICAgICAgICAgIFwicmVhY3RpdmVcIixcbiAgICAgICAgICAgIFwibmV4dFRpY2tcIixcbiAgICAgICAgICAgIFwiY29tcHV0ZWRcIixcbiAgICAgICAgICAgIFwid2F0Y2hcIixcbiAgICAgICAgICAgIFwib25Nb3VudGVkXCIsXG4gICAgICAgICAgICBcIm9uQmVmb3JlVW5tb3VudFwiLFxuICAgICAgICAgICAgXCJvblVubW91bnRlZFwiLFxuICAgICAgICAgICAgXCJkZWZpbmVFbWl0c1wiLFxuICAgICAgICAgICAgXCJ1c2VTbG90c1wiLFxuICAgICAgICAgIF0sXG4gICAgICAgICAgXCJAaW5lcnRpYWpzL3Z1ZTNcIjogW1wicm91dGVyXCIsIFwidXNlUGFnZVwiLCBcInVzZUZvcm1cIl0sXG4gICAgICAgICAgXCJAL1N0b3Jlcy9uYXYuanNcIjogW1widXNlTmF2XCJdXG4gICAgICAgIH0sXG4gICAgICBdLFxuICAgIH0pLFxuICAgIHZpdGVDb21wcmVzc2lvbigpLFxuICAgIHZpc3VhbGl6ZXIoKSxcbiAgXSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFnUyxTQUFTLG9CQUFvQjtBQUM3VCxPQUFPLHFCQUFxQjtBQUM1QixPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sZ0JBQWdCO0FBQ3ZCLFNBQVMsa0JBQWtCO0FBRTNCLElBQU8sc0JBQVEsYUFBYTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBLEVBYTFCLFNBQVM7QUFBQSxJQUNQLE9BQU87QUFBQSxNQUNILE9BQU87QUFBQSxJQUNYO0FBQUEsRUFDSjtBQUFBLEVBQ0UsU0FBUztBQUFBLElBQ1AsUUFBUTtBQUFBLE1BQ04sT0FBTztBQUFBLFFBQUM7QUFBQSxRQUF1QjtBQUFBO0FBQUEsTUFBeUU7QUFBQTtBQUFBLE1BRXhHLFNBQVM7QUFBQSxJQUNYLENBQUM7QUFBQSxJQUNELElBQUk7QUFBQSxNQUNGLFVBQVU7QUFBQSxRQUNSLG9CQUFvQjtBQUFBLFVBQ2xCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ25CO0FBQUEsTUFDRjtBQUFBLElBQ0YsQ0FBQztBQUFBLElBQ0QsV0FBVztBQUFBLE1BQ1QsS0FBSztBQUFBLE1BQ0wsTUFBTTtBQUFBLFFBQ0o7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNULEtBQUs7QUFBQSxNQUNMLE1BQU0sQ0FBQywwQkFBMEI7QUFBQSxNQUNqQyxTQUFTO0FBQUEsUUFDUDtBQUFBLFVBQ0UsS0FBSztBQUFBLFlBQ0g7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxVQUNGO0FBQUEsVUFDQSxtQkFBbUIsQ0FBQyxVQUFVLFdBQVcsU0FBUztBQUFBLFVBQ2xELG1CQUFtQixDQUFDLFFBQVE7QUFBQSxRQUM5QjtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELGdCQUFnQjtBQUFBLElBQ2hCLFdBQVc7QUFBQSxFQUNiO0FBQ0YsQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
