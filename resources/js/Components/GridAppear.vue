<template>
    <div ref="el" class="grid justify-center"
        :style="colWidth?{ 'grid-template-columns': `repeat(auto-fill, minmax(${colWidth}, 1fr))` }:{}">
        <slot></slot>
</div>
</template>

<script setup>
    defineProps({
        colWidth: {
            type: [String, Number],
            default: null
        }
    })


const el = ref(null)

onMounted(()=>{
    var d = 0.0;
    console.log('EL', {el})
    for(let i = 0; i < el.value.children.length; i++)
    {
        el.value.children[i].style.setProperty('--a-delay', d + 's')
        d+=0.04
    }
    el.value.classList.add('appear')
})
</script>


<style scoped>
.grid:not(class*=[gap-]) {
    @apply gap-4;
}
.grid>>>*
{
    opacity: 0;
}

.grid.appear >>> * {
    --a-delay : 0;
    animation: appear .2s;
    animation-delay: var(--a-delay);
    animation-fill-mode: forwards;
    animation-timing-function: ease_out;
}

@keyframes appear {
    0% {opacity: 0; transform: translateY(10rem)}
    100% {opacity: 1; tranform: none}
}
</style>
