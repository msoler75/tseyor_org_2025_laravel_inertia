import "./bootstrap";
import "../css/app.css";
import "../css/tabs.css";

import { createApp, h } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
// import { createSSRApp, h } from 'vue'
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
// import { usePage } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy";
import { Icon } from "@iconify/vue";
import { Head } from "@inertiajs/vue3";
//import FloatingVue from 'floating-vue'

const appName = "TSEYOR.org";

// Función para actualizar las metaetiquetas
/* function updateMetaTags(title, description, image) {
  const fullTitle = `${title} - ${appName}`;
  const url = window.location.href;

  // Función para convertir URL relativa a absoluta
  function getAbsoluteUrl(relativeUrl) {
    if (
      relativeUrl &&
      !relativeUrl.startsWith("http") &&
      !relativeUrl.startsWith("//")
    ) {
      return `${window.location.origin}${
        relativeUrl.startsWith("/") ? "" : "/"
      }${relativeUrl}`;
    }
    return relativeUrl;
  }

  // Convertir la imagen a URL absoluta
  const absoluteImageUrl = getAbsoluteUrl(image);

  // Actualizar el título
  const curTitle = document.head.querySelector("title[inertia]");
  if (curTitle) {
    curTitle.textContent = fullTitle;
    document.title = fullTitle;
  }

  // Función auxiliar para actualizar o crear metaetiquetas
  function updateMetaTag(property, content) {
    let tag =
      document.querySelector(`meta[property="${property}"]`) ||
      document.querySelector(`meta[name="${property}"]`);
    if (!tag) {
      tag = document.createElement("meta");
      tag.setAttribute(property.includes(":") ? "property" : "name", property);
      document.head.appendChild(tag);
    }
    tag.setAttribute("content", content);
  }

  // Actualizar metaetiquetas básicas
  updateMetaTag("description", description);

  // Actualizar Open Graph tags
  updateMetaTag("og:url", url);
  updateMetaTag("og:title", fullTitle);
  updateMetaTag("og:description", description);
  updateMetaTag("og:image", absoluteImageUrl);

  // Actualizar Twitter Card tags
  updateMetaTag("twitter:url", url);
  updateMetaTag("twitter:title", fullTitle);
  updateMetaTag("twitter:description", description);
  updateMetaTag("twitter:image", absoluteImageUrl);

  // Actualizar etiqueta canonical
  let canonical = document.querySelector('link[rel="canonical"]');
  if (!canonical) {
    canonical = document.createElement("link");
    canonical.setAttribute("rel", "canonical");
    document.head.appendChild(canonical);
  }
  canonical.setAttribute("href", url);
}

*/

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
    
    console.log('TITLE:', title)
    // const page = usePage();
    // console.log({ props: page.props, title });
    // if (!title) title = page.props?.seo?.titulo;
    // console.log("title:", title);

    // const description = page.props?.seo?.descripcion;

    // const image = page.props?.seo?.imagen;

    // Actualizar la etiqueta canonical
    let canonicalTag = document.head.querySelector('link[rel="canonical"]');
    if (!canonicalTag) {
      canonicalTag = document.createElement("link");
      canonicalTag.setAttribute("rel", "canonical");
      document.head.appendChild(canonicalTag);
    }
    // console.log("canonical", window.location.href);
    canonicalTag.setAttribute("href", window.location.href);
    console.log('CANONICAL:', window.location.href)

    //updateMetaTags(title, description, image);

    /*if(!title){
        const inertiaTitle = document.head.querySelector('title[inertia]')
        if(inertiaTitle)
            title = inertiaTitle.textContent
                console.log('TITLE (inertia):', title)
        }*/

    if(title)
        updateTitle(title);


    // Lógica para obtener el título
    setTimeout(() => {
      if (title) return;

      // no hay un title, le buscamos uno en la propia página actual
      title = document.body.querySelector("h1")?.textContent;

      if (!title) title = document.body.querySelector("h2")?.textContent;

      /// Actualizar las metaetiquetas
      //updateMetaTags(title, description, image);

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
