<template>
    <component :is="displaySrc?'img':'div'" ref="img" class="is-image transition-opacity duration-200" :src="displaySrc"
        :alt="alt" :title="title" :class="imageLoaded ? 'opacity-100' : 'opacity-0'" :style="styles" />
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
    width: {
        type: [Number, String],
        required: false
    },
    height: {
        type: [Number, String],
        required: false
    },
    srcWidth: {
        type: [Number, String],
        required: true,
        default: null
    },
    srcHeight: {
        type: [Number, String],
        required: true,
        default: null
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

const myDomain = computed(() => window?.location.origin)

// la imagen que se cargará del servidor
const imageSrc = computed(() => getImageUrl(props.src, props.fallback))

// este componente ya está montado?
const isMounted = ref(false)

// ya disponemos de la información del tamaño original de la imagen? y está pendiente de poner?
const justPutResized = ref(false)

const imageLoaded = ref(false)

function fillUnits(value) {
    let units = 'px'
    if (typeof value === 'string' && value.match(/\d+\D+/))
        units = ''
    return value + units
}

const styles = computed(() => {
    const s = {}
    if (props.width) s.width = fillUnits(props.width)
    if (props.height) s.height = fillUnits(props.height)
    return s
})

function getPixels(value) {
    if (typeof value === 'number') return value
    return parseInt(value)
}

// la imagen que se muestra en el componente
const displaySrc = ref("")

// effect active or not necessary
var justDontTouch = computed(() =>
    // si no queremos optimización
    !props.optimize
    ||
    // si es una url absoluta y corresponde a otro servidor...
    props.src.match(/https?:\/\/[^/]+/)?.[0] === myDomain.value
    // isFromMyDomain(props.src)
    ||
    // o si ya está la imagen redimensionada...
    props.src.match(/\?[wh]=/)
)

function init() {
    console.log('image.init. justdonttouch=', justDontTouch.value)
    console.log('window', window)
    if (justDontTouch.value)
        putSrcImage()
    else
        prepareEffect()
}
// const estado = ref("inicial")

function prepareEffect() {
    console.log('prepareEffect', props.srcWidth ,props.srcHeight)
    if (props.srcWidth && props.srcHeight) {
        const originalSize = {
            width: getPixels(props.srcWidth),
            height: getPixels(props.srcHeight)
        }
        applyImageOriginalSize(originalSize)
    }
    else
        getImageSize(imageSrc.value)
            .then(originalSize => {
                applyImageOriginalSize(originalSize)
            }).catch(err => {
                putSrcImage(imageSrc.value)
            })
}

function applyImageOriginalSize(originalSize) {
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
    }, 200)
}

async function replaceWithSizedImage() {
    console.log('replaceWithSizedImage', imageSrc.value, img.value.offsetWidth, img.value.offsetHeight)
    const webp = await isWebPSupported()
    var src = imageSrc.value + '?w=' + img.value.offsetWidth + '&h=' + img.value.offsetHeight
    if (!webp)
        src += '&fmt=jpg'
    putSrcImage(src)
}

function putSrcImage(src) {
    displaySrc.value = src
    nextTick(()=>{
        img.value.setAttribute('loading', 'lazy')
        img.value.onload = ()=> {
            imageLoaded.value = true
        }
    })
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
