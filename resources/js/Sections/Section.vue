<template>
    <div class="section" :style="computedStyle">
        <slot></slot>
    </div>
</template>

<script setup>
const props = defineProps({
    height: {}
})

const computedStyle = computed(() => {
    if(props.height)
    return {
        '--sectionHeight': props.height ? `${props.height}px` : ''
    }
    else
    return {}
})
</script>
