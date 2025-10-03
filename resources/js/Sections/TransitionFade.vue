<template>
    <div :style="cssVars">
        <transition name="fade">
            <slot/>
        </transition>
    </div>
</template>

<script setup>
const props = defineProps({
    duration: {
        type: Number,
        default: 300
    }
})

const cssVars = computed(() => ({
    '--fade-duration': `${props.duration}ms`
}))

</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  /* Fallback para navegadores sin soporte de CSS custom properties */
  transition: opacity 300ms ease-out;
  /* Soporte moderno con variables CSS */
  transition: opacity var(--fade-duration, 300ms) ease-out;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

