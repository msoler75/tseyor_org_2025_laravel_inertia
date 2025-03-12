<template>
    <TransitionFade>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30 backdrop-blur-md text-gray-200 touch-none"
            v-show="show && images?.length" ref="vImagesWrap">
            <!-- Loading -->
            <span class=" loading loading-spinner loading-lg" v-show="state.imgState === 'loading'"
                aria-hidden="true"></span>

            <div ref="imgContainer" class="fixed left-0 top-0 w-full h-full flex items-center justify-center"
                @touchstart="handleTouchStart" @touchmove="handleTouchMove" @touchend="handleTouchEnd"
                @mousedown.prevent="handleMouseDown" @mousemove="handleMouseMove" @mouseup="handleMouseUp" :style="{
                    transform: `scale(${style.imgScaleTouch})`,
                    left: position.x + 'px',
                    top: position.y + 'px',
                }">

                <!-- Imagen cargada con éxito -->
                <img v-show="state.imgState === 'success'"
                    class="max-w-full max-h-full cursor-move transition-transform duration-200" :src="state.src"
                    :style="`transform: scale(${style.imgScale}) rotate(${style.imgRotate}deg);`" alt="" />

                <!-- Error de carga de imagen -->
                <div v-show="state.imgState === 'error'" class="text-gray-700" aria-hidden="true"
                    :style="`transform: scale(${style.imgScale}) rotate(${style.imgRotate}deg);`">
                    <Icon icon="ph:image-broken-duotone" class="text-9xl pepet" />
                </div>
            </div>

            <!-- Botón de cierre -->
            <button
                class="z-30 text-2xl absolute flex justify-center items-center top-1 lg:top-12 right-4 lg:right-12 w-9 h-9 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                :class="!showFilename ? 'bg-black bg-opacity-30' : 'lg:bg-black lg:bg-opacity-30'" aria-hidden="true"
                @click="handleClose" v-if="showCloseBtn">
                <Icon icon="material-symbols-light:close" />
            </button>

            <!-- Flechas de navegación -->
            <template v-if="visibleArrowBtn">
                <div class="absolute left-4 lg:left-12 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black bg-opacity-30 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                    title="Anterior" @click="toggleImg(false)">
                    <button class="w-6 h-6 flex justify-center items-center">
                        <Icon icon="ph:caret-left-duotone" class="text-xl" />
                    </button>
                </div>
                <div class="absolute right-4 lg:right-12 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black bg-opacity-30 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                    title="Siguiente" @click="toggleImg(true)">
                    <button class="w-6 h-6 flex justify-center items-center">
                        <Icon icon="ph:caret-right-duotone" class="text-xl" />
                    </button>
                </div>
            </template>

            <!-- Barra top -->
            <div class="lg:hidden absolute top-0 left-0 w-full text-4xl bg-black bg-opacity-40 py-2 text-gray-300"
                v-if="showFilename && showToolbar">
                <section class="text-center text-lg px-3">
                    {{ currentImageName }}
                </section>
            </div>

            <!-- Barra de herramientas -->
            <div class="absolute bottom-0 lg:bottom-[10%] left-0 w-full lg:w-auto lg:left-1/2 lg:-translate-x-1/2 flex flex-col justify-center text-4xl lg:text-2xl
        bg-black bg-opacity-40 lg:rounded-2xl py-2 text-gray-300
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
    // on_unmount_v3_ima_preview_app: { type: Function }
})

const emit = defineEmits(['close'])

const vImagesWrap = ref(null)
const imgContainer = ref(null)
// const disabled = ref(false)
/*const { x, y, style: dragStyle } = useDraggable(imgContainer, {
    preventDefault: true,
    disabled,
})*/



const state = reactive({
    imgState: 'loading',
    src: '',
    imgIndex: props.index
})

const position = reactive({ x: 0, y: 0 });
const style = reactive({
    imgScale: 1,
    imgScaleTouch: 1,
    imgRotate: 0
})

let initialDistance = null;
let initialMidpoint = { x: 0, y: 0 };
let baseScale = 1
let isDragging = false;
let lastTouchX = 0;
let lastTouchY = 0;

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

const handleKeyStroke = (e) => {
    if (!props.keyboard) return false
    if (!props.show) return false
    //e.preventDefault()

    const { key } = e
    if (['s', 'S', 'ArrowDown', '-'].includes(key)) { e.preventDefault(); return handleScale(-0.1, false) }
    if (['w', 'W', 'ArrowUp', '+'].includes(key)) { e.preventDefault(); return handleScale(0.1, false) }
    if (key === ' ') return initImgSize()
    if (key === 'Escape' && props.escClose) return handleClose()
    if (['E', 'e'].includes(key)) return handleRotate(true)
    if (['Q', 'q'].includes(key)) return handleRotate(false)
    if (['a', 'A', 'ArrowLeft'].includes(key)) return toggleImg(false)
    if (['d', 'D', 'ArrowRight'].includes(key)) return toggleImg(true)
}

const initImg = () => {
    nextTick(() => {
        if (props.url !== undefined) return changeUrl(props.url)
        if (Array.isArray(props.images) && props.images?.length > 0) {
            return changeUrl(props.images[state.imgIndex])
        } else {
            // console.warn('images is not Array or Array length is 0')
        }
    })
}

