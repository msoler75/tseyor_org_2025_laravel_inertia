<template>
    <!-- App layout -->
     <TransitionFade>
     <div v-if="ui.tools.mostrarTools" class="bottom-toolbar text-4xl fixed right-7 z-40 flex flex-col justify-center items-center gap-2"
      :class="
      [
          ui.folderExplorer.seleccionando
                ? 'bottom-20'
                : ui.player.audioClosed
                ? 'bottom-7'
                : ui.player.expanded
                ? 'bottom-24'
                : 'bottom-14'
        ]">

        <!-- icono de scroll to top -->
        <Icon
            icon="ph:arrow-circle-up-duotone"
            @click="nav.scrollToTopSmart('smooth')"
        />

        <!-- Icono de búsqueda -->
        <Icon v-if="ui.tools.hayContenido"
            icon="ph:magnifying-glass-duotone"
            @click="ui.tools.toggleSearch()"
        />

     </div>
     </TransitionFade>
</template>

<script setup>

const ui = useUi()
const nav = ui.nav

watch(()=>nav.navigating, (value) => {
    if (value) {
        ui.tools.closeTools()
    }
})

onMounted(() => {
   ui.tools.detectContent()
})

</script>

<style scoped>
/* reference to ../../css/app.css handled by build pipeline */

:deep(.bottom-toolbar > *) {
    width: 48px;
    height: 48px;
    display: flex;
    justify-content: center;
    align-items: center;
    outline: 2px solid transparent;
    cursor: pointer;
    transition: color 100ms, opacity 100ms;
}
:deep(.bottom-toolbar > *):hover {
    color: #f97316; /* approximate Tailwind orange-500 */
}
</style>
