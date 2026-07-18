<template>
    <img
        v-if="!hydrated"
        :src="imageSrc"
        :alt="alt"
        :title="title"
        class="is-image"
        :style="ssrStyles"
    />
    <component
        v-else
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
import { getImageSize, getImageUrl } from "@/composables/image.js";
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
    rootMargin: {
        type: String,
        default: "3000px 0px 3000px 0px", // Formato completo: top right bottom left
    },
    priority: {
        type: Boolean,
        default: false,
    },
    quality: {
        type: Number,
        default: null
    }
});

const emit = defineEmits(["loaded", "error"]);

const img = ref();

// flag para controlar render cliente tras hidratacion
const hydrated = ref(false);

// Control de logs de imagen (puede configurarse desde localStorage o variable global)
const enableImageLogs = ref(
    typeof window !== 'undefined' &&
    (localStorage.getItem('enable-image-logs') === 'true' ||
     window.enableImageLogs === true)
);

// Función de logging condicional para el componente Image
const log = (message, ...args) => {
    if (enableImageLogs.value) {
        log(`[Image] ${message}`, ...args);
    }
};

// const myDomain = getMyDomain()

// la imagen que se cargará del servidor
const imageSrc = computed(() => getImageUrl(props.src, props.fallback));

const errorLoading = ref(false);

// este componente ya está montado?
const isMounted = ref(false);

const imageLoaded = ref(false);

// Estado para rastrear si la imagen está visible según IntersectionObserver
const isVisible = ref(false);

// Estado para saber si ya se configuró la imagen final
const finalImageConfigured = ref(false);

// Comprobamos si estamos en SSR
const isSSR = typeof window === 'undefined';

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

