<template>
  <!-- Notificación de nueva versión disponible -->
  <Transition name="notification">
    <div
      v-if="updateAvailable"
      class="fixed top-4 right-4 z-50 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg max-w-sm"
    >
      <div class="flex items-center gap-3">
        <Icon icon="mdi:update" class="text-xl" />
        <div class="flex-1">
          <p class="font-medium">Nueva versión disponible</p>
          <p class="text-sm text-blue-100">Se aplicará automáticamente</p>
        </div>
        <button
          @click="updateAvailable = false"
          class="text-blue-200 hover:text-white"
        >
          <Icon icon="mdi:close" />
        </button>
      </div>
    </div>
  </Transition>

  <!-- Notificación de app lista offline -->
  <Transition name="notification">
    <div
      v-if="offlineReady"
      class="fixed top-4 right-4 z-50 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg max-w-sm"
    >
      <div class="flex items-center gap-3">
        <Icon icon="mdi:wifi-off" class="text-xl" />
        <div class="flex-1">
          <p class="font-medium">¡App lista offline!</p>
          <p class="text-sm text-green-100">Funciona sin conexión</p>
        </div>
        <button
          @click="offlineReady = false"
          class="text-green-200 hover:text-white"
        >
          <Icon icon="mdi:close" />
        </button>
      </div>
    </div>
  </Transition>

  <!-- Banner de instalación -->
  <Transition name="notification">
    <div
      v-if="showInstallPrompt"
      class="fixed bottom-4 left-4 right-4 z-50 bg-indigo-600 text-white p-4 rounded-lg shadow-lg mx-auto max-w-md"
    >
      <div class="flex items-center gap-3">
        <Icon icon="mdi:cellphone-arrow-down" class="text-2xl" />
        <div class="flex-1">
          <p class="font-medium">¡Instala Tseyor!</p>
          <p class="text-sm text-indigo-100">Acceso rápido desde tu pantalla de inicio</p>
        </div>
        <div class="flex gap-2">
          <button
            @click="install"
            class="bg-white text-indigo-600 px-3 py-1 rounded text-sm font-medium hover:bg-indigo-50"
          >
            Instalar
          </button>
          <button
            @click="dismiss"
            class="text-indigo-200 hover:text-white text-sm"
          >
            Ahora no
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { usePWA } from '@/composables/usePWA.js';

const {
  updateAvailable,
  offlineReady,
  showInstallPrompt,
  handleInstallPrompt
} = usePWA();

const { install, dismiss } = handleInstallPrompt();
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
