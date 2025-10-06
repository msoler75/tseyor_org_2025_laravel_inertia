import "./bootstrap";
// import "dropzone/dist/dropzone.css";
import "../css/app.css";

//import { createApp, h } from "vue";
import { createSSRApp, h, nextTick } from 'vue'
import AppLayout from "@/Layouts/AppLayout.vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
// import { usePage } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy";
import { Ziggy } from './ziggy.js'
import { Icon } from "@iconify/vue";
import { Head } from "@inertiajs/vue3";
import { registerAsyncComponents } from '../../async_components.d.ts';
//import FloatingVue from 'floating-vue'
import useRoute from '@/composables/useRoute.js';
import {useNav} from '@/Stores/nav.js';
import { LazyHydrationWrapper } from 'vue3-lazy-hydration';
import { usePWA } from '@/composables/usePWA.js';
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js';


// Deshabilitar restauración automática de scroll del navegador
/*if (typeof window !== 'undefined') {
  window.history.scrollRestoration = 'manual';
}*/

window.route = useRoute()

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
        methods: {useNav}
      })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      // .use(FloatingVue)
      // https://laracasts.com/discuss/channels/inertia/import-link-component-globally-in-inertiajs
      // app.config.globalProperties.$nav = useNav()
      registerAsyncComponents(app);
      app.component('LazyHydrate', LazyHydrationWrapper);

      // Inicializar PWA
      if (typeof window !== 'undefined') {
        const { initializePWA } = usePWA();
        initializePWA();

        // Inicializar Google Analytics
        const { loadGoogleAnalytics, trackPageView, trackDownload } = useGoogleAnalytics();
        loadGoogleAnalytics();

        // Variable para controlar tracking duplicado
        let lastTrackedUrl = null;
        let trackingTimeout = null;

        // Configurar listener global para descargas
        document.addEventListener('click', (event) => {
          const target = event.target.closest('a');
          if (target) {
            // Detectar enlaces de descarga
            const isDownload = target.hasAttribute('download') ||
                             target.href.match(/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|mp3|mp4|avi)$/i);

            if (isDownload) {
              const fileName = target.download || target.href.split('/').pop() || 'unknown_file';
              const fileExtension = fileName.split('.').pop()?.toLowerCase() || '';
              const fullUrl = target.href;
              trackDownload(fileName, fileExtension, fullUrl);
            }
          }
        });

        // Configurar seguimiento automático de navegación con Inertia
        // Usar evento 'finish' en lugar de 'navigate' para mayor precisión
        app.config.globalProperties.$inertia.on('finish', (event) => {
          // Limpiar timeout anterior si existe
          if (trackingTimeout) {
            clearTimeout(trackingTimeout);
          }

          // Usar timeout para evitar múltiples llamadas rápidas
          trackingTimeout = setTimeout(() => {
            nextTick(() => {
              const title = document.title;
              const url = window.location.href;

              // Evitar tracking duplicado de la misma URL en corto período
              if (lastTrackedUrl !== url) {
                // console.log('Inertia navigation finished, tracking page view:', url, title);
                trackPageView(url, title);
                lastTrackedUrl = url;
              }
            });
          }, 100); // Pequeño delay para agrupar eventos múltiples
        });
      }

      app.mount(el);
    return app;
  },
  progress: {
    color: "#4B5563",
  },
});
