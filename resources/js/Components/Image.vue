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
        required: false,
        default: null
    },
    srcHeight: {
        type: [Number, String],
        required: false,
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



/*

Hay varios tipos de situaciones:

(1) la imagen es externa o no se desea optimizacion de ningun tipo (optimize=0),
    así que se carga directamente (putSrcImage)

(2) El src ya tiene parámetros w ó h, por ejemplo imagen.jpg?w=400,
    así que se carga la imagen directamente (putSrcImage)

(3) Se ha establecido el tamaño mediante props (width y height),
    así que se carga directamente con ?w=width y ?h=height (putSrcImage)

(4) Se conoce el tamaño original de la imagen (srcWidth y srcHeight),
    así que se visualiza una imagen en blanco para obtener las dimensiones optimizadas (widthOp y HeightOp)
    y después se carga la imagen con ?w=widthOp&h=heightOp (putImageWithSize)

(5) Se debe recalcular sus dimensiones óptimas de visualización,
    así que primero se debe solicitar sus dimensiones originales al servidor,
    para establecer una imagen en blanco svg (putFakeImage) de las mismas dimensiones originales y, al rato,
    saber las dimensiones de visualización, con las que cargaremos ya la imagen original aplicando esas dimensiones (putImageWithSize)

*/

function init() {
    console.log('Image.init', props.src, 'props.width:', props.width, 'props.height:', props.height)

    if(!props.src)
        return

    // si es una url absoluta y corresponde a otro servidor o no queremos optimización (1)
    if (props.src.match(/https?:\/\/[^/]+/)?.[0] === myDomain.value || !props.optimize)
        return putSrcImage(imageSrc.value)

    // si ya está la imagen redimensionada (2)
    if (props.src.match(/\?[wh]=/))
        return putSrcImage(imageSrc.value)

    // Se ha establecido el tamaño mediante props (width y height) (3)
    if (props.width && props.height)
        return putImageWithSize(props.width, props.height)

    // Se conoce el tamaño original de la imagen (4)
    if (props.srcWidth && props.srcHeight)
        return putFakeImage(getPixels(props.srcWidth),getPixels(props.srcHeight))

    // Se debe recalcular sus dimensiones óptimas de visualización (5)
    // así que primero se debe solicitar sus dimensiones originales al servidor
    getImageSize(imageSrc.value)
        .then(originalSize => {
            putFakeImage(originalSize.width, originalSize.height)
        }).catch(err => {
            console.error(err)
            putSrcImage(imageSrc.value)
        })
}

var originalSize =  {width: 0, height: 0}

function putFakeImage(width, height) {
    originalSize.width = width
    originalSize.height = height
    // generar una imagen transparent SVG con formato URI, debe tener ancho igual a size.width y alto igual a size.height
    displaySrc.value = `data:image/svg+xml,%3Csvg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg"%3E%3C/svg%3E`
    setTimeout(() => {
        // obtenemos las dimensiones reales de visualización (img.value.offsetWidth y offsetHeight)
        if (isMounted.value)
            putImageWithSize(img.value.offsetWidth, img.value.offsetHeight)
        else {
            putFakeImage(width, height) // esperamos un poco más
        }
    }, 50) // a los 50 milisegundos ya podemos saber la dimensiones de visualización de la imagen
}

async function putImageWithSize(widthOp, heightOp) {
    console.log('putImageWithSize', imageSrc.value, widthOp, heightOp)
    if(widthOp==originalSize.width&&heightOp==originalSize.height)
        return putSrcImage(imageSrc.value)
    const webp = await isWebPSupported()
    var src = imageSrc.value + '?w=' + Math.round(parseFloat(widthOp)) + '&h=' + Math.round(parseFloat(heightOp))
    if (!webp)
        src += '&fmt=jpg'
    putSrcImage(src)
}

function putSrcImage(src) {
    displaySrc.value = src
    // esperamos que cambie el componente :is
    nextTick(() => {
        img.value.setAttribute('loading', 'lazy')
        img.value.onload = () => {
            imageLoaded.value = true
        }
    })
}


onMounted(() => {
    console.log(`image mounted: ${props.src}`)
    // doImageSize()
    isMounted.value = true
    //if (justPutResized.value)
    // putImageWithSize()
    // init()
})

// watch(imageSrc, init)

init()

onBeforeUnmount(() => {

})

// si cambia la imagen, reiniciamos el componente y la carga
watch(()=>props.src, ()=>init())
</script>