const initImgSize = () => {
    console.log('IV: initImgSize')
    style.imgScale = 1;
    style.imgScaleTouch = 1;
    style.imgRotate = 0;
    position.x = 0;
    position.y = 0;
    simulateTouches = []
}


const handleRotate = (flag) => {
    style.imgRotate += 90 * (flag ? 1 : -1)
}

const handleScale = (num, flag = false) => {
    if (style.imgScale <= 0.2 && num < 0) return
    if (flag) {
        style.imgScale = num
    } else {
        style.imgScale += num
    }
}

const handleScroll = (e) => {
    e.preventDefault()
    handleScale(e.deltaY < 0 ? 0.05 : -0.05)
}

const changeUrl = (url) => {
    console.log('IV: changeUrl', url)
    state.imgState = 'loading'
    loadImage(url)
        .then(() => {
            state.imgState = 'success'
            state.src = url
            initImgSize()
        })
        .catch(() => {
            state.imgState = 'error'
        })
}

const init = () => {
    nextTick(() => {
        useEventListener(vImagesWrap.value, 'mousewheel', handleScroll, false)
        initImgSize()
        initImg()
    })
}

const handleClose = () => {
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
    window.removeEventListener('keydown', handleKeyStroke)
})

watch(() => props.index, (index) => {
    if(!props.images||!props.images.length) return
    if (index >= 0 && index < props.images.length) {
        state.imgIndex = props.index
        changeUrl(props.images[state.imgIndex])
    }
})


// arrastrar y redimensionar imagen


function handleTouchStart(event) {
    if (event.touches.length === 1) {
        isDragging = true;
        const touch = event.touches[0];
        lastTouchX = touch.clientX;
        lastTouchY = touch.clientY;
    } else if (event.touches.length === 2) {
        setInitialZoom(event.touches)
    }
}


function handleTouchMove(event) {
    event.preventDefault(); // Prevenir el scroll del navegador

    if (isDragging && event.touches.length === 1) {
        const touch = event.touches[0];
        const deltaX = touch.clientX - lastTouchX;
        const deltaY = touch.clientY - lastTouchY;

        position.x += deltaX;
        position.y += deltaY;

        lastTouchX = touch.clientX;
        lastTouchY = touch.clientY;
    } else if (event.touches.length === 2 && initialDistance) {
        setScalingZoom(event.touches)
    }
}

function handleTouchEnd() {
    isDragging = false;
    initialDistance = null;
    initialMidpoint = { x: 0, y: 0 };
}


let simulateTouches = []

function handleMouseDown(event) {
    // si está la tecla Ctrl pulsada:
    if (event.ctrlKey) {
        // no es touch:
        simulateTouches = [{clientX:event.clientX, clientY:event.clientY}]
        return;
    }
    if (event.button === 0) {

        if(simulateTouches.length > 0) {
            simulateTouches.push({clientX:event.clientX, clientY:event.clientY})
            setInitialZoom(simulateTouches)
            return;
        }

        isDragging = true;
        // no es touch:
        lastTouchX = event.clientX;
        lastTouchY = event.clientY;
    }
}

function handleMouseMove(event) {
    if (isDragging) {
        const deltaX = event.clientX - lastTouchX;
        const deltaY = event.clientY - lastTouchY;

        position.x += deltaX;
        position.y += deltaY;

        lastTouchX = event.clientX;
        lastTouchY = event.clientY;
    } else if(simulateTouches.length > 1) {
        simulateTouches[1].clientX = event.clientX
        simulateTouches[1].clientY = event.clientY
        setScalingZoom(simulateTouches)
    }
}

function handleMouseUp() {
    isDragging = false;
    if(simulateTouches.length==2)
        simulateTouches = []
}





// operaciones de Zoom
function setInitialZoom(touches) {
    isDragging = false;
    baseScale = style.imgScaleTouch;
    const [touch1, touch2] = touches;
    initialDistance = Math.hypot(
        touch2.clientX - touch1.clientX,
        touch2.clientY - touch1.clientY
    );

    initialMidpoint = {
        x: (touch1.clientX + touch2.clientX) / 2,
        y: (touch1.clientY + touch2.clientY) / 2
    };
}

function setScalingZoom(touches) {

    const [touch1, touch2] = touches;
    const currentDistance = Math.hypot(
        touch2.clientX - touch1.clientX,
        touch2.clientY - touch1.clientY
    );


    const currentMidpoint = {
        x: (touch1.clientX + touch2.clientX) / 2,
        y: (touch1.clientY + touch2.clientY) / 2
    };


    const newScale = baseScale * (currentDistance / initialDistance);

    style.imgScaleTouch = newScale


    // calculo de la posición del centro del movimiento touch

    // const scaleFactor = newScale / style.imgScaleTouch;

    // Calcular el desplazamiento basado en el punto medio
    // const dx = (initialMidpoint.x - touch2.clientX) / scaleFactor;
    // const dy = (initialMidpoint.y - touch2.clientY) / scaleFactor;

    // position.x -= 0.01*dx;
    // position.y -=  0.01*dy;

    // Ajustar la posición basada en el movimiento del punto medio
    //position.x += currentMidpoint.x - initialMidpoint.x;
    //position.y += currentMidpoint.y - initialMidpoint.y;


    console.log({x: position.x, y: position.y, scale: style.imgScale, scaleTouch: style.imgScaleTouch})
}

</script>

