<template>
    <div class="sections" :style="computedStyle">
        <slot></slot>
    </div>
</template>


<script setup>

const props = defineProps({
    height: {}
})

// Variable reactiva para forzar la actualización
const themeVersion = ref(0)

// Función para obtener colores del tema actual
const getThemeColors = () => {
    if (typeof document === 'undefined') return { base100: '#fff', base200: '#d4e3ff' }

    const root = document.documentElement
    const base100 = getComputedStyle(root).getPropertyValue('--color-base-100').replace(/['"]/g, '').trim() || '#d4e3ff'
    const base200 = getComputedStyle(root).getPropertyValue('--color-base-200').replace(/['"]/g, '').trim() || '#fff'

    return { base100, base200 }
}

// Función para generar SVG con color específico
const generateSvgUrl = (color) => {
    return `url("data:image/svg+xml,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%202000%20200'%20class%3D'section-divider'%3E%3Cpath%20d%3D'M0%200L2000%200L0%2030Z'%20fill%3D'${encodeURIComponent(color)}'%3E%3C%2Fpath%3E%3C%2Fsvg%3E")`
}

// Función para generar SVG invertido con color específico
const generateInvertedSvgUrl = (color) => {
    return `url("data:image/svg+xml,%3Csvg%20xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg'%20viewBox%3D'0%200%202000%20200'%20class%3D'section-divider'%3E%3Cpath%20d%3D'M0%200L2000%200L2000%2030Z'%20fill%3D'${encodeURIComponent(color)}'%3E%3C%2Fpath%3E%3C%2Fsvg%3E")`
}

const computedStyle = computed(() => {
    // Usar themeVersion para hacer que el computed sea reactivo a cambios de tema
    themeVersion.value

    const colors = getThemeColors()

    return {
        '--sectionHeight': props.height ? `${props.height}px` : '',
        '--divider-odd-svg': generateSvgUrl(colors.base100),
        '--divider-even-svg': generateInvertedSvgUrl(colors.base200),
    }
})

// Observar cambios en el tema
onMounted(() => {
    if (typeof document !== 'undefined') {
        const observer = new MutationObserver(() => {
            // Incrementar themeVersion para forzar recálculo del computed
            nextTick(() => {
                themeVersion.value++
            })
        })

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-theme']
        })

        onBeforeUnmount(() => {
            observer.disconnect()
        })
    }
})
</script>



<style>
@reference "../../css/app.css";

.sections>* {
    @apply w-full mx-auto;
}

.sections>*>* {
    @apply mx-auto;
}

.sections:not([white-first])>.section:nth-child(2n) {
    @apply bg-base-100;
}

.sections[white-first]>.section:nth-child(2n+1) {
    @apply bg-base-100;
}


/* Divisores diagonales con SVG embebido */
.sections > .section {
    position: relative;
}
.sections > .section::before {
    content: '';
    position: absolute;
    top: calc(100% - 2px);
    left: 0;
    right: 0;
    width: 100%;
    height: 30px;
    background-size: 100%;
    background-repeat: no-repeat;
    background-position: top;
    z-index: 1;
    pointer-events: none;
}


.sections > .section:nth-child(even)::before {
    background-image: var(--divider-even-svg);
}

/* pares */
.sections > .section:nth-child(odd)::before {
    background-image: var(--divider-odd-svg);
}

</style>
