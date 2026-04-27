<template>
    <InertiaLink :as="asIF" :data="data" :href="href" :method="method" :headers="headers" @click.capture="handleClick($event)"
        :preserve-scroll="preserveScroll" :preserve-state="preserveState" :replace="replace" :only="only"
        :on-before="onBefore" :on-start="onStart" :on-progress="onProgress" :on-finish="onFinish"
        :on-cancel-token="onCancelToken" :on-cancel="onCancel" :on-success="onSuccess"
        :prefetch="prefetch"
        @finish="onFinish"
        >
    <slot />
    </InertiaLink>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { Link as InertiaLink } from "@inertiajs/vue3";
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js';
import { onMounted, onUnmounted } from 'vue'

const nav = useNav()
const { trackEvent, trackDownload } = useGoogleAnalytics();

const props = defineProps(
    {
        data: {
            type: Object,
            default: undefined
        },
        href: {
            type: String,
            required: true
        },
        method: {
            type: String,
            default: undefined
        },
        as: {
            type: String,
            default: undefined
        },
        headers: {
            type: Object,
            default: undefined
        },
        onClick: {
            type: Function,
            default: undefined
        },
        preserveScroll: {
            type: [Boolean, Function],
            default: true /* ESTA ES LA DIFERENCIA CON EL LINK DE INERTIA */
        },
        preserveState: {
            type: [Boolean, Function, null],
            default: null
        },
        replace: {
            type: Boolean,
            default: false
        },
        only: {
            type: Array,
            default: () => []
        },
        onCancelToken: {
            type: Function,
            default: undefined
        },
        onBefore: {
            type: Function,
            default: undefined
        },
        onStart: {
            type: Function,
            default: undefined
        },
        onProgress: {
            type: Function,
            default: undefined
        },
        onFinish: {
            type: Function,
            default: undefined
        },
        onCancel: {
            type: Function,
            default: undefined
        },
        onSuccess: {
            type: Function,
            default: undefined
        },
        queryStringArrayFormat: {
            type: String,
            default: 'brackets'
        },
        prefetch: {
            type: Boolean,
            default: false
        },
        preservePage: { // my navigation things
            type: Boolean,
            default: false
        }/*,
        autoScroll: {
            type: Boolean,
            default: true
        }*/
    }
)

const asIF = computed( () => {
                if (props.method) {
                    return 'button'
                }
                return undefined
            }
        )


function handleClick(event) {
    console.log('router: Link.vue: handleClick', { href: props.href, time: Date.now(), event });
    // Tracking de Google Analytics para descargas
    if (props.href && props.href.match(/\.(pdf|doc|docx|xls|xlsx|ppt|pptx|zip|rar|mp3|mp4|avi)$/i)) {
        const fileName = props.href.split('/').pop() || 'unknown_file';
        const fileExtension = fileName.split('.').pop()?.toLowerCase() || '';
        trackDownload(fileName, fileExtension, props.href);
    }
    // Tracking de Google Analytics para enlaces externos
    else if (props.href && (props.href.startsWith('http') || props.href.startsWith('mailto:') || props.href.startsWith('tel:'))) {
        trackEvent('click', {
            link_domain: new URL(props.href).hostname || props.href,
            link_url: props.href,
            outbound: true
        });
    }

    // create custom event for other parts of the app
    const customEvent = new CustomEvent('link-clicked', { detail: { url: props.href } });
    window.dispatchEvent(customEvent);


    const domLink = event.target?.closest('a')

    // Detect modifier / middle-clicks robustly (cross-browser)
    const isModified = event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ||
        event.button === 1 || event.which === 2 || ((event.buttons || 0) & 4) === 4

    // If previous global handler opened a tab, or this is a modified click,
    // cancel Inertia navigation here to avoid visits in the current tab.
    try {
        if (domLink && (domLink.dataset && domLink.dataset.__inertia_opened === '1' || isModified)) {
            try { event.preventDefault() } catch (e) {}
            try { event.stopImmediatePropagation() } catch (e) {}
            try { router.cancelAll() } catch (e) {}
            console.log('Link.vue: cancelled Inertia visit due to modified click or prior open', { href: props.href })
            // still run tracking/custom event, but do not let Inertia visit proceed
        }
    } catch (e) {
        /* ignore */
    }

    //console.log('Link.vue clicked, preservePage:', props.preservePage)
    if (props.preservePage)
        nav.preservePage= true
    /*if(props.fadeOut)
        nav.fadeoutPage()
    if (!props.autoScroll)
        nav.dontScroll = true*/
    if (props.onClick) {
        props.onClick()
    }
}
// Add global capture listeners to stop propagation before Inertia's handlers run.
onMounted(() => {
    const globalHandler = (event) => {
        try {
            const domLink = event.target?.closest && event.target.closest('a')
            if (!domLink) return

            const isModified = event.ctrlKey || event.metaKey || event.button === 1
            if (!isModified) return

            // Prevent Inertia from intercepting by marking the event as prevented
            // and stopping propagation in capture phase. Then open a single new tab
            // manually to avoid browser-specific double-open issues (Firefox).
            try { router.cancelAll() } catch (e) {}
            try { event.preventDefault() } catch (e) {}
            try { event.stopImmediatePropagation() } catch (e) {}

            // Ensure we only open one tab per click (avoid double-open in Firefox auxclick+click)
            if (!domLink.dataset.__inertia_opened) {
                domLink.dataset.__inertia_opened = '1'
                try { window.open(domLink.href, '_blank') } catch (e) {}
                // clear the flag shortly after
                setTimeout(() => { try { delete domLink.dataset.__inertia_opened } catch (e) {} }, 500)
            }
        } catch (e) {
            /* ignore */
        }
    }

    // Install global handler only once across components. Keep it installed for the
    // lifetime of the page (no teardown on unmount) to simplify behavior.
    if (!window.__inertia_link_global_handler) {
        document.addEventListener('mousedown', globalHandler, true)
        document.addEventListener('mouseup', globalHandler, true)
        // Also intercept final click in capture phase to stop handlers that run on mouseup
        document.addEventListener('click', globalHandler, true)
        // Firefox may use auxclick for middle clicks; listen for it too.
        document.addEventListener('auxclick', globalHandler, true)
        // store on window so other instances know it's installed
        window.__inertia_link_global_handler = globalHandler
    }
})

// Keep the global handler installed for the page lifetime; no teardown on unmount.

const emit = defineEmits(['finish'])

function onFinish() {
    console.log('finished Loading')
    emit('finish')
}
</script>
