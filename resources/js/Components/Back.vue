<template>
    <Teleport v-if="mounted" to="#afterNav" :disabled="inline || !floating">
        <Link :href="enlace"
            class="transition duration-200 flex h-fit gap-2 text-sm items-center hover:underline select-none"
            :class="calcClass" v-bind="$attrs" :fadeOut="fadeOut">
        <Icon icon="ph:arrow-left" />
        <div :class="floating ? 'hidden sm:block' : ''">
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


const heightToHide = 600 // altura de scroll para esta lógica de mostrar/ocultar con scroll
const wrapToShow = 120 // nº de pixeles de recorrido scroll arriba para mostrar el boton
const wrapToHide = 70 // nº de pixeles de recorrido scroll abajo para ocultar el boton

var show = ref(true)

var prevY = -10000 // marca de valor inicial sin computar
var subiendo = false
var recorridoUp = 0
var recorridoDown = 0

watch(() => nav.scrollY, (y) => {
    console.log('watch scrollY', y)
    if (!floating) return
    if (prevY != 10000) {
        var dy = y - prevY
        if (y < heightToHide)
            show.value = true
        else
            if (dy > 0) {
                if (subiendo) {
                    recorridoDown = dy
                } else {
                    recorridoDown += dy
                    if (recorridoDown >  wrapToHide)
                        show.value = false
                }
                subiendo = false
                // bajando
                console.log("bajando")
            } else {
                // subiendo
                if (!subiendo) {
                    recorridoUp = dy
                } else {
                    recorridoUp += dy
                    if (recorridoUp < -1 * wrapToShow)
                        show.value = true
                }
                subiendo = true
            }
    }
    prevY = y
})

const calcClass = computed(() => {
    return (show.value ? 'opacity-100' : 'opacity-0') + ' ' +
        (!props.inline && floating.value ? 'p-3 bg-base-100 rounded-br-xl shadow' : '')
})
</script>