const ssrStyles = computed(() => {
    const s = {};
    if (props.width) s.width = fillUnits(props.width);
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

    /*log(
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
            log("getImageSize", imageSrc.value, { originalSize });
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

// Función helper para obtener el elemento DOM real desde una ref de Vue
function getDOMElement(ref) {
    if (!ref) return null;

    // Si ya es un elemento DOM nativo
    if (ref instanceof Element) {
        return ref;
    }

    // Si es un componente Vue, intentar obtener su elemento raíz
    if (ref.$el) {
        return ref.$el;
    }

    // Si es una ref con el elemento DOM dentro
    if (ref.value && ref.value instanceof Element) {
        return ref.value;
    }

    return null;
}

function putFakeImage(width, height) {
    log("putFakeImage", width, height);
    originalSize.width = width;
    originalSize.height = height;
    // generar una imagen transparent SVG con formato URI, debe tener ancho igual a size.width y alto igual a size.height
    displaySrc.value = `data:image/svg+xml,%3Csvg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg"%3E%3C/svg%3E`;

    if (!isClient) return; // No ejecutamos en SSR
    requestAnimationFrame(() => {
        // obtenemos las dimensiones reales de visualización
        if (isMounted.value) {
            const domElement = getDOMElement(img.value);
            log(
                "after put fake image",
                imageSrc.value,
                "dimensions are",
                domElement?.offsetWidth,
                domElement?.offsetHeight
            );
            putImageWithSize(domElement?.offsetWidth, domElement?.offsetHeight);
        } else {
            log("repeat fake image");
            putFakeImage(width, height); // esperamos un poco más
        }
    }); // ya debe estar renderizado
}

async function putImageWithSize(widthOp, heightOp) {
    // if (!isClient) return // No ejecutamos en SSR
    log("image:putImageWithSize", imageSrc.value, widthOp, heightOp);
    if (widthOp == originalSize.width && heightOp == originalSize.height)
        return putSrcImage(imageSrc.value);
    // No forzar fmt, dejar que el servidor decida basado en el formato original
    // const webp = await isWebPSupported();
    var src =
        imageSrc.value +
        "?w=" +
        Math.round(parseFloat(widthOp)) +
        "&h=" +
        Math.round(parseFloat(heightOp))
        + (props.quality? 'q='+props.quality : '')
    putSrcImage(src);
}

var observer = null;
var scrollFallbackInterval = null;

// Sistema de fallback basado en scroll para cuando IntersectionObserver no está disponible
function startScrollFallback() {
    if (scrollFallbackInterval) return; // Ya está iniciado

    log("🔄 Iniciando sistema de fallback basado en scroll");

    const checkVisibility = () => {
        if (isVisible.value) return;

        const domElement = getDOMElement(img.value);
        if (!domElement) return;

        const rect = domElement.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        const triggerDistance = 3000; // 3000px antes de que entre al viewport
        const distanceFromViewport = rect.top - windowHeight;

        if (distanceFromViewport <= triggerDistance) {
            log("🎯🔄 FALLBACK SCROLL: Elemento detectado como visible!");
            isVisible.value = true;

            // Limpiar el interval de fallback
            if (scrollFallbackInterval) {
                clearInterval(scrollFallbackInterval);
                scrollFallbackInterval = null;
            }
        }
    };

    // Verificar inmediatamente
    checkVisibility();

    // Y después cada 500ms mientras se hace scroll
    scrollFallbackInterval = setInterval(checkVisibility, 500);
}

// Inicializar IntersectionObserver desde el comienzo
function initIntersectionObserver() {
    if (!isClient) {
        log("⚠️ No está en cliente, no se puede inicializar observer");
        return;
    }

    if (observer) {
        log("⚠️ Observer ya existe, evitando recrear");
        return;
    }

    // Verificar si IntersectionObserver está disponible
    if (typeof IntersectionObserver === 'undefined') {
        console.warn("⚠️ IntersectionObserver no disponible, usando fallback desde el inicio");
        startScrollFallback();
        return;
    }

    log("🔧 Inicializando IntersectionObserver con configuración:", {
        rootMargin: props.rootMargin,
        lazy: props.lazy,
        priority: props.priority,
        imageSrc: imageSrc.value
    });

    // Verificar que el formato del rootMargin sea correcto
    const rootMarginFormatted = props.rootMargin.includes(' ') ? props.rootMargin : `${props.rootMargin} 0px ${props.rootMargin} 0px`;

    log("📐 rootMargin formateado:", rootMarginFormatted);

    const options = {
        root: null, // viewport
        rootMargin: rootMarginFormatted,
        threshold: 0
    };

    observer = new IntersectionObserver(handleIntersection, options);

    log("✅ IntersectionObserver creado exitosamente con opciones:", options);

    // Verificar que el observer se creó correctamente
    if (observer.rootMargin) {
        log("🎯 Observer rootMargin confirmado:", observer.rootMargin);
    } else {
        console.warn("⚠️ Observer no tiene rootMargin, puede ser un problema de formato");
    }

    // Si el elemento ya existe, comenzar a observarlo inmediatamente
    if (img.value) {
        const domElement = getDOMElement(img.value);
        if (domElement) {
            log("🎯 Elemento img ya disponible, iniciando observación inmediata");
            observer.observe(domElement);

            // Debug: información del elemento
            const rect = domElement.getBoundingClientRect();
            log("📊 Info del elemento al iniciar observación:", {
                top: rect.top,
                bottom: rect.bottom,
                height: rect.height,
                viewportHeight: window.innerHeight,
                distanceFromViewport: rect.top - window.innerHeight
            });
        } else {
            console.warn("⚠️ No se pudo obtener elemento DOM válido desde img.value");
        }
    } else {
        log("⏳ Elemento img no disponible aún, esperando...");
    }
}

// Función que maneja la intersección - SOLO marca el estado de visibilidad
function handleIntersection(entries) {
    entries.forEach((entry) => {
        const rect = entry.boundingClientRect;
        const rootBounds = entry.rootBounds;

        log("📡 IntersectionObserver callback activado:", {
            isIntersecting: entry.isIntersecting,
            intersectionRatio: entry.intersectionRatio,
            rootMargin: props.rootMargin,
            observerRootMargin: observer?.rootMargin,
            targetInfo: {
                className: entry.target.className,
                tagName: entry.target.tagName,
                top: rect.top,
                bottom: rect.bottom,
                left: rect.left,
                right: rect.right,
                width: rect.width,
                height: rect.height
            },
            rootBoundsInfo: rootBounds ? {
                top: rootBounds.top,
                bottom: rootBounds.bottom,
                left: rootBounds.left,
                right: rootBounds.right,
                width: rootBounds.width,
                height: rootBounds.height
            } : null,
            distanceFromTop: rect.top,
            distanceFromBottom: rect.top - (rootBounds?.height || window.innerHeight),
            viewportHeight: window.innerHeight
        });

        if (entry.isIntersecting) {
            log("👁️✅ ¡INTERSECCIÓN DETECTADA POR OBSERVER!");
            log("📊 Detalles críticos:", {
                intersectionRatio: entry.intersectionRatio,
                distanceFromViewportTop: rect.top,
                distanceFromViewportBottom: rect.top - window.innerHeight,
                rootMarginConfigured: props.rootMargin,
                rootMarginEffective: observer?.rootMargin,
                detectionMethod: "IntersectionObserver"
            });

            isVisible.value = true;

            // Dejar de observar una vez detectada la visibilidad
            observer.unobserve(entry.target);
            log("🔚 IntersectionObserver: dejando de observar elemento tras detección exitosa");
        } else {
            log("👁️❌ Elemento aún NO visible");
            log("📍 Posición actual:", {
                elementTop: rect.top,
                viewportHeight: window.innerHeight,
                distanceToEnterViewport: rect.top - window.innerHeight,
                shouldTriggerAt: `${window.innerHeight + 3000}px from top`
            });
        }
    });
}

// Computed para determinar si debe cargarse inmediatamente (solo casos explícitos)
const shouldLoadEagerly = computed(() => {
    // Solo cargar inmediatamente si se especifica explícitamente priority o lazy está desactivado
    return props.priority || !props.lazy;
});

let finalSrc = null;

function putSrcImage(src) {
    log("📥 putSrcImage called with:", src, "rootMargin:", props.rootMargin);

    finalSrc = src;
    finalImageConfigured.value = true;

    if (shouldLoadEagerly.value) {
        // Cargar inmediatamente para imágenes prioritarias o sin lazy loading
        log("🚀 Cargando inmediatamente (shouldLoadEagerly=true)");
        loadFinalImage();
    } else if (isVisible.value) {
        // Si ya fue detectada como visible por IntersectionObserver, cargar inmediatamente
        log("👁️ Imagen ya visible, cargando inmediatamente");
        loadFinalImage();
    } else {
        // Esperar a que IntersectionObserver detecte visibilidad
        log("⏳ Esperando detección de visibilidad con rootMargin:", props.rootMargin);
        log("📊 Estado actual: isVisible=", isVisible.value, "finalImageConfigured=", finalImageConfigured.value);

        // Asegurar que el observer esté observando este elemento
        if (observer && img.value) {
            const domElement = getDOMElement(img.value);
            if (domElement) {
                log("👀 Iniciando observación del elemento:", domElement.className || domElement.tagName);
                observer.observe(domElement);
            } else {
                console.warn("⚠️ No se pudo obtener elemento DOM válido para observación");
            }
        } else if (!observer && img.value) {
            // No hay observer disponible, usar fallback de scroll
            log("🔄 No hay observer disponible, iniciando fallback de scroll");
            startScrollFallback();
        } else {
            console.warn("⚠️ Observer o img no disponible:", { observer: !!observer, img: !!img.value });
            // Intentar inicializar si no existe
            if (!observer) {
                initIntersectionObserver();
            }
            // Volver a intentar en el próximo tick
            nextTick(() => {
                if (observer && img.value) {
                    const domElement = getDOMElement(img.value);
                    if (domElement) {
                        log("🔄 Reintentando observación en nextTick");
                        observer.observe(domElement);
                    }
                } else if (!observer && img.value) {
                    log("🔄 Iniciando fallback en nextTick");
                    startScrollFallback();
                }
            });
        }
    }
}

let imageElem = null;
function loadFinalImage() {
    log("🖼️ loadFinalImage starting with:", finalSrc);

    // Evitar cargas duplicadas
    /*if (imageLoaded.value || !finalSrc) {
        log("⚠️ Evitando carga duplicada:", { imageLoaded: imageLoaded.value, finalSrc: !!finalSrc });
        return;
    }*/

    imageElem = new Image();
    imageElem.src = finalSrc;
    imageElem.onload = () => {
        log("✅ Imagen cargada exitosamente:", finalSrc);
        imageLoaded.value = true;
        emit("loaded");
        displaySrc.value = imageElem.src;
        imageElem = null;

        // Limpiar fallback de scroll si está activo
        if (scrollFallbackInterval) {
            clearInterval(scrollFallbackInterval);
            scrollFallbackInterval = null;
        }

        // Desconectar observer una vez cargada exitosamente
        if (observer && img.value) {
            const domElement = getDOMElement(img.value);
            if (domElement) {
                observer.unobserve(domElement);
                log("🔚 Observer desconectado tras carga exitosa");
            }
        }
    };
    imageElem.onerror = () => {
        console.error("❌ Error cargando imagen:", finalSrc);
        errorLoading.value = true;
        emit('error');
        imageElem = null;
    }
}

onMounted(() => {
    log(`🚀 IMAGE MOUNTED: ${imageSrc.value}`);
    log(`📏 rootMargin configurado:`, props.rootMargin);
    log(`⚡ lazy:`, props.lazy, `priority:`, props.priority);
    isMounted.value = true;

    // marcar como hydrated en el cliente para que la plantilla muestre la versión cliente
    hydrated.value = true;

    // SIEMPRE inicializar IntersectionObserver si lazy loading está habilitado
    if (props.lazy && !props.priority) {
        initIntersectionObserver();
        log("📡 IntersectionObserver inicializado desde onMounted");
    }

    // Inicializar la carga de la imagen
    init();
});

// Watcher para asegurar que el observer esté observando cuando el elemento esté listo
watch(img, (newImg) => {
    if (newImg && !imageLoaded.value && props.lazy && !props.priority) {
        log("🔍 Elemento img detectado en watcher, iniciando observación con rootMargin:", props.rootMargin);

        // Obtener el elemento DOM real
        const domElement = getDOMElement(newImg);

        if (domElement && observer) {
            observer.observe(domElement);
        } else if (domElement && !observer) {
            // No hay observer, usar fallback
            log("🔄 No hay observer en watcher, iniciando fallback de scroll");
            startScrollFallback();
        } else {
            console.warn("⚠️ No se pudo obtener elemento DOM válido:", newImg);
        }
    }
});

// Watcher para cargar la imagen cuando se marca como visible
watch(isVisible, (newIsVisible) => {
    if (newIsVisible && finalImageConfigured.value && finalSrc && !imageLoaded.value) {
        const detectionMethod = observer ? "IntersectionObserver" : "ScrollFallback";
        log(`✅ isVisible cambió a true (método: ${detectionMethod}), cargando imagen final`);

        loadFinalImage();
    }
});

onBeforeUnmount(() => {
    // Limpiar observer
    if (observer) {
        observer.disconnect();
        observer = null;
    }

    // Limpiar fallback de scroll
    if (scrollFallbackInterval) {
        clearInterval(scrollFallbackInterval);
        scrollFallbackInterval = null;
    }
});

// si cambia la imagen, reiniciamos el componente y la carga
watch(
    () => props.src,
    () => init()
);
</script>
