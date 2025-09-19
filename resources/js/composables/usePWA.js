import { ref, onMounted } from 'vue'

/**
 * Configuración centralizada de PWA
 * Controla el comportamiento de todos los popups y notificaciones PWA
 */
const PWA_CONFIG = {
  // 🔧 Control de popups de notificación
  notifications: {
    install: true,        // "¡Instala Tseyor!" - Banner de instalación
    update: true,         // "Nueva versión disponible" - Notificación de actualización
    offline: true,        // "App lista offline" - Confirmación de funcionalidad offline
  },

  // ⏱️ Tiempo de auto-ocultación (milisegundos)
  autoHideDelay: {
    update: 5000,         // 5 segundos para notificación de actualización
    offline: 3000         // 3 segundos para notificación offline
  },

  // 🔄 Configuración de actualizaciones
  updateCheckInterval: 60000, // Verificar actualizaciones cada 1 minuto (60000ms)

  // 🚀 Entorno de desarrollo
  enableInDev: false,     // Mostrar PWA en desarrollo (false = solo producción)

  // 📱 Configuración adicional
  installPrompt: {
    // Tiempo mínimo antes de mostrar el prompt de instalación (futuro uso)
    minVisitTime: 30000,  // 30 segundos
    // Número mínimo de visitas antes de mostrar prompt (futuro uso)
    minVisits: 3
  }
}

export function usePWA() {
  const updateAvailable = ref(false)
  const offlineReady = ref(false)
  const showInstallPrompt = ref(false)
  const isInstalled = ref(false)
  const updateSW = ref(null)

  // Detectar si la app ya está instalada
  const checkIfInstalled = () => {
    if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
      isInstalled.value = true
    }
    if (window.navigator && window.navigator.standalone) {
      isInstalled.value = true
    }
    if (document.referrer.includes('android-app://')) {
      isInstalled.value = true
    }
  }

  // Inicializar PWA
  const initializePWA = async () => {
    checkIfInstalled()

    // Solo en producción (o si está habilitado en dev)
    if (import.meta.env.DEV && !PWA_CONFIG.enableInDev) return

    try {
      const { registerSW } = await import('virtual:pwa-register')

      updateSW.value = registerSW({
        onNeedRefresh() {
          console.log('🔄 Nueva versión disponible, actualizando automáticamente...')
          if (PWA_CONFIG.notifications.update) {
            updateAvailable.value = true

            // En autoUpdate, se actualiza automáticamente
            // Pero podemos mostrar una notificación discreta
            setTimeout(() => {
              updateAvailable.value = false
            }, PWA_CONFIG.autoHideDelay.update)
          }
        },

        onOfflineReady() {
          console.log('📱 App lista para funcionar offline')
          if (PWA_CONFIG.notifications.offline) {
            offlineReady.value = true

            setTimeout(() => {
              offlineReady.value = false
            }, PWA_CONFIG.autoHideDelay.offline)
          }
        },

        onRegistered(registration) {
          console.log('✅ Service Worker registrado')

          // Verificar actualizaciones usando configuración
          if (registration) {
            setInterval(() => {
              registration.update()
            }, PWA_CONFIG.updateCheckInterval)
          }
        },

        onRegisterError(error) {
          console.error('❌ Error registrando Service Worker:', error)
        }
      })
    } catch (error) {
      console.error('Error inicializando PWA:', error)
    }
  }

  // Manejar evento de instalación
  const handleInstallPrompt = () => {
    let deferredPrompt = null

    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault()
      deferredPrompt = e
      if (PWA_CONFIG.notifications.install) {
        showInstallPrompt.value = true
      }
    })

    return {
      install: async () => {
        if (deferredPrompt) {
          deferredPrompt.prompt()
          const { outcome } = await deferredPrompt.userChoice

          if (outcome === 'accepted') {
            console.log('👍 Usuario aceptó instalar la app')
            showInstallPrompt.value = false
            isInstalled.value = true
          }

          deferredPrompt = null
        }
      },

      dismiss: () => {
        showInstallPrompt.value = false
        deferredPrompt = null
      }
    }
  }

  onMounted(() => {
    initializePWA()
  })

  return {
    updateAvailable,
    offlineReady,
    showInstallPrompt,
    isInstalled,
    initializePWA,
    handleInstallPrompt
  }
}
