<template>
    <Sections class="snap-mandatory snap-y overflow-y-scroll h-screen scroll-smooth" ref="container">
        <slot></slot>
    </Sections>
</template>

<script setup>
import { onBeforeUnmount } from 'vue'
import { useNav } from '@/Stores/nav'

const container = ref(null)

const nav = useNav()
nav.scrollY = 0
// const inUpperPage = computed(()=>scrollY.value<screen.height*.7)

const handleScroll = () => {
    nav.scrollY = container.value.$el.scrollTop;
    // Hacer algo con la posición actual del scroll Y, como actualizar una variable reactiva
};

onMounted(() => {
    // primer establecimiento de valor
    handleScroll()
    // Agregar un evento para el desplazamiento de la ventana
    container.value.$el.addEventListener('scroll', handleScroll, { passive: true });
    // marcamos que estamos en modo "full page" para que la barra de navegación sea flotante
    nav.fullPage = true
});



onBeforeUnmount(() => {
    container.value.$el.removeEventListener('scroll', handleScroll);
    nav.fullPage = false
});

</script>


<style scoped>
:deep(.section) {
    @apply h-screen snap-center flex flex-col justify-center;
}
</style>
