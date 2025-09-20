<template>
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
  import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

  const { trackUserEngagement, trackViewTime } = useGoogleAnalytics()

  const imageSrc = ref('/almacen/medios/logos/SELLO_TRANSPARENTE_GRANDE.png');
  const isFullscreen = ref(false);
  const fullscreenStartTime = ref(null);

  function openFullscreen() {
    isFullscreen.value = true;
    fullscreenStartTime.value = Date.now();

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

  // Calcular tiempo de visualización
  if (fullscreenStartTime.value) {
    const viewTime = Date.now() - fullscreenStartTime.value;
    const viewTimeSeconds = Math.round(viewTime / 1000);

    // Tracking específico de tiempo de visualización
    trackViewTime('sello', 'sello de tseyor', viewTimeSeconds)

    // Tracking de cierre con tiempo de visualización
    trackUserEngagement('sello_fullscreen_close', `tiempo_visualización: ${viewTimeSeconds}s`)
    console.log('📺 Sello cerrado tras', viewTimeSeconds, 'segundos')

    fullscreenStartTime.value = null;
  }

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
})
  </script>

