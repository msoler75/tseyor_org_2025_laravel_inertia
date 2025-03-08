import "./bootstrap";
import "../css/app.css";
import "../css/tabs.css";

//import { createApp, h } from "vue";
import { createSSRApp, h } from 'vue'
import AppLayout from "@/Layouts/AppLayout.vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
// import { usePage } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy";
import { Icon } from "@iconify/vue";
import { Head } from "@inertiajs/vue3";
import { registerAsyncComponents } from '../../async_components.d.ts';
//import FloatingVue from 'floating-vue'
// import VueSocialSharing from 'vue-social-sharing'

const appName = "TSEYOR.org";

function updateTitle(title) {
  const fullTitle = `${title} - ${appName}`;

  // Actualizar el título
  const curTitle = document.head.querySelector("title[inertia]");
  if (curTitle) {
    curTitle.textContent = fullTitle;
    document.title = fullTitle;
  }
}

createInertiaApp({
  title: (title) => {

    // Actualizar la etiqueta canonical
    let canonicalTag = document.head.querySelector('link[rel="canonical"]');
    if (!canonicalTag) {
      canonicalTag = document.createElement("link");
      canonicalTag.setAttribute("rel", "canonical");
      document.head.appendChild(canonicalTag);
    }
    canonicalTag.setAttribute("href", window.location.href);

    if(title)
        updateTitle(title);

    // Lógica para obtener el título
    setTimeout(() => {
      if (title) return;

      // no hay un title, le buscamos uno en la propia página actual
      const h1s = document.body.querySelectorAll("h1")
      if(h1s.length > 0){
        title = h1s[0].textContent
        if(h1s.length > 1){
          title += ' - ' + h1s[1].textContent
        }
      }

      if (!title) title = document.body.querySelector("h2")?.textContent;

      updateTitle(title);
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
    // const app = createApp({ render: () => h(App, props) })
      const app = createSSRApp({ render: () => h(App, props) })
      // https://chriswray.dev/posts/how-to-add-components-globally-in-an-inertiajs-application
      .mixin({
        components: { Icon, Head },
        methods: { useNav },
      })
      // .use(VueSocialSharing)
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      // .use(FloatingVue)
      // https://laracasts.com/discuss/channels/inertia/import-link-component-globally-in-inertiajs
      // app.config.globalProperties.$nav = useNav()
      registerAsyncComponents(app);
      app.mount(el);
    return app;
  },
  progress: {
    color: "#4B5563",
  },
});
