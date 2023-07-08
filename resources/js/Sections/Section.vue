<template>
    <div class="section" :style="styles">
        <slot></slot>
    </div>
</template>

<script setup>
const props = defineProps({
    height: {}
})

const styles = computed(() => {
    if (props.height)
        return { '--sectionHeight': props.height }
    return null
})
</script>
