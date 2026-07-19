<template>
    <div ref="el" class="grid grid-appear justify-center"
        :style="colWidth ? { 'grid-template-columns': `repeat(auto-fill, minmax(min(${colWidth}, 100%), 1fr))` } : {}">
        <slot></slot>
    </div>
</template>

<script setup>
const props = defineProps({
    colWidth: {
        type: [String, Number],
        default: null
    }
    ,
    timeLapse: {
        type: Number,
        default: 0.04
    }
})


const el = ref(null)

function triggerAnimation() {
    if (!el.value) return
    el.value.classList.remove('appear')
    var d = 0.0
    for (let i = 0; i < el.value.children.length; i++) {
        el.value.children[i].style.setProperty('--a-delay', d + 's')
        d += props.timeLapse
    }
    // Doble rAF: el primero procesa la eliminación de .appear y los CSS vars,
    // el segundo añade .appear para que las animaciones arranquen con sus delays
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            el.value?.classList.add('appear')
        })
    })
}

let observer = null

onMounted(() => {
    triggerAnimation()
    if (!el.value) return
    observer = new MutationObserver(() => {
        triggerAnimation()
    })
    observer.observe(el.value, { childList: true, subtree: false })
})

onBeforeUnmount(() => {
    observer?.disconnect()
})
</script>


<style>

.grid-appear>* {
    opacity: 0;
}

.grid-appear.appear>* {
    --a-delay: 0;
    animation: appear .2s;
    animation-delay: var(--a-delay);
    animation-fill-mode: forwards;
    animation-timing-function: ease_out;
}

@keyframes appear {
    0% {
        opacity: 0;
        transform: translateY(10rem)
    }

    100% {
        opacity: 1;
        transform: none
    }
}</style>
