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
.grid {
    gap: 4px;
}
</style>
