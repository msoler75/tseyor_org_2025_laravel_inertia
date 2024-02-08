<template>
    <component :is="displaySrc?'img':'div'" ref="img" class="is-image" :src="displaySrc" :alt="alt" />
</template>



<script setup>
import { getImageSize, getImageUrl, isWebPSupported } from '@/composables/imageutils.js'

const props = defineProps({
    src: {
        type: String,
        required: true
    },
    fallback: {
        type: String,
        required: false
    },
    alt: {
        type: String,
    },
    title: {
        type: String
    },
    optimize: {
        type: Boolean,
        default: true
    }
});

const img = ref()

// la imagen que se cargará del servidor
const imageSrc = computed(() => getImageUrl(props.src, props.fallback))

// este componente ya está montado?
const isMounted = ref(false)

// ya disponemos de la información del tamaño original de la imagen? y está pendiente de poner?
const justPutResized = ref(false)

// la imagen que se muestra en el componente
const displaySrc = ref("")

// effect active or not necessary
var justDontTouch = computed(() =>
    // si no queremos optimización
    !props.optimize
    ||
    // si es una url absoluta y corresponde a otro servidor...
    props.src.match(/https?:\/\/[^/]+/)?.[0] == window.location.origin
    ||
    // o si ya está la imagen redimensionada...
    props.src.match(/\?[wh]=/)
)

function init() {
    console.log('image.init')
    if (justDontTouch.value)
        displaySrc.value = imageSrc.value
    else
        prepareEffect()
}
// const estado = ref("inicial")

function prepareEffect() {
    console.log('prepareEffect')
    // if(estado.value == "inicial")
    getImageSize(imageSrc.value)
        .then(originalSize => {
            console.log('originalSize of', imageSrc.value, 'is', originalSize)
            // generar una imagen transparent SVG con formato URI, debe tener ancho igual a size.width y alto igual a size.height
            displaySrc.value = `data:image/svg+xml,%3Csvg width="${originalSize.width}" height="${originalSize.height}" xmlns="http://www.w3.org/2000/svg"%3E%3C/svg%3E`
            setTimeout(() => {
                if (isMounted.value)
                    replaceWithSizedImage()
                else {
                    console.log('aun no está montada, esperamos...', imageSrc.value)
                    justPutResized.value = true
                }
            }, 1000)
        }).catch(err => {
            displaySrc.value = imageSrc.value
        })
}

async function replaceWithSizedImage() {
    console.log('replaceWithSizedImage', imageSrc.value, img.value.offsetWidth, img.value.offsetHeight)
    const webp = await isWebPSupported()
    displaySrc.value = imageSrc.value + '?w=' + img.value.offsetWidth + '&h=' + img.value.offsetHeight
    if (!webp)
        displaySrc.value += '&fmt=jpg'
    img.value.setAttribute('loading', 'lazy')
}



onMounted(() => {
    console.log(`image mounted: ${props.src}`)
    // doImageSize()
    isMounted.value = true
    if (justPutResized.value)
        replaceWithSizedImage()
})

// watch(imageSrc, init)

init()

onBeforeUnmount(() => {

})
</script>
