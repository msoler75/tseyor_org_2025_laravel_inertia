<template>
    <InertiaLink :as="asIF" :data="data" :href="href" :method="method" :headers="headers" @click.capture="handleClick"
        :preserve-scroll="preserveScroll" :preserve-state="preserveState" :replace="replace" :only="only"
        :on-before="onBefore" :on-start="onStart" :on-progress="onProgress" :on-finish="onFinish"
        :on-cancel-token="onCancelToken" :on-cancel="onCancel" :on-success="onSuccess"
        :queryStringArrayFormat="queryStringArrayFormat"
        @finish="onFinish"
        >
    <slot />
    </InertiaLink>
</template>

<script setup>
import { Link as InertiaLink } from "@inertiajs/vue3";

const nav = useNav()

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
        preservePage: { // my navigation things
            type: Boolean,
            default: false
        },
        autoScroll: {
            type: Boolean,
            default: true
        },
        fadeOut: {
            type: Boolean,
            default: false
        }
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
    if(props.fadeOut)
        nav.fadeoutPage()
    if (props.preservePage)
        nav.dontFadeout = true
    if (!props.autoScroll)
        nav.dontScroll = true
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
