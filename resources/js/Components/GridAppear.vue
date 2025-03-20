<template>
    <div ref="el" class="grid grid-appear justify-center"
        :style="colWidth ? { 'grid-template-columns': `repeat(auto-fill, minmax(${colWidth}, 1fr))` } : {}">
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

onMounted(() => {
    var d = 0.0;
    console.log('EL', { el }, props.timeLapse)
    for (let i = 0; i < el.value.children.length; i++) {
        el.value.children[i].style.setProperty('--a-delay', d + 's')
        d += props.timeLapse
    }
    el.value.classList.add('appear')
})
</script>


<style>

.grid-appear:not(class*=[gap-]) {
    gap:5px;
}

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
