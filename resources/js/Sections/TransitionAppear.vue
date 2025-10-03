<template>
    <div :style="cssVars" class="transition-container">
        <transition name="scale">
            <slot/>
        </transition>
    </div>
</template>

<script setup>
const props = defineProps({
    duration: {
        type: Number,
        default: 300
    },
    origin: {
        type: String,
        default: 'left bottom',
        // cualquiera de las palabras clave de CSS para transform-origin
        // ejemplos: center; top left; top center; bottom right; center center;50px 20px; 50% 50% ...
    }
})

const cssVars = computed(() => {
    const vars = {
        '--scale-duration': `${props.duration}ms`,
        '--scale-origin': props.origin
    }
    console.log('TransitionAppear cssVars:', vars) // Debug
    return vars
})
</script>

<style scoped>
.transition-container {
    /* Eliminar valores por defecto que interfieren con las variables dinámicas */
    transform-origin: var(--scale-origin, 50% right );
}

.scale-enter-active,
.scale-leave-active {
    transition: transform var(--scale-duration, 300ms) ease-out;
    /* Prevenir borrosidad durante y después de la animación */
    backface-visibility: hidden;
    transform-style: preserve-3d;
    will-change: transform;
}

.scale-enter-from,
.scale-leave-to {
    transform: scale(0);
}

.scale-enter-to,
.scale-leave-from {
    transform: scale(1);
    /* Asegurar renderizado nítido al finalizar la animación */
    backface-visibility: visible;
    transform: scale(1) translateZ(0);
}
</style>

