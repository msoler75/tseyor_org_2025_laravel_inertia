<template>
    <InertiaLink :as="asIF" :data="data" :href="href" :method="method" :headers="headers" @click.capture="handleClick"
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
import { Link as InertiaLink } from "@inertiajs/vue3";
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js';

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


function handleClick() {
    console.log('router: Link.vue: handleClick', { href: props.href, time: Date.now() });
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

    console.log('Link.vue clicked, preservePage:', props.preservePage)
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

const emit = defineEmits(['finish'])

function onFinish() {
    console.log('finished Loading')
    emit('finish')
}
</script>
