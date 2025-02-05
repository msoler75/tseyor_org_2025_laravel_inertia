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

  const imageSrc = ref('/almacen/medios/logos/SELLO_TRANSPARENTE_GRANDE.png');
  const isFullscreen = ref(false);

  function openFullscreen() {
    isFullscreen.value = true;
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
  isFullscreen.value = false;
}
  </script>

