<template>
    <div class="text-container" ref="container">
        <div class="text-wrapper">
            <div class="text opacity-0" ref="textEl">{{ text }}</div>
            <div class="text-clone" ref="textCloneEl" v-html="textRep"/>
        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount } from 'vue'
const container = ref(null)
const textEl = ref(null)
const textCloneEl = ref(null)

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

const repetitions = 777

const textRep = computed(() => {
    var t = props.text
    for (var i = 0; i < repetitions; i++) {
        t += "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + props.text
    }
    return t
})

const calculateDuration = () => {
    const containerWidth = container.value.offsetWidth
    const textWidth = textEl.value.offsetWidth
    const animationDuration = (textWidth / containerWidth) * props.animationDuration / 1.2 * repetitions
    textEl.value.style.setProperty('--animation-duration', `${animationDuration}s`)
    textCloneEl.value.style.setProperty('--animation-duration', `${animationDuration}s`)
}

watch(() => props.text, () => { calculateDuration() })

onMounted(() => {
    calculateDuration()
    window.addEventListener('resize', calculateDuration)
})

onBeforeUnmount(() => {
    window.removeEventListener('resize', calculateDuration)
})
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
    animation-name: slide-text;
    position: absolute;
    left: 0;
    animation-delay: 0s;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    animation-duration: var(--animation-duration);
    animation-delay: 5s;
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
