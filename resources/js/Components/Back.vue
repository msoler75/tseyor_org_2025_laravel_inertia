<template>
    <Teleport v-if="mounted" to="#afterNav" :disabled="inline || !floating">
        <Link :href="enlace" class="flex h-fit gap-2 text-sm items-center hover:underline select-none"
            :class="!inline && floating ? 'p-3 bg-base-100 rounded-br-xl shadow' : ''" v-bind="$attrs" :fadeOut="fadeOut">
        <Icon icon="ph:arrow-left" />
        <div :class="floating?'hidden sm:block':''">
            <slot />
        </div>
        </Link>
    </Teleport>
    <span v-if="!(inline || !floating)"></span>
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

const floating = computed(() => nav.scrollY >= props.floatAtY)
const currentUrl = window.location.href;
const parentUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
const enlace = ref(props.href || parentUrl)

const nav = useNav()


const mounted = ref(false)
onMounted(() => {
    mounted.value = true
})
</script>
