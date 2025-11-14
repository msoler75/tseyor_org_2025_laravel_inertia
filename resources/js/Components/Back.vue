<template>
    <div to="#afterNav" :disabled="!mounted || inline || !floating">
        <Link :href="enlace"
            class="transition duration-250 flex h-fit gap-2 text-sm items-center hover:text-secondary select-none"
            :class="calcClass" v-bind="$attrs"
            :preserve-state="preserveState"
            :preserve-page="preservePage"
            >
        <Icon icon="ph:arrow-left" />
        <div>
            <slot />
        </div>
        </Link>
    </div>
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
        default: true
    },
    preserveState: {
        type: Boolean,
        default: false
    },
    preservePage: {
        type: [Boolean, Function, null],
        default: false
    }
})


const nav = useNav()
const floating = computed(() => nav.scrollY >= props.floatAtY)
const currentUrl = usePage().url
const parentUrl = currentUrl.substring(0, currentUrl.lastIndexOf('/'));
const enlace = ref(props.href || parentUrl)



const mounted = ref(false)
onMounted(() => {
    mounted.value = true
})


const wrapToShow = 120 // nº de pixeles de recorrido scroll arriba para mostrar el boton
const wrapToHide = 70 // nº de pixeles de recorrido scroll abajo para ocultar el boton
const heightToHide = props.floatAtY - wrapToHide // altura de scroll para esta lógica de mostrar/ocultar con scroll

var show = ref(true)

var prevY = -10000 // marca de valor inicial sin computar
var subiendo = false
var recorridoUp = 0
var recorridoDown = 0

watch(() => nav.scrollY, (y) => {
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
                    if (recorridoDown > wrapToHide)
                        show.value = false
                }
                subiendo = false
                // bajando
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
        (!props.inline && floating.value ? 'p-3 bg-base-100 rounded-br-xl shadow-2xs' : '')
})
</script>
