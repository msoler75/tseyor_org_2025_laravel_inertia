<template>
    <TransitionFade>
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30 backdrop-blur-md text-gray-200"
            v-show="show && images?.length" ref="vImagesWrap">
            <!-- Loading -->
            <span class=" loading loading-spinner loading-lg" v-show="state.imgState === 'loading'"
                aria-hidden="true"></span>

            <div ref="imgContainer" :style="dragStyle"
                class="fixed left-0 top-0 w-full h-full flex items-center justify-center"
                style="touch-action: none"
                >

                <!-- Imagen cargada con éxito -->
                <img v-show="state.imgState === 'success'"
                    class="max-w-full max-h-full cursor-move transition-transform duration-200"
                     :src="state.src"
                    :style="`transform: scale(${style.imgScale}) rotate(${style.imgRotate}deg);`" alt="" />

                <!-- Error de carga de imagen -->
                <div v-show="state.imgState === 'error'" class="text-gray-700"
                    aria-hidden="true" :style="`transform: scale(${style.imgScale}) rotate(${style.imgRotate}deg);`">
                    <Icon icon="ph:image-broken-duotone" class="text-9xl" />
                </div>
            </div>

            <!-- Botón de cierre -->
            <button
                class="z-30 text-2xl absolute flex justify-center items-center top-1 md:top-12 right-4 md:right-12 w-9 h-9 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                :class="!showFilename ? 'bg-black bg-opacity-30' : 'md:bg-black md:bg-opacity-30'"
                aria-hidden="true" @click="handleClose" v-if="showCloseBtn">
                <Icon icon="material-symbols-light:close" />
            </button>

            <!-- Flechas de navegación -->
            <template v-if="visibleArrowBtn">
                <div class="absolute left-4 md:left-12 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black bg-opacity-30 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                    title="Anterior" @click="toggleImg(false)">
                    <button class="w-6 h-6 flex justify-center items-center">
                        <Icon icon="ph:caret-left-duotone" class="text-xl" />
                    </button>
                </div>
                <div class="absolute right-4 md:right-12 top-1/2 -translate-y-1/2 w-10 h-10 flex items-center justify-center bg-black bg-opacity-30 rounded-full cursor-pointer transition-transform duration-200 hover:scale-110"
                    title="Siguiente" @click="toggleImg(true)">
                    <button class="w-6 h-6 flex justify-center items-center">
                        <Icon icon="ph:caret-right-duotone" class="text-xl" />
                    </button>
                </div>
            </template>

            <!-- Barra top -->
            <div class="md:hidden absolute top-0 left-0 w-full text-4xl bg-black bg-opacity-40 py-2 text-gray-300"
                v-if="showFilename && showToolbar">
                <section class="text-center text-lg px-3">
                    {{ currentImageName }}
                </section>
            </div>

            <!-- Barra de herramientas -->
            <div class="absolute bottom-0 md:bottom-[10%] left-0 w-full md:w-auto md:left-1/2 md:-translate-x-1/2 flex flex-col justify-center text-4xl md:text-2xl
        bg-black bg-opacity-40 md:rounded-2xl py-2 text-gray-300
        " v-if="showToolbar">
                <section class="flex gap-6 justify-center items-center px-4">
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="handleScale(-0.1, false)" title="Reducir Zoom">
                        <Icon icon="ph:magnifying-glass-minus-duotone" />
                    </button>
                    <button class="cursor-pointer transition-transform duration-200 hover:scale-110" aria-hidden="true"
                        @click="handleScale(0.1, false)" title="Aumentar zoom">
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
                <section v-if="showFilename" class="hidden md:block text-center text-lg px-3">
                    {{ currentImageName }}
                </section>
            </div>
        </div>
    </TransitionFade>
</template>

<script setup>
import { useDraggable, useEventListener } from '@vueuse/core'

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
const disabled = ref(false)
const { x, y, style: dragStyle } = useDraggable(imgContainer, {
  preventDefault: true,
  disabled,
})

const state = reactive({
    imgState: 'loading',
    src: '',
    imgIndex: props.index
})

const style = reactive({
    imgScale: 1,
    imgRotate: 0
})

const visibleArrowBtn = computed(() => props.images?.length > 1 && props.showArrowBtn)
const isMultiple = computed(() => props.images?.length > 1)
const currentImageUrl = ref()
const currentImageName = ref()

const loadImage = (url) => {
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
    if (['s', 'S', 'ArrowDown', '-'].includes(key)) {e.preventDefault(); return handleScale(-0.1, false)}
    if (['w', 'W', 'ArrowUp', '+'].includes(key)) {e.preventDefault(); return handleScale(0.1, false)}
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
        if (Array.isArray(props.images) && props.images.length > 0) {
            return changeUrl(props.images[state.imgIndex])
        } else {
            console.error('images is not Array or Array length is 0')
        }
    })
}

const initImgSize = () => {
    console.log('IV: initImgSize')
    style.imgScale = 1
    style.imgRotate = 0
    x.value = 0
    y.value = 0
    if (imgContainer.value) {
        imgContainer.value.style.top = '0'
        imgContainer.value.style.left = '0'
    }
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
    if (index >= 0 && index < props.images.length) {
        state.imgIndex = props.index
        changeUrl(props.images[state.imgIndex])
    }
})
</script>

<style scoped></style>
