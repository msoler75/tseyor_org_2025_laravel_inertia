<template>
    <div class="grid gap-4 justify-center"
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
