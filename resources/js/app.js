import "./bootstrap";
import "../css/app.css";
import "../css/tabs.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "ziggy";
import { Icon } from "@iconify/vue";
import { Head } from "@inertiajs/vue3";
//import FloatingVue from 'floating-vue'

// https://github.com/John-Weeks-Dev/facebook-clone/blob/master/resources/js/app.js
import { createPinia } from "pinia";
const pinia = createPinia();

import {useNav} from "@/Stores/nav"

const appName =
  window.document.getElementsByTagName("title")[0]?.innerText || "TSEYOR";

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob("./Pages/**/*.vue")
    ),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
        // https://chriswray.dev/posts/how-to-add-components-globally-in-an-inertiajs-application
        .mixin({
          components: { Icon, Head },
          methods: {useNav}
        })
        .use(plugin)
        .use(ZiggyVue, Ziggy)
        .use(pinia)
        .mount(el)
        // .use(FloatingVue)
        // https://laracasts.com/discuss/channels/inertia/import-link-component-globally-in-inertiajs


        // app.config.globalProperties.$nav = useNav()



        return app
  },
  progress: {
    color: "#4B5563",
  },
});
