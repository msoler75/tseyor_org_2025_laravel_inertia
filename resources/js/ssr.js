import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import { renderToString } from '@vue/server-renderer'
import { createSSRApp, h } from 'vue'
import { Icon } from "@iconify/vue";
import { Head } from "@inertiajs/vue3";
import { ZiggyVue } from "ziggy";
import { Ziggy } from './ziggy.js'
import { JSDOM } from 'jsdom'
import axios from 'axios'
import { registerAsyncComponents } from '../../async_components.d.ts';
import AppLayout from "@/Layouts/AppLayout.vue";
import useRoute from '@/composables/useRoute.js';
import {useNav} from '@/Stores/nav.js';
import { LazyHydrationWrapper } from 'vue3-lazy-hydration';
import dotenv from 'dotenv';

dotenv.config(); // carga valores de .env en process.env

global.axios = axios
global.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
global.route = useRoute()

// Crear una instancia de JSDOM
const dom = new JSDOM('<!DOCTYPE html><html><body></body></html>')

// Asignar DOMParser al objeto global
global.DOMParser = dom.window.DOMParser

// Asignar document al objeto global si es necesario
// global.document = dom.window.document

createServer(page =>
  createInertiaApp({
    page,
    render: renderToString,
    resolve: name => {
      const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
      const page = pages[`./Pages/${name}.vue`]
      page.default.layout = page.default.layout || AppLayout
      return page
    },
    setup({ App, props, plugin }) {
      const app = createSSRApp({
        render: () => h(App, props),
      })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .mixin({
        components: { Icon, Head },
        methods: { useNav }
      })

      registerAsyncComponents(app);
      app.component('LazyHydrate', LazyHydrationWrapper);

      return app
    },
  }),
)
