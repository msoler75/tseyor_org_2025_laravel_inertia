<template>
    <div
        class="relative inset-0"
        role="region"
        aria-roledescription="carousel"
        aria-label="Carrusel de imagenes"
        tabindex="0"
        @mouseenter="pause"
        @mouseleave="play"
        @focus="pause"
        @blur="play"
        @keydown="handleKeydown"
    >
        <img
            v-for="(img, i) in images"
            :key="img"
            :src="img"
            :alt="alt"
            class="carousel-slide absolute inset-0 w-full h-full object-cover"
            :class="{ active: i === current }"
            :aria-current="i === current ? 'true' : 'false'"
        />
        <div
            v-if="gradient"
            class="absolute inset-0 bg-linear-to-t from-black/30 to-90% to-transparent pointer-events-none"
        />
        <div
            role="tablist"
            aria-label="Navegacion del carrusel"
            class="absolute bottom-4 left-0 right-0 flex justify-center gap-2 z-10"
        >
            <button
                v-for="(img, i) in images"
                :key="'d' + i"
                role="tab"
                :aria-selected="i === current"
                :aria-label="'Imagen ' + (i + 1) + ' de ' + images.length"
                class="carousel-dot rounded-full transition-all duration-300 min-w-[44px] min-h-[44px] flex items-center justify-center focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-1"
                :class="{ active: i === current }"
                @click="goTo(i)"
                @keydown.stop="handleDotKeydown($event, i)"
            />
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    images: { type: Array, required: true },
    alt: { type: String, default: '' },
    interval: { type: Number, default: 4000 },
    gradient: { type: Boolean, default: true },
})

const current = ref(0)
let timer = null
let prefersReducedMotion = false

function goTo(index) {
    current.value = index
}

function next() {
    current.value = (current.value + 1) % props.images.length
}

function prev() {
    current.value = (current.value - 1 + props.images.length) % props.images.length
}

function pause() {
    if (timer) {
        clearInterval(timer)
        timer = null
    }
}

function play() {
    if (prefersReducedMotion) return
    if (timer) return
    timer = setInterval(next, props.interval)
}

function handleKeydown(e) {
    if (e.key === 'ArrowLeft') {
        e.preventDefault()
        prev()
    } else if (e.key === 'ArrowRight') {
        e.preventDefault()
        next()
    }
}

function handleDotKeydown(e, index) {
    if (e.key === 'ArrowLeft') {
        e.preventDefault()
        prev()
    } else if (e.key === 'ArrowRight') {
        e.preventDefault()
        next()
    } else if (e.key === 'Home') {
        e.preventDefault()
        goTo(0)
    } else if (e.key === 'End') {
        e.preventDefault()
        goTo(props.images.length - 1)
    }
}

onMounted(() => {
    prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    const mql = window.matchMedia('(prefers-reduced-motion: reduce)')
    const handler = (e) => {
        prefersReducedMotion = e.matches
        if (e.matches) {
            pause()
        } else {
            play()
        }
    }
    mql.addEventListener('change', handler)

    if (!prefersReducedMotion) {
        play()
    }
})

onBeforeUnmount(() => {
    pause()
})
</script>

<style scoped>
.carousel-slide {
    opacity: 0;
    transition: opacity 1s ease;
    pointer-events: none;
}
.carousel-slide.active {
    opacity: 1;
    pointer-events: auto;
}

.carousel-dot::after {
    content: '';
    display: block;
    width: 8px;
    height: 8px;
    border-radius: 9999px;
    background: rgb(255 255 255 / 0.6);
    transition: width 300ms ease;
}

.carousel-dot.active::after {
    width: 32px;
    background: white;
}

@media (prefers-reduced-motion: reduce) {
    .carousel-slide {
        transition: opacity 0.01s ease;
    }
    .carousel-dot::after {
        transition: none;
    }
}
</style>
