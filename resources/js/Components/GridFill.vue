<template>
    <div class="grid justify-center"
        :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(${w}, 1fr))` }">
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        w: {
            type: [String, Number],
            default: '16rem'
        }
    }
}
</script>


<style scoped>
.grid:not(class*=[gap-]) {
    @apply gap-4;
}
</style>
