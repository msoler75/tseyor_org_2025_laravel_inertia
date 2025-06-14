<template>
    <component
        :is="errorLoading && errorIcon?Icon: !errorLoading && displaySrc ? 'img' : 'div'"
        ref="img"
        class="is-image transition-opacity duration-200 text-3xl"
        :src="displaySrc"
        :alt="alt"
        :title="title"
        icon="ph:image-broken-duotone"
        :class="[
            errorLoading && errorIcon ? 'opacity-50':
         imageLoaded
                ? 'opacity-100'
                : 'opacity-0',
            errorLoading && errorIcon
                ? 'bg-gray-500/25 flex justify-center items-center min-w-[80px] min-h-[80px]'
                : ''
        ]"
        :style="styles"
        @error="errorLoading = true"
    >
    </component>
</template>

<script setup>
import { getImageSize, getImageUrl, isWebPSupported } from "@/Stores/image.js";
import {belongsToCurrentDomain} from '@/composables/srcutils.js'
import { Icon } from "@iconify/vue";

const props = defineProps({
    src: {
        type: String,
        required: false,
    },
    fallback: {
        type: String,
        required: false,
    },
    width: {
        type: [Number, String],
        required: false,
    },
    height: {
        type: [Number, String],
        required: false,
    },
    srcWidth: {
        type: [Number, String],
        required: false,
        default: null,
    },
    srcHeight: {
        type: [Number, String],
        required: false,
        default: null,
    },
    alt: {
        type: String,
    },
    title: {
        type: String,
    },
    optimize: {
        type: Boolean,
        default: true,
    },
    errorIcon: {
        type: Boolean,
        default: true,
    },
    lazy: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["loaded", "error"]);

const img = ref();

// const myDomain = getMyDomain()

// la imagen que se cargará del servidor
const imageSrc = computed(() => getImageUrl(props.src, props.fallback));

const errorLoading = ref(false);

// este componente ya está montado?
const isMounted = ref(false);

const imageLoaded = ref(false);

function fillUnits(value) {
    let units = "px";
    if (typeof value === "string" && value.match(/\d+\D+/)) units = "";
    return value + units;
}

const styles = computed(() => {
    const s = {
        backgroundColor: errorLoading.value ? '#eee': 'transparent'
    };
    if (props.width) s.width = fillUnits(props.width);
    // if (props.height) s.height = fillUnits(props.height)
    return s;
});

function getPixels(value) {
    if (typeof value === "number") return value;
    return parseInt(value);
}

// la imagen que se muestra en el componente
const displaySrc = ref("");

// Comprobamos si estamos en el lado del cliente
const isClient = typeof window !== "undefined";
// const isClient = false

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
    // if (!isClient) return // No ejecutamos en SSR

    /*console.log(
        "image:init()",
        props.src,
        "fallback:",
        props.fallback,
        "props.width:",
        props.width,
        "props.height:",
        props.height
    );*/

    if (!imageSrc.value) return;

    // si es una url absoluta y corresponde a otro servidor o no queremos optimización (1)
    if (
        !belongsToCurrentDomain(imageSrc.value) ||
        //imageSrc.value.match(/https?:\/\/[^/]+/)?.[0] === myDomain ||
        !props.optimize
    )
        return putSrcImage(imageSrc.value);

    // si ya está la imagen redimensionada (2)
    if (imageSrc.value.match(/\?[wh]=/)) return putSrcImage(imageSrc.value);

    // Se ha establecido el tamaño mediante props (width y height) (3)
    if (props.width && props.height)
        // return putImageWithSize(props.width, props.height)
        return putFakeImage(props.width, props.height);

    // Se conoce el tamaño original de la imagen (4)
    if (props.srcWidth && props.srcHeight)
        return putFakeImage(
            getPixels(props.srcWidth),
            getPixels(props.srcHeight)
        );

    if (!isClient) return; // No ejecutamos en SSR

    // Se debe recalcular sus dimensiones óptimas de visualización (5)
    // así que primero se debe solicitar sus dimensiones originales al servidor
    getImageSize(imageSrc.value)
        .then((originalSize) => {
            console.log("getImageSize", imageSrc.value, { originalSize });
            if(originalSize.width==-1) {
                // no existe la imagen, se usa la imagen fallback
                emit('error')
                if(props.fallback)
                    putSrcImage(props.fallback)
                else
                    errorLoading.value = true;
            }
            else
            putFakeImage(originalSize.width, originalSize.height);
        })
        .catch((err) => {
            console.warn('Imagen '+imageSrc.value+' no existe', err);
            errorLoading.value = true;
            emit('error')
            if(props.fallback)
                putSrcImage(props.fallback)
            else
                errorLoading.value = true;
        });
}

