import { router } from '@inertiajs/vue3'

/*

AppLayout.initPWA(nav)
  ↓
usePWASession.initPWA()
  ↓
  ├── restoreState() [maneja navegación automática]
  ├── initStatePreservation(nav) [watchers de scroll]
  └── initLoader() [maneja ocultar loader]
      ↓
      hideLoader() [oculta el loader PWA]

*/

/**
 * Composable para preservar el estado de sesión en PWA
 * Guarda y restaura la URL actual y posición de scroll
 */
export function usePWASession() {
  // Sistema de logging para PWA (guarda en localStorage para debugging móvil)
  const LOG_KEY = 'pwa-debug-logs'

  // Claves para localStorage
  const STORAGE_KEYS = {
    URL: 'pwa-last-url',
    SCROLL_Y: 'pwa-scroll-y',
    TIMESTAMP: 'pwa-timestamp'
  }

  // Configuración de expiración del estado (7 días en milisegundos)
  const MAX_STATE_AGE = 7 * 24 * 60 * 60 * 1000

  // Estado reactivo para mostrar loader durante restauración
  const isRestoring = ref(false)

  // Bandera para saber si ya se verificó la restauración
  const hasCheckedRestoration = ref(false)

  // Variable para throttling del scroll
  let scrollLastSave = null

  const log = (level, message, data = null) => {
    const timestamp = new Date().toLocaleTimeString()
    const logEntry = {
      timestamp,
      level,
      message,
      data,
      url: typeof window !== 'undefined' ? window.location.href : 'SSR'
    }

    // Guardar en localStorage para debugging local (máximo 20 logs)
    try {
      const existingLogs = JSON.parse(localStorage.getItem(LOG_KEY) || '[]')
      existingLogs.push(logEntry)
      if (existingLogs.length > 20) { // Reducido para no llenar localStorage
        existingLogs.shift()
      }
      localStorage.setItem(LOG_KEY, JSON.stringify(existingLogs))
    } catch (error) {
      // Silenciar errores de localStorage
    }

    // Envío de logs al servidor (siempre activo para debugging PWA)
    if(false) // desactivado en producción
    if (typeof window !== 'undefined') {
      try {
        fetch('/pwa-log', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({
            level,
            message,
            data,
            url: logEntry.url,
            timestamp,
            user_agent: navigator.userAgent,
            is_pwa: isPWA()
          })
        }).catch((error) => {
          console.warn('[PWA] Error enviando log al servidor:', error)
          log('warn', 'Error enviando log al servidor', { error: error.message || error })
        })
      } catch (error) {
        console.warn('[PWA] Error en fetch de log:', error)
        log('warn', 'Error en fetch de log', { error: error.message || error })
      }
    }

    // También mostrar en consola
    const consoleMethod = level === 'error' ? 'error' : level === 'warn' ? 'warn' : 'log'
    console[consoleMethod](`[${level.toUpperCase()}] ${message}`, data || '')

    // Para mensajes críticos en móvil, mostrar notificación si es posible
    if (level === 'error' && typeof window !== 'undefined' && 'Notification' in window) {
      try {
        if (Notification.permission === 'granted') {
          new Notification('PWA Error', { body: message })
        }
      } catch (e) {
        // Silenciar errores de notificación
      }
    }
  }

  /**
   * Verifica si la app está ejecutándose como PWA instalada
   */
  const isPWA = () => {
    if (typeof window === 'undefined') return false

    // Detectar si está en modo standalone
    if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) {
      return true
    }

    // iOS Safari
    if (window.navigator && window.navigator.standalone) {
      return true
    }

    // Android (referrer check)
    if (document.referrer && document.referrer.includes('android-app://')) {
      return true
    }

    return false
  }

  // Setear cookie cuando se detecta PWA para futuras requests SSR
  if (typeof window !== 'undefined' && isPWA()) {
    try {
      document.cookie = 'is_pwa=true; path=/; max-age=31536000'; // 1 año
    } catch (e) {
      // Silenciar errores de cookies
    }
  }



  const getLogs = () => {
    try {
      return JSON.parse(localStorage.getItem(LOG_KEY) || '[]')
    } catch {
      return []
    }
  }

  const clearLogs = () => {
    try {
      localStorage.removeItem(LOG_KEY)
    } catch {
      // Silenciar errores
    }
  }

  // Estado interno para evitar sobrescrituras inmediatas
  let recentlyRestored = false
  let restoreTimestamp = 0
  let isInitializing = true // Nueva bandera para período de inicialización

  /**
   * Guarda el estado actual en localStorage
   */
  const saveState = () => {
    if (typeof window === 'undefined' || !isPWA()) {
      log('info', 'No es PWA o no hay window, saltando guardado')
      return
    }

    // NO guardar durante el período de inicialización (primeros 5 segundos)
    if (isInitializing) {
      log('info', 'PWA inicializándose, saltando guardado automático')
      return
    }

    // Evitar guardar inmediatamente después de una restauración (primeros 3 segundos)
    if (recentlyRestored && Date.now() - restoreTimestamp < 3000) {
      log('info', 'Recientemente restaurado, saltando guardado para evitar sobrescritura')
      return
    }

    // Resetear la bandera después del período de protección
    if (recentlyRestored && Date.now() - restoreTimestamp >= 3000) {
      recentlyRestored = false
      log('info', 'Período de protección terminado, guardando normalmente')
    }

    try {
      const currentUrl = window.location.href
      const scrollY = window.scrollY || window.pageYOffset
      const timestamp = Date.now()

      // Si estamos en la página principal con scroll mínimo, borrar el estado en lugar de guardarlo
      if (window.location.pathname === '/' && scrollY < 150) {
        localStorage.removeItem(STORAGE_KEYS.URL)
        localStorage.removeItem(STORAGE_KEYS.SCROLL_Y)
        localStorage.removeItem(STORAGE_KEYS.TIMESTAMP)
        log('info', 'Estado PWA borrado (página principal con scroll mínimo)')
        return
      }

      localStorage.setItem(STORAGE_KEYS.URL, currentUrl)
      localStorage.setItem(STORAGE_KEYS.SCROLL_Y, scrollY.toString())
      localStorage.setItem(STORAGE_KEYS.TIMESTAMP, timestamp.toString())

      log('info', 'Estado PWA guardado', {
        url: currentUrl,
        scrollY,
        timestamp: new Date(timestamp).toLocaleTimeString()
      })
    } catch (error) {
      log('error', 'Error guardando estado PWA', error)
    }
  }

      /**
   * Restaura el estado guardado desde localStorage
   */
  const restoreState = () => {
    if (typeof window === 'undefined' || !isPWA()) {
      // log('info', 'No es PWA o no hay window, saltando restauración')
      hasCheckedRestoration.value = true // Marcar como verificado incluso si no es PWA
      isRestoring.value = false // Asegurar que no se está restaurando
      return
    }

    log('info', '=== VERIFICANDO ESTADO PWA PARA RESTAURACIÓN ===')

    try {
      const savedUrl = localStorage.getItem(STORAGE_KEYS.URL)
      const savedScrollY = localStorage.getItem(STORAGE_KEYS.SCROLL_Y)
      const savedTimestamp = localStorage.getItem(STORAGE_KEYS.TIMESTAMP)

      if (!savedUrl || !savedScrollY || !savedTimestamp) {
        log('info', 'No hay estado guardado para restaurar')
        hasCheckedRestoration.value = true // Marcar como verificado
        isRestoring.value = false // Asegurar que no se está restaurando
        return
      }

      const timestamp = parseInt(savedTimestamp)
      if (isNaN(timestamp)) {
        log('warn', 'Timestamp inválido, limpiando estado')
        clearState()
        hasCheckedRestoration.value = true // Marcar como verificado
        isRestoring.value = false // Asegurar que no se está restaurando
        return
      }

      log('info', 'Datos recuperados de localStorage', { savedUrl, savedScrollY, savedTimestamp })

      const now = Date.now()
      const ageMinutes = Math.round((now - timestamp) / (1000 * 60))

      log('info', `Estado PWA tiene ${ageMinutes} minutos de antigüedad`)

      // Verificar si el estado no es demasiado antiguo
      if (now - timestamp > MAX_STATE_AGE) {
        log('warn', 'Estado PWA expirado, limpiando...')
        clearState()
        hasCheckedRestoration.value = true // Marcar como verificado
        isRestoring.value = false // Asegurar que no se está restaurando
        return
      }

      const scrollY = parseInt(savedScrollY)
      const currentUrl = window.location.href
      const currentPath = window.location.pathname

      log('info', 'Comparando URLs', { saved: savedUrl, current: currentUrl, currentPath })

      // Si estamos en la página inicial (/) y hay una URL guardada diferente, navegar a ella
      if (currentPath === '/' && savedUrl !== currentUrl) {
        log('info', '=== INICIANDO RESTAURACIÓN DE ESTADO PWA ===')

        // ACTIVAR loader durante la restauración
        isRestoring.value = true

        // Marcar que acabamos de restaurar para evitar sobrescrituras
        recentlyRestored = true
        restoreTimestamp = Date.now()

        try {
          log('info', 'Estamos en página inicial, navegando a URL guardada', savedUrl)
          log('info', 'Ejecutando router.visit()...')

          // Verificar que el router esté disponible
          if (!router || typeof router.visit !== 'function') {
            log('error', 'Router de Inertia no disponible', { router: typeof router })
            isRestoring.value = false
            return
          }

          // Usar Inertia router para navegar a la URL guardada
          router.visit(savedUrl, {
            preserveState: true,
            preserveScroll: false,
            onSuccess: () => {
              log('info', 'router.visit() completado exitosamente')
              // Después de navegar, restaurar el scroll
              setTimeout(() => {
                log('info', 'Ejecutando scroll restoration...')
                window.scrollTo({ top: scrollY, left: 0, behavior: 'instant' })
                log('success', 'Navegación completada y scroll restaurado', { url: savedUrl, scrollY })

                // DESACTIVAR loader después de completar la restauración
                setTimeout(() => {
                  isRestoring.value = false
                  hasCheckedRestoration.value = true
                  log('info', 'Loader desactivado, restauración completa')
                }, 300)
              }, 200)
            },
            onError: (error) => {
              log('error', 'Error en router.visit()', { error: error.message || error, url: savedUrl })
              isRestoring.value = false
              hasCheckedRestoration.value = true
            }
          })
        } catch (visitError) {
          log('error', 'Excepción en router.visit()', { error: visitError.message || visitError })
          isRestoring.value = false
          hasCheckedRestoration.value = true
        }
      } else if (isRelatedUrl(savedUrl, currentUrl)) {
        // Si estamos en la misma página, solo restaurar scroll
        log('info', 'Misma página, restaurando solo scroll', { scrollY })

        // ACTIVAR loader durante la restauración de scroll
        isRestoring.value = true

        setTimeout(() => {
          window.scrollTo({ top: scrollY, left: 0, behavior: 'instant' })
          log('success', 'Scroll restaurado en misma página', { scrollY })
          hasCheckedRestoration.value = true // Marcar como verificado
          isRestoring.value = false // Desactivar loader
        }, 100)
      } else {
        log('info', 'URLs no relacionadas o ya en destino, no se restaura')
        hasCheckedRestoration.value = true // Marcar como verificado
        isRestoring.value = false // Asegurar que no se está restaurando
      }
    } catch (error) {
      log('error', 'Error restaurando estado PWA', error)
      isRestoring.value = false
      hasCheckedRestoration.value = true // Marcar como verificado incluso en error
    }

    // Marcar que se ha verificado la restauración
    hasCheckedRestoration.value = true

    // Si no se activó restauración, desactivar loader inmediatamente
    if (!isRestoring.value) {
      log('info', 'No se requiere restauración, desactivando loader')
    }
  }

  /**
   * Limpia el estado guardado
   */
  const clearState = () => {
    if (typeof window === 'undefined') return

    try {
      localStorage.removeItem(STORAGE_KEYS.URL)
      localStorage.removeItem(STORAGE_KEYS.SCROLL_Y)
      localStorage.removeItem(STORAGE_KEYS.TIMESTAMP)
      log('info', 'Estado PWA limpiado')
    } catch (error) {
      log('error', 'Error limpiando estado PWA', error)
    }
  }

  /**
   * Verifica si dos URLs están relacionadas (misma página base)
   */
  const isRelatedUrl = (savedUrl, currentUrl) => {
    try {
      const savedPath = new URL(savedUrl).pathname
      const currentPath = new URL(currentUrl).pathname
      return savedPath === currentPath
    } catch {
      return false
    }
  }

  /**
   * Inicializa los event listeners para guardar estado
   * Devuelve una función de cleanup
   */
  const initStatePreservation = (nav = null) => {
    if (typeof window === 'undefined' || !isPWA()) return () => {}

    log('info', 'Inicializando preservación de estado PWA')

    // Configurar watcher del scroll si se proporciona nav
    let unwatchScroll = null
    if (nav) {
      unwatchScroll = watch(
        () => nav.scrollY,
        () => {
          // Guardar estado con throttling (máximo cada 2 segundos)
          const now = Date.now()
          if (!scrollLastSave || now - scrollLastSave > 2000) {
            saveState()
            scrollLastSave = now
          }
        }
      )
    }

    // Configurar inmediatamente los event listeners, pero con protección de inicialización
    const saveEvents = ['beforeunload', 'pagehide', 'visibilitychange']

    const handleSave = () => {
      if (document.visibilityState === 'hidden' || document.visibilityState === 'unloading') {
        saveState()
      }
    }

    saveEvents.forEach(event => {
      window.addEventListener(event, handleSave, { passive: true })
    })

    // También guardar periódicamente cada 30 segundos
    const intervalId = setInterval(saveState, 30000)

    // DESACTIVAR la protección de inicialización después de 5 segundos
    setTimeout(() => {
      isInitializing = false
      log('info', 'Período de inicialización PWA terminado, activando guardado automático')
    }, 5000)

    // Cleanup function
    const cleanup = () => {
      if (unwatchScroll) unwatchScroll()
      saveEvents.forEach(event => {
        window.removeEventListener(event, handleSave)
      })
      clearInterval(intervalId)
    }

    return cleanup
  }

  /**
   * Oculta el loader inicial de PWA
   */
  const hideLoader = () => {
    if (typeof window === 'undefined') return

    const initialLoader = document.getElementById('pwa-initial-loader')
    const loadingMessage = document.getElementById('pwa-loading-message')
    if (initialLoader) {
      log('info', 'Ocultando loader PWA')
      initialLoader.style.display = 'none'
    }
    if (loadingMessage) {
      loadingMessage.style.display = 'none'
    }
  }

  /**
   * Inicializa el manejo del loader PWA
   */
  const initLoader = () => {
    if (typeof window === 'undefined') return

    // Si no es PWA, ocultar loader inmediatamente
    if (!isPWA()) {
      log('info', 'No es PWA, ocultando loader inmediatamente')
      hideLoader()
      return
    }

    log('info', 'Inicializando loader PWA')

    // Verificar si la restauración ya se completó
    if (hasCheckedRestoration.value && !isRestoring.value) {
      log('info', 'Restauración ya completada, ocultando loader inmediatamente')
      hideLoader()
      return
    }

    // Configurar watchers para ocultar el loader cuando termine la restauración
    const unwatchRestoring = watch(
      () => isRestoring.value,
      (newValue) => {
        if (!newValue) {
          hideLoader()
          unwatchRestoring()
        }
      }
    )

    const unwatchChecked = watch(
      () => hasCheckedRestoration.value,
      (newValue) => {
        if (newValue && !isRestoring.value) {
          hideLoader()
          unwatchChecked()
        }
      }
    )

    // Mostrar mensaje de carga a los 4 segundos
    setTimeout(() => {
      const loadingMessage = document.getElementById('pwa-loading-message')
      if (loadingMessage) {
        loadingMessage.style.opacity = '1'
      }
    }, 4000)

    // Fallback: ocultar loader después de 10 segundos si algo falla
    setTimeout(() => {
      hideLoader()
      log('warn', 'Loader ocultado por timeout de fallback')
    }, 15000)
  }

  /**
   * Inicializa completamente la sesión PWA
   * Debe llamarse una sola vez desde el componente principal
   */
  const initPWA = (nav = null) => {
    if (typeof window === 'undefined') return

    // Restaurar estado si es necesario
    restoreState()

    // Inicializar preservación de estado (scroll automático)
    initStatePreservation(nav)

    // Inicializar loader
    initLoader()
  }

  return {
    isPWA,
    initPWA,
    getLogs,
    clearLogs
  }
}
