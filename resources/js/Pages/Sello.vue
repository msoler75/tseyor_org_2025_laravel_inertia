<template>
    <div class="bg-base-100 w-full pt-4 lg:pt-12 pb-10">
        <div class="px-2 max-w-[120ch] w-full mx-auto flex justify-between ">
            <span />
            <Share />
    </div>
    </div>

    <div class="bg-base-100 p-5 pb-20" v-if="!isFullscreen">


      <h1>Sello de Tseyor</h1>
      <p>Pulsa en el sello para verlo a pantalla completa.</p>
      <img
        :src="imageSrc"
        class="mx-auto cursor-pointer w-full sm:w-[500px]"
        @click="openFullscreen"
      />
    </div>
    <!-- Pantalla completa -->
    <div
      v-else
      class="fixed top-0 left-0 w-full h-full bg-base-100 flex justify-center items-center z-50"
    >
      <img
        :src="imageSrc"
        class="max-w-full max-h-full object-contain py-4"
        @click="closeFullscreen"
      />
    </div>
  </template>

  <script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'
  import { useViewTimeTracking } from '@/composables/useViewTimeTracking.js'

  const { trackUserEngagement } = useGoogleAnalytics()
  const viewTimeTracker = useViewTimeTracking()
  const { startTracking, stopTracking } = viewTimeTracker

  const imageSrc = ref('/almacen/medios/logos/SELLO_TRANSPARENTE_GRANDE.png');
  const isFullscreen = ref(false);

  function openFullscreen() {
    isFullscreen.value = true;

    // Iniciar tracking de tiempo de visualización
    startTracking('sello', 'sello de tseyor')

    // Tracking de apertura de pantalla completa
    trackUserEngagement('sello_fullscreen_open', 'sello de tseyor')
    console.log('📺 Sello abierto en pantalla completa')

    document.documentElement.requestFullscreen();
  }

  async function closeFullscreen() {
  if (document.fullscreenElement) {
    try {
      await document.exitFullscreen();
    } catch (error) {
      console.error('Error al salir del modo pantalla completa:', error);
    }
  }

  // Detener tracking (automáticamente envía el tiempo)
  stopTracking()

  // Tracking de cierre
  trackUserEngagement('sello_fullscreen_close', 'cerrado por usuario')
  console.log('📺 Sello cerrado')

  isFullscreen.value = false;
}

// Listener para detectar salida de pantalla completa por otros métodos (ESC, etc.)
const handleFullscreenChange = () => {
  if (!document.fullscreenElement && isFullscreen.value) {
    // El usuario salió de pantalla completa por otro método (ESC, etc.)
    closeFullscreen();
  }
}

onMounted(() => {
  document.addEventListener('fullscreenchange', handleFullscreenChange);
})

onUnmounted(() => {
  document.removeEventListener('fullscreenchange', handleFullscreenChange);
  // stopTracking se encarga de limpiar automáticamente los listeners
  stopTracking()
})
  </script>

