import './bootstrap';
import '../css/app.css';
import '../css/tabs.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Icon } from '@iconify/vue';
import { Head, Link } from '@inertiajs/vue3';

// https://github.com/John-Weeks-Dev/facebook-clone/blob/master/resources/js/app.js
import { createPinia } from 'pinia'
const pinia = createPinia()

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
        // https://chriswray.dev/posts/how-to-add-components-globally-in-an-inertiajs-application
        .mixin({
            components: { Icon, Head, Link}
        })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(pinia)
            // https://laracasts.com/discuss/channels/inertia/import-link-component-globally-in-inertiajs
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