var originalSize = { width: 0, height: 0 };

function putFakeImage(width, height) {
    console.log("putFakeImage", width, height);
    originalSize.width = width;
    originalSize.height = height;
    // generar una imagen transparent SVG con formato URI, debe tener ancho igual a size.width y alto igual a size.height
    displaySrc.value = `data:image/svg+xml,%3Csvg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg"%3E%3C/svg%3E`;

    if (!isClient) return; // No ejecutamos en SSR
    requestAnimationFrame(() => {
        // obtenemos las dimensiones reales de visualización (img.value.offsetWidth y offsetHeight)
        if (isMounted.value) {
            console.log(
                "after put fake image",
                imageSrc.value,
                "dimensions are",
                img.value?.offsetWidth,
                img.value?.offsetHeight
            );
            putImageWithSize(img.value?.offsetWidth, img.value?.offsetHeight);
        } else {
            console.log("repeat fake image");
            putFakeImage(width, height); // esperamos un poco más
        }
    }); // ya debe estar renderizado
}

async function putImageWithSize(widthOp, heightOp) {
    // if (!isClient) return // No ejecutamos en SSR
    console.log("image:putImageWithSize", imageSrc.value, widthOp, heightOp);
    if (widthOp == originalSize.width && heightOp == originalSize.height)
        return putSrcImage(imageSrc.value);
    const webp = await isWebPSupported();
    var src =
        imageSrc.value +
        "?w=" +
        Math.round(parseFloat(widthOp)) +
        "&h=" +
        Math.round(parseFloat(heightOp));
    if (!webp) src += "&fmt=jpg";
    putSrcImage(src);
}

var observer = null
function handleIntersection(entries) {
    entries.map((entry) => {
        if (entry.isIntersecting) {
            // entry.target.src = entry.target.dataset.src;
            // entry.target.classList.add("loaded")
            loadFinalImage();
            observer.unobserve(entry.target);
        }
    });
}

const options = {
    rootMargin: "2000px",
    threshold: 0
};

let finalSrc = null;

function putSrcImage(src) {
    // if (!isClient) return; // No ejecutamos en SSR
    console.log("putSrcImage", src);

    finalSrc = src;

    if (!props.lazy) {
        loadFinalImage();
    } else {
        // Solo ejecutar IntersectionObserver en cliente
        if (!isClient) return;
        // use API Intersecton Observer to simulate activation on scroll and loadFinalImage
        observer = new IntersectionObserver(handleIntersection, options);
        if (!img.value) {
            nextTick(() => {
                observer.observe(img.value);
            });
        } else {
            observer.observe(img.value);
        }
    }
    // imageElem = new Image()
    // imageLoaded.value = true
    // emit('loaded')
}

let imageElem = null;
function loadFinalImage() {
    console.log("loadFinalImage", finalSrc);
    imageElem = new Image();
    imageElem.src = finalSrc;
    imageElem.onload = () => {
        console.log("imageElem.onload");
        imageLoaded.value = true;
        emit("loaded");
        displaySrc.value = imageElem.src;
        imageElem = null;
    };
    imageElem.onerror = ()=> {
        errorLoading.value = true;
        emit('error')
        console.log('loadFinalImage errorLoading')
    }
}

onMounted(() => {
    console.log(`image:image mounted: ${imageSrc.value}`);
    // doImageSize()
    isMounted.value = true;
    //if (justPutResized.value)
    // putImageWithSize()
    // init()
});

// watch(imageSrc, init)

init();

/* onBeforeUnmount(() => {

}) */

// si cambia la imagen, reiniciamos el componente y la carga
watch(
    () => props.src,
    () => init()
);
</script>
