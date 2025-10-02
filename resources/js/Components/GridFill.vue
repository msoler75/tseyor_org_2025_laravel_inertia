<template>
    <div class="grid justify-center"
        :style="colWidth ? { 'grid-template-columns': `repeat(auto-fill, minmax(min(${colWidth}, 100%), 1fr))` } : {}">
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
.grid:not([class*="gap-"]) {
    gap: 4px;
}
</style>
