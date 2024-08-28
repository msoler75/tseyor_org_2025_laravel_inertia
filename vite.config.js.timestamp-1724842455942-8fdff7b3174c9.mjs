// vite.config.js
import { defineConfig } from "file:///D:/projects/tseyor/laravel_inertia/node_modules/vite/dist/node/index.js";
import viteCompression from "file:///D:/projects/tseyor/laravel_inertia/node_modules/vite-plugin-compression/dist/index.mjs";
import laravel from "file:///D:/projects/tseyor/laravel_inertia/node_modules/laravel-vite-plugin/dist/index.mjs";
import vue from "file:///D:/projects/tseyor/laravel_inertia/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import Components from "file:///D:/projects/tseyor/laravel_inertia/node_modules/unplugin-vue-components/dist/vite.mjs";
import AutoImport from "file:///D:/projects/tseyor/laravel_inertia/node_modules/unplugin-auto-import/dist/vite.js";
import { visualizer } from "file:///D:/projects/tseyor/laravel_inertia/node_modules/rollup-plugin-visualizer/dist/plugin/index.js";
var vite_config_default = defineConfig({
  build: {
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
      ziggy: "vendor/tightenco/ziggy/dist/vue.es.js"
    }
  },
  plugins: [
    laravel({
      input: [
        "resources/js/app.js",
        "resources/js/backpack/app.js",
        "resources/css/admin.css"
      ],
      // ssr: "resources/js/ssr.js",
      refresh: true
    }),
    {
      name: "ziggy",
      enforce: "post",
      handleHotUpdate({ server, file }) {
        if (file.includes("/routes/") && file.endsWith(".php")) {
          exec(
            "php artisan ziggy:generate",
            (error, stdout) => error === null && console.log(`  > Ziggy routes generated!`)
          );
        }
      }
    },
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
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJEOlxcXFxwcm9qZWN0c1xcXFx0c2V5b3JcXFxcbGFyYXZlbF9pbmVydGlhXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJEOlxcXFxwcm9qZWN0c1xcXFx0c2V5b3JcXFxcbGFyYXZlbF9pbmVydGlhXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9EOi9wcm9qZWN0cy90c2V5b3IvbGFyYXZlbF9pbmVydGlhL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHsgZGVmaW5lQ29uZmlnIH0gZnJvbSBcInZpdGVcIjtcbmltcG9ydCB2aXRlQ29tcHJlc3Npb24gZnJvbSBcInZpdGUtcGx1Z2luLWNvbXByZXNzaW9uXCI7XG5pbXBvcnQgbGFyYXZlbCBmcm9tIFwibGFyYXZlbC12aXRlLXBsdWdpblwiO1xuaW1wb3J0IHZ1ZSBmcm9tIFwiQHZpdGVqcy9wbHVnaW4tdnVlXCI7XG5pbXBvcnQgQ29tcG9uZW50cyBmcm9tIFwidW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvdml0ZVwiO1xuaW1wb3J0IEF1dG9JbXBvcnQgZnJvbSBcInVucGx1Z2luLWF1dG8taW1wb3J0L3ZpdGVcIjtcbmltcG9ydCB7IHZpc3VhbGl6ZXIgfSBmcm9tIFwicm9sbHVwLXBsdWdpbi12aXN1YWxpemVyXCI7XG5cbmV4cG9ydCBkZWZhdWx0IGRlZmluZUNvbmZpZyh7XG4gIGJ1aWxkIDoge1xuICAgIHJvbGx1cE9wdGlvbnM6IHtcbiAgICAgIC8vIG1heFBhcmFsbGVsRmlsZU9wczogMiAvLyB1biBpbnRlbnRvIGRlIHF1ZSBmdW5jaW9uZSBlbiBkcmVhbWhvc3RcbiAgICB9XG4gIH0sXG4gIC8qIGJ1aWxkOiB7XG4gICAgb3V0RGlyOiAnZGlzdCcsXG4gICAgcm9sbHVwT3B0aW9uczoge1xuICAgICAvLyBEZXNoYWJpbGl0YW1vcyBpbmxpbmVEeW5hbWljSW1wb3J0cyBwYXJhIGV2aXRhciBlc3RlIGVycm9yXG4gICAgIGlubGluZUR5bmFtaWNJbXBvcnRzOiBmYWxzZVxuICAgIH0sXG4gICAgbGliOiB7XG4gICAgICAgIGVudHJ5OiBcIi4vcmVzb3VyY2VzL2pzL2JhY2twYWNrL2luZGV4LmpzXCIsXG4gICAgICBmb3JtYXRzOiBbJ2VzJywgJ3VtZCddLFxuICAgICAgbmFtZTogJ2NvbXBvbmVudHMnXG4gICAgfVxuICB9LCAqL1xuICByZXNvbHZlOiB7XG4gICAgYWxpYXM6IHtcbiAgICAgICAgemlnZ3k6ICd2ZW5kb3IvdGlnaHRlbmNvL3ppZ2d5L2Rpc3QvdnVlLmVzLmpzJ1xuICAgIH0sXG4gIH0sXG4gIHBsdWdpbnM6IFtcbiAgICBsYXJhdmVsKHtcbiAgICAgIGlucHV0OiBbXG4gICAgICAgIFwicmVzb3VyY2VzL2pzL2FwcC5qc1wiLFxuICAgICAgICBcInJlc291cmNlcy9qcy9iYWNrcGFjay9hcHAuanNcIixcbiAgICAgICAgXCJyZXNvdXJjZXMvY3NzL2FkbWluLmNzc1wiLFxuICAgICAgXSxcbiAgICAgIC8vIHNzcjogXCJyZXNvdXJjZXMvanMvc3NyLmpzXCIsXG4gICAgICByZWZyZXNoOiB0cnVlLFxuICAgIH0pLFxuICAgIHtcbiAgICAgIG5hbWU6IFwiemlnZ3lcIixcbiAgICAgIGVuZm9yY2U6IFwicG9zdFwiLFxuICAgICAgaGFuZGxlSG90VXBkYXRlKHsgc2VydmVyLCBmaWxlIH0pIHtcbiAgICAgICAgaWYgKGZpbGUuaW5jbHVkZXMoXCIvcm91dGVzL1wiKSAmJiBmaWxlLmVuZHNXaXRoKFwiLnBocFwiKSkge1xuICAgICAgICAgIGV4ZWMoXG4gICAgICAgICAgICBcInBocCBhcnRpc2FuIHppZ2d5OmdlbmVyYXRlXCIsXG4gICAgICAgICAgICAoZXJyb3IsIHN0ZG91dCkgPT5cbiAgICAgICAgICAgICAgZXJyb3IgPT09IG51bGwgJiYgY29uc29sZS5sb2coYCAgPiBaaWdneSByb3V0ZXMgZ2VuZXJhdGVkIWApXG4gICAgICAgICAgKTtcbiAgICAgICAgfVxuICAgICAgfSxcbiAgICB9LFxuICAgIHZ1ZSh7XG4gICAgICB0ZW1wbGF0ZToge1xuICAgICAgICB0cmFuc2Zvcm1Bc3NldFVybHM6IHtcbiAgICAgICAgICBiYXNlOiBudWxsLFxuICAgICAgICAgIGluY2x1ZGVBYnNvbHV0ZTogZmFsc2UsXG4gICAgICAgIH0sXG4gICAgICB9LFxuICAgIH0pLFxuICAgIENvbXBvbmVudHMoe1xuICAgICAgZHRzOiB0cnVlLFxuICAgICAgZGlyczogW1xuICAgICAgICBcInJlc291cmNlcy9qcy9Db21wb25lbnRzXCIsXG4gICAgICAgIFwicmVzb3VyY2VzL2pzL0NvbXBvbmVudHMvQ29tZW50YXJpb3NcIixcbiAgICAgICAgXCJyZXNvdXJjZXMvanMvQ29tcG9uZW50cy9Gb2xkZXJFeHBsb3JlclwiLFxuICAgICAgICBcInJlc291cmNlcy9qcy9JY29uc1wiLFxuICAgICAgICBcInJlc291cmNlcy9qcy9TZWN0aW9uc1wiLFxuICAgICAgICBcInJlc291cmNlcy9qcy9MYXlvdXRzXCIsXG4gICAgICBdLFxuICAgIH0pLFxuICAgIEF1dG9JbXBvcnQoe1xuICAgICAgZHRzOiB0cnVlLFxuICAgICAgZGlyczogW1wicmVzb3VyY2VzL2pzL2NvbXBvc2FibGVzXCJdLFxuICAgICAgaW1wb3J0czogW1xuICAgICAgICB7XG4gICAgICAgICAgdnVlOiBbXG4gICAgICAgICAgICBcInJlZlwiLFxuICAgICAgICAgICAgXCJyZWFjdGl2ZVwiLFxuICAgICAgICAgICAgXCJuZXh0VGlja1wiLFxuICAgICAgICAgICAgXCJjb21wdXRlZFwiLFxuICAgICAgICAgICAgXCJ3YXRjaFwiLFxuICAgICAgICAgICAgXCJvbk1vdW50ZWRcIixcbiAgICAgICAgICAgIFwib25CZWZvcmVVbm1vdW50XCIsXG4gICAgICAgICAgICBcIm9uVW5tb3VudGVkXCIsXG4gICAgICAgICAgICBcImRlZmluZUVtaXRzXCIsXG4gICAgICAgICAgICBcInVzZVNsb3RzXCIsXG4gICAgICAgICAgXSxcbiAgICAgICAgICBcIkBpbmVydGlhanMvdnVlM1wiOiBbXCJyb3V0ZXJcIiwgXCJ1c2VQYWdlXCIsIFwidXNlRm9ybVwiXSxcbiAgICAgICAgICBcIkAvU3RvcmVzL25hdi5qc1wiOiAgW1widXNlTmF2XCJdXG4gICAgICAgIH0sXG4gICAgICBdLFxuICAgIH0pLFxuICAgIHZpdGVDb21wcmVzc2lvbigpLFxuICAgIHZpc3VhbGl6ZXIoKSxcbiAgXSxcbn0pO1xuIl0sCiAgIm1hcHBpbmdzIjogIjtBQUFnUyxTQUFTLG9CQUFvQjtBQUM3VCxPQUFPLHFCQUFxQjtBQUM1QixPQUFPLGFBQWE7QUFDcEIsT0FBTyxTQUFTO0FBQ2hCLE9BQU8sZ0JBQWdCO0FBQ3ZCLE9BQU8sZ0JBQWdCO0FBQ3ZCLFNBQVMsa0JBQWtCO0FBRTNCLElBQU8sc0JBQVEsYUFBYTtBQUFBLEVBQzFCLE9BQVE7QUFBQSxJQUNOLGVBQWU7QUFBQTtBQUFBLElBRWY7QUFBQSxFQUNGO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUEsRUFhQSxTQUFTO0FBQUEsSUFDUCxPQUFPO0FBQUEsTUFDSCxPQUFPO0FBQUEsSUFDWDtBQUFBLEVBQ0Y7QUFBQSxFQUNBLFNBQVM7QUFBQSxJQUNQLFFBQVE7QUFBQSxNQUNOLE9BQU87QUFBQSxRQUNMO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNGO0FBQUE7QUFBQSxNQUVBLFNBQVM7QUFBQSxJQUNYLENBQUM7QUFBQSxJQUNEO0FBQUEsTUFDRSxNQUFNO0FBQUEsTUFDTixTQUFTO0FBQUEsTUFDVCxnQkFBZ0IsRUFBRSxRQUFRLEtBQUssR0FBRztBQUNoQyxZQUFJLEtBQUssU0FBUyxVQUFVLEtBQUssS0FBSyxTQUFTLE1BQU0sR0FBRztBQUN0RDtBQUFBLFlBQ0U7QUFBQSxZQUNBLENBQUMsT0FBTyxXQUNOLFVBQVUsUUFBUSxRQUFRLElBQUksNkJBQTZCO0FBQUEsVUFDL0Q7QUFBQSxRQUNGO0FBQUEsTUFDRjtBQUFBLElBQ0Y7QUFBQSxJQUNBLElBQUk7QUFBQSxNQUNGLFVBQVU7QUFBQSxRQUNSLG9CQUFvQjtBQUFBLFVBQ2xCLE1BQU07QUFBQSxVQUNOLGlCQUFpQjtBQUFBLFFBQ25CO0FBQUEsTUFDRjtBQUFBLElBQ0YsQ0FBQztBQUFBLElBQ0QsV0FBVztBQUFBLE1BQ1QsS0FBSztBQUFBLE1BQ0wsTUFBTTtBQUFBLFFBQ0o7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELFdBQVc7QUFBQSxNQUNULEtBQUs7QUFBQSxNQUNMLE1BQU0sQ0FBQywwQkFBMEI7QUFBQSxNQUNqQyxTQUFTO0FBQUEsUUFDUDtBQUFBLFVBQ0UsS0FBSztBQUFBLFlBQ0g7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxZQUNBO0FBQUEsWUFDQTtBQUFBLFlBQ0E7QUFBQSxVQUNGO0FBQUEsVUFDQSxtQkFBbUIsQ0FBQyxVQUFVLFdBQVcsU0FBUztBQUFBLFVBQ2xELG1CQUFvQixDQUFDLFFBQVE7QUFBQSxRQUMvQjtBQUFBLE1BQ0Y7QUFBQSxJQUNGLENBQUM7QUFBQSxJQUNELGdCQUFnQjtBQUFBLElBQ2hCLFdBQVc7QUFBQSxFQUNiO0FBQ0YsQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
