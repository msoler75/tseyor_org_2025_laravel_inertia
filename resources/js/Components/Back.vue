<template>
    <Teleport v-if="mounted" to="#afterNav" :disabled="inline || nav.scrollY<props.floatAtY">
        <Link :href="enlace" class="flex h-fit gap-2 text-sm items-center hover:underline select-none"
        :class="!inline && nav.scrollY>=props.floatAtY?'p-3 bg-base-100 rounded-br-xl shadow':''"
        v-bind="$attrs" :fadeOut="fadeOut"
        >
        <Icon icon="ph:arrow-left" />
        <slot />
        </Link>
    </Teleport>
</template>

<script setup>
const props = defineProps({
    href: String,
    floatAtY: { // en qué posición de scroll Y aparece flotando a la izquierda
        type: Number,
        required: false,
        default: 130
    },
    inline: {
        type: Boolean,
        required: false,
        default: false
    },
    fadeOut: {
        type: Boolean,
        required: false,
        default: false
    }
})

const currentUrl = window.location.href;
const parentUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
const enlace = ref(props.href || parentUrl)

const nav = useNav()


const mounted = ref(false)
onMounted(()=>{
    mounted.value = true
})
</script>
