<template>
    <div class="grid justify-center"
        :style="colWidth ? { 'grid-template-columns': `repeat(auto-fill, minmax(${colWidth}, 1fr))` } : {}">
        <slot></slot>
    </div>
</template>

<script>
export default {
    props: {
        colWidth: {
            type: [String, Number],
            default: '16rem'
        }
    }
}
</script>


<style scoped>
@reference "../../css/app.css";

.grid:not(class*=[gap-]) {
    @apply gap-4;
}
</style>
