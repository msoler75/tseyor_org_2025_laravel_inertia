import "./bootstrap";
import "../css/app.css";
import "../css/tabs.css";

import { createApp, h } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
// import { createSSRApp, h } from 'vue'
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "ziggy";
import { Icon } from "@iconify/vue";
import { Head } from "@inertiajs/vue3";
//import FloatingVue from 'floating-vue'



// Función para actualizar las metaetiquetas Open Graph
function updateOpenGraphTags(title) {
    const ogUrl = document.querySelector('meta[property="og:url"]');
    const ogTitle = document.querySelector('meta[property="og:title"]');

    if (ogUrl) {
      ogUrl.setAttribute('content', window.location.href);
    }
    if (ogTitle) {
      ogTitle.setAttribute('content', `${title} - ${appName}`);
    }
  }

const appName = "TSEYOR.org";
createInertiaApp({
  title: (title) => {
    setTimeout(() => {
      // Lógica para obtener el título
      if (!title) {
        title = document.body.querySelector("h1")?.textContent;
      }
      if (!title) {
        title = document.body.querySelector("h2")?.textContent;
      }

      // Actualizar el título
      const curTitle = document.head.querySelector("title[inertia]");
      if (curTitle) {
        curTitle.textContent = `${title} - ${appName}`;
        document.title = curTitle.textContent;
      }

      // Actualizar la etiqueta canonical
      let canonicalTag = document.head.querySelector('link[rel="canonical"]');
      if (!canonicalTag) {
        canonicalTag = document.createElement("link");
        canonicalTag.setAttribute("rel", "canonical");
        document.head.appendChild(canonicalTag);
      }
      canonicalTag.setAttribute("href", window.location.href);

      // Actualizar las metaetiquetas Open Graph
      updateOpenGraphTags(title);
    }, 100);
    return `${title} - ${appName}`;
  },
  resolve: (name) => {
    return resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob("./Pages/**/*.vue")
    ).then((page) => {
      // Comprueba si el componente está en una carpeta "Partials" o "Partes"
      const isPartial = name
        .split("/")
        .some(
          (part) =>
            part.toLowerCase() === "partials" || part.toLowerCase() === "partes"
        );

      // Aplica AppLayout solo si no es un componente parcial y no tiene un layout definido
      if (!isPartial && page.default.layout === undefined) {
        page.default.layout = AppLayout;
      }

      return page;
    });
  },
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      // createSSRApp({ render: () => h(App, props) })
      // https://chriswray.dev/posts/how-to-add-components-globally-in-an-inertiajs-application
      .mixin({
        components: { Icon, Head },
        methods: { useNav },
      })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .mount(el);
    // .use(FloatingVue)
    // https://laracasts.com/discuss/channels/inertia/import-link-component-globally-in-inertiajs
    // app.config.globalProperties.$nav = useNav()
    return app;
  },
  progress: {
    color: "#4B5563",
  },
});
