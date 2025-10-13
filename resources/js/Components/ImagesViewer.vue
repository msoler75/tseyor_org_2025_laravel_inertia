<template>
    <TransitionFade>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-md text-gray-200 touch-none text-3xl"
            v-show="show && images?.length" ref="vImagesWrap">
            <!-- Loading -->
             <div class="absolute inset-0 w-full h-full flex items-center justify-center z-50" v-show="state.imgState === 'loading'">
                <Spinner/>
             </div>

            <!-- VueZoomable container -->
            <VueZoomable
                style="width: 100vw; height: 100vh;"
                selector="#imageContent"
                :min-zoom="0.1"
                :max-zoom="10"
                :wheel-enabled="true"
                :wheel-zoom-step="0.15"
                :dbl-click-enabled="true"
                :dbl-click-zoom-step="0.5"
                :pan-enabled="true"
                :zoom-enabled="true"
                :mouse-enabled="true"
                :touch-enabled="true"
                :enable-control-button="false"
                zoom-origin="pointer"
                v-model:zoom="zoomLevel"
                v-model:pan="panPosition"
            >
                <div id="imageContent" class="w-full h-full flex items-center justify-center">
                    <!-- Imagen cargada con éxito -->
                    <img v-show="state.imgState === 'success'"
                        :src="state.src"
                        :style="`transform: rotate(${rotationAngle}deg);`"
                        class="max-w-full max-h-full object-contain pointer-events-none select-none"
                        alt=""
                        @load="onImageLoad" />

                    <!-- Error de carga de imagen -->
                    <div v-show="state.imgState === 'error'">
                        <Icon icon="ph:image-broken-duotone" />
                    </div>
                </div>
            </VueZoomable>            <!-- Botón de cierre -->
            <button
                class="z-40 text-2xl absolute flex justify-center items-center top-1 lg:top-12 right-4 lg:right-12 w-9 h-9 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                :class="!showFilename ? 'bg-black/30' : 'lg:bg-black/30'" aria-hidden="true"
                @click="handleClose" v-if="showCloseBtn">
                <Icon icon="material-symbols-light:close" />
            </button>

            <!-- Flechas de navegación -->
            <template v-if="visibleArrowBtn">
                <div class="absolute left-4 lg:left-12 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black/30 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110 z-40"
                    title="Anterior" @click="toggleImg(false)">
                    <button class="w-6 h-6 flex justify-center items-center">
                        <Icon icon="ph:caret-left-duotone" class="text-xl" />
                    </button>
                </div>
                <div class="absolute right-4 lg:right-12 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black/30 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110 z-40"
                    title="Siguiente" @click="toggleImg(true)">
                    <button class="w-6 h-6 flex justify-center items-center">
                        <Icon icon="ph:caret-right-duotone" class="text-xl" />
                    </button>
                </div>
            </template>

            <!-- Barra top -->
            <div class="lg:hidden absolute top-0 left-0 w-full text-4xl bg-black/40 py-2 text-gray-300 z-40"
                v-if="showFilename && showToolbar">
                <section class="text-center text-lg px-3">
                    {{ currentImageName }}
                </section>
            </div>

            <!-- Barra de herramientas -->
            <div class="absolute bottom-0 lg:bottom-[10%] left-0 w-full lg:w-auto lg:left-1/2 lg:-translate-x-1/2 flex flex-col justify-center text-4xl lg:text-2xl
        bg-black/40 lg:rounded-2xl py-2 text-gray-300 z-40
        " v-if="showToolbar">
                <section class="flex gap-6 justify-center items-center px-4">
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="handleScale(-0.4, false)" title="Reducir Zoom">
                        <Icon icon="ph:magnifying-glass-minus-duotone" />
                    </button>
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="handleScale(0.4, false)" title="Aumentar zoom">
                        <Icon icon="ph:magnifying-glass-plus-duotone" />
                    </button>
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="initImgSize" title="Escalar a 1:1">
                        <Icon icon="ph:number-square-one-duotone" />
                    </button>
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="handleRotate(false)" title="Girar a la izquierda">
                        <Icon icon="ph:arrow-counter-clockwise-duotone" />
                    </button>
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="handleRotate(true)" title="Girar a la derecha">
                        <Icon icon="ph:arrow-clockwise-duotone" />
                    </button>

                    <a download :href="currentImageUrl" @click=""
                        class="cursor-pointer transition-transform duration-200 hover:scale-110 text-gray-200"
                        title="Descargar">
                        <Icon icon="ph:download-duotone" />
                    </a>
                </section>
                <section v-if="showFilename" class="hidden lg:block text-center text-lg px-3">
                    {{ currentImageName }}
                </section>
            </div>
        </div>
    </TransitionFade>
</template>

<script setup>
import { useEventListener } from '@vueuse/core'
import VueZoomable from 'vue-zoomable'
import 'vue-zoomable/dist/style.css'
import { useViewTimeTracking } from '@/composables/useViewTimeTracking.js'

const props = defineProps({
    show: { type: Boolean, default: false },
    url: { type: String, default: undefined },
    images: { type: Array, default: () => [] },
    index: { type: Number, default: 0 },
    showToolbar: { type: Boolean, default: true },
    showArrowBtn: { type: Boolean, default: true },
    showFilename: { type: Boolean, default: false },
    keyboard: { type: Boolean, default: true },
    escClose: { type: Boolean, default: true },
    showCloseBtn: { type: Boolean, default: true },
})

const emit = defineEmits(['close'])

