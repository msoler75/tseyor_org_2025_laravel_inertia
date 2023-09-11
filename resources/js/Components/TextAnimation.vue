<template>
    <div class="text-container" ref="container">
        <div class="text-wrapper">
            <div class="text opacity-0" ref="textEl">{{ text }}</div>
            <div class="text-clone" ref="textCloneEl" v-html="textRep"
            :class="containerWidth>0 ? 'animate':''" />
        </div>
    </div>
</template>

<script setup>
import { useMutationObserver } from '@vueuse/core'

const container = ref(null)
const textEl = ref(null)
const textCloneEl = ref(null)
const containerWidth = ref(0)


const props = defineProps({
    text: {
        type: String,
        required: true
    },
    animationDuration: {
        type: Number,
        default: 7
    }
})

const repetitions = 77

const textRep = computed(() => {
    var t = props.text
    for (var i = 0; i < repetitions; i++) {
        t += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + props.text
    }
    return t
})

const calculateDuration = () => {
    containerWidth.value = container.value.offsetWidth
    const textWidth = textEl.value.offsetWidth
    const animationDuration = (textWidth / containerWidth.value) * props.animationDuration / 1.2 * repetitions
    textEl.value.style.setProperty('--animation-duration', `${animationDuration}s`)
    textCloneEl.value.style.setProperty('--animation-duration', `${animationDuration}s`)
    console.log('animate-duration:', animationDuration, { textWidth, containerWidth, container })
}

watch(() => props.text, () => { calculateDuration() })

onMounted(() => {
    calculateDuration()
    window.addEventListener('resize', calculateDuration)
})

onBeforeUnmount(() => {
    window.removeEventListener('resize', calculateDuration)
})


useMutationObserver(container, (mutations) => {
    if (mutations[0])
        checkWidth()
}, {
    attributes: true,
})


function checkWidth() {
    const newWidth = container.value.offsetWidth
    console.log('checkWidth', newWidth)
    if (newWidth != containerWidth.value)
        calculateDuration()
    containerWidth.value = newWidth
}
</script>

<style scoped>
.text-container {
    overflow: hidden;
    white-space: nowrap;
}

.text-wrapper {
    position: relative;
    display: inline-block;
    animation: slide linear infinite;
}

.text,
.text-clone {
    display: inline-block;
    white-space: nowrap;
}

.text-clone {
    position: absolute;
    left: 0;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    animation-duration: var(--animation-duration);
    animation-delay: 2s;
}

.animate {
    animation-name: slide-text;
}

@keyframes slide-text {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-100%);
    }
}
</style>