// Inicializar composable de tracking de tiempo de visualización
const { startTracking, stopTracking } = useViewTimeTracking()

// Función para detectar si una imagen es una psicografía
const isPsicography = (imageUrl) => {
    if (!imageUrl) return false
    return imageUrl.includes('almacen/medios/psicografias/')
}

const vImagesWrap = ref(null)

// Estado de la imagen
const state = reactive({
    imgState: 'loading',
    src: '',
    imgIndex: props.index
})

// Variables para VueZoomable
const zoomLevel = ref(1)
const panPosition = ref({ x: 0, y: 0 })
const rotationAngle = ref(0)

const visibleArrowBtn = computed(() => props.images?.length > 1 && props.showArrowBtn)
const isMultiple = computed(() => props.images?.length > 1)
const currentImageUrl = ref()
const currentImageName = ref()

const loadImage = (url) => {
    if(typeof window === 'undefined') return // si es SSR no hacemos nada
    const img = new Image()
    img.src = url
    currentImageUrl.value = url.replace(/\?.*/, '')
    currentImageName.value = currentImageUrl.value.substr(url.lastIndexOf('/') + 1)
    return new Promise((resolve, reject) => {
        img.onload = () => resolve(url)
        img.onerror = () => reject(url)
    })
}

const onImageLoad = (event) => {
    // Ya no necesitamos capturar dimensiones
    console.log('Imagen cargada')
}

const handleKeyStroke = (e) => {
    if (!props.keyboard) return false
    if (!props.show) return false
    //e.preventDefault()

    const { key } = e
    if (['s', 'S', 'ArrowDown', '-'].includes(key)) { e.preventDefault(); return handleScale(-0.1, false) }
    if (['w', 'W', 'ArrowUp', '+'].includes(key)) { e.preventDefault(); return handleScale(0.1, false) }
    if (key === ' ') { e.preventDefault(); return initImgSize() }
    if (key === 'Escape' && props.escClose) return handleClose()
    if (['E', 'e'].includes(key)) return handleRotate(true)
    if (['Q', 'q'].includes(key)) return handleRotate(false)
    if (['a', 'A', 'ArrowLeft'].includes(key)) return toggleImg(false)
    if (['d', 'D', 'ArrowRight'].includes(key)) return toggleImg(true)
}

const initImg = () => {
    nextTick(() => {
        if (props.url !== undefined) {
            changeUrl(props.url)
            return
        }
        if (Array.isArray(props.images) && props.images?.length > 0) {
            changeUrl(props.images[state.imgIndex])
            return
        } else {
            // console.warn('images is not Array or Array length is 0')
        }
    })
}

const initImgSize = () => {
    console.log('IV: initImgSize')
    zoomLevel.value = 1
    panPosition.value = { x: 0, y: 0 }
    rotationAngle.value = 0
}

const handleRotate = (flag) => {
    rotationAngle.value += 90 * (flag ? 1 : -1)
}

const handleScale = (num, flag = false) => {
    const currentScale = zoomLevel.value
    let newScale = flag ? num : currentScale + num

    // Limitar el zoom mínimo y máximo
    if (newScale <= 0.1 && num < 0) return
    if (newScale >= 10 && num > 0) return

    zoomLevel.value = newScale
}

const changeUrl = (url) => {
    console.log('IV: changeUrl', url)

    // Detener tracking anterior
    stopTracking()

    state.imgState = 'loading'
    loadImage(url)
        .then(() => {
            state.imgState = 'success'
            state.src = url
            // Resetear transform cuando cambiamos de imagen
            nextTick(() => {
                initImgSize()
                // Iniciar tracking de la nueva imagen si es psicografía
                if (isPsicography(url)) {
                    startTracking('psicografia', currentImageName.value)
                    console.log('🔮 Iniciando tracking de psicografía:', currentImageName.value)
                }
            })
        })
        .catch(() => {
            state.imgState = 'error'
        })
}

const init = () => {
    nextTick(() => {
        // Ya no necesitamos configurar wheel zoom manualmente
        initImgSize()
        initImg()
    })
}

const handleClose = () => {
    // Detener tracking antes de cerrar
    stopTracking()

    state.visible = false
    emit('close')
    // props.on_unmount_v3_ima_preview_app?.()
}

const toggleImg = (flag) => {
    if (!isMultiple.value) return
    if(!props.images||!props.images.length) return
    if (flag) {
        state.imgIndex++
        if (state.imgIndex > props.images.length - 1) state.imgIndex = 0
    } else {
        state.imgIndex--
        if (state.imgIndex < 0) state.imgIndex = props.images.length - 1
    }
    changeUrl(props.images[state.imgIndex])
}

onMounted(() => {
    init()
    window.addEventListener('keydown', handleKeyStroke)
})

onBeforeUnmount(() => {
    // Detener tracking antes de desmontar el componente
    stopTracking()
    window.removeEventListener('keydown', handleKeyStroke)
})

watch(() => props.index, (index) => {
    if(!props.images||!props.images.length) return
    if (index >= 0 && index < props.images.length) {
        state.imgIndex = props.index
        changeUrl(props.images[state.imgIndex])
    }
})

// Watcher para cuando se oculta el componente
watch(() => props.show, (newShow, oldShow) => {
    if (oldShow && !newShow) {
        // El componente se está ocultando, detener tracking
        stopTracking()
    }
})


// arrastrar y redimensionar imagen - Manejado por zoompinch
// Todas las funciones de manejo de touch y mouse han sido removidas
// ya que zoompinch las maneja internamente

</script>

