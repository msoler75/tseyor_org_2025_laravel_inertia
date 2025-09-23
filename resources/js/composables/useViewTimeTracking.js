import { useGoogleAnalytics } from './useGoogleAnalytics'

// Composable para trackear el tiempo de visualización de un contenido específico (psicografía, sello, etc.)
// Maneja automáticamente pausas al cambiar de pestaña, minimizar, etc.
// Uso:
//   const { startTracking, stopTracking } = useViewTimeTracking()
//   startTracking('psicografia', 'Nombre de la psicografía')
//   ...
//   stopTracking() // Envía el tiempo acumulado a Google Analytics
export function useViewTimeTracking() {
    const { trackViewTime } = useGoogleAnalytics()

    let viewStartTime = null
    let totalViewTime = 0
    let isWindowVisible = true
    let currentContentType = null
    let currentContentTitle = null
    let isTracking = false
    let clientId = null
    const getClientId = () => {
        if (typeof window !== 'undefined' && window.gtag) {
            try {
                // Intentar obtener el measurement_id de la configuración
                const measurementId = document.querySelector('[name="google_analytics_measurement_id"]')?.content ||
                                    window.GA_MEASUREMENT_ID ||
                                    'G-1KFEWPWDFZ'; // fallback

                window.gtag('get', measurementId, 'client_id', (id) => {
                    clientId = id;
                    console.log('📊 Client ID obtenido:', id);
                });
            } catch (error) {
                console.warn('No se pudo obtener client_id de GA4:', error);
                // Generar un client_id temporal como fallback
                clientId = 'temp_' + Math.random().toString(36).substr(2, 9) + '.' + Date.now();
            }
        } else {
            // Fallback si no hay gtag disponible
            clientId = 'fallback_' + Math.random().toString(36).substr(2, 9) + '.' + Date.now();
        }
    }

    // Funciones principales
    const startTracking = (contentType, contentTitle) => {
        if (isTracking) stopTracking() // Detener tracking anterior si existe

        // Obtener client_id si no lo tenemos aún
        if (!clientId) {
            getClientId();
        }

        currentContentType = contentType
        currentContentTitle = contentTitle
        isTracking = true

        // Instalar event listeners al empezar tracking
        setupEventListeners()

        if (isWindowVisible) {
            viewStartTime = Date.now()
            console.log('🕒 Iniciando tracking:', contentType, contentTitle)
        }
    }

    const pauseTracking = () => {
        if (viewStartTime && isTracking) {
            const now = Date.now()
            const sessionTime = (now - viewStartTime) / 1000

            totalViewTime += sessionTime
            viewStartTime = null
            console.log('⏸️ Pausando tracking:', sessionTime + 's', 'Total:', totalViewTime + 's')
        }
    }

    const resumeTracking = () => {
        if (isTracking && isWindowVisible && !viewStartTime) {
            viewStartTime = Date.now()
            console.log('▶️ Resumiendo tracking')
        }
    }

    const stopTracking = () => {
        if (!isTracking) return

        pauseTracking()

        // Verificar si el tiempo total es realista (máximo 1 hora)
        const maxReasonableTime = 3600 // 1 hora en segundos
        if (totalViewTime > maxReasonableTime) {
            console.log('⚠️ Tiempo de visualización irrealmente largo (' + Math.round(totalViewTime/60) + ' min), descartando sesión')
            // Reset y no enviar datos
            totalViewTime = 0
            currentContentType = null
            currentContentTitle = null
            isTracking = false
            // Desinstalar listeners
            removeEventListeners()
            return
        }

        if (totalViewTime >= 5) { // un minimo de 5 segundos para considerar válido
            // Usar sendBeacon para envío más confiable al cerrar navegador
            if (navigator.sendBeacon && typeof window !== 'undefined') {
                try {
                    const data = JSON.stringify({
                        event: 'view_time',
                        content_type: currentContentType,
                        content_title: currentContentTitle,
                        view_time_seconds: Math.round(totalViewTime),
                        view_time_minutes: Math.round(totalViewTime / 60 * 10) / 10,
                        time_category: getTimeCategory(Math.round(totalViewTime)),
                        page_title: document.title,
                        page_location: window.location.href,
                        client_id: clientId, // Incluir el client_id real de GA4
                        timestamp: Date.now()
                    })

                    // Intenta usar sendBeacon primero (más confiable para cierre de navegador)
                    const sent = navigator.sendBeacon('/analytics/beacon', data)
                    console.log('📡 Datos enviados via beacon:', sent ? 'exitoso' : 'fallback a gtag')

                    if (!sent) {
                        // Fallback a tracking normal si beacon falla
                        trackViewTime(currentContentType, currentContentTitle, Math.round(totalViewTime))
                    }
                } catch (error) {
                    console.warn('Error con beacon, usando gtag:', error)
                    trackViewTime(currentContentType, currentContentTitle, Math.round(totalViewTime))
                }
            } else {
                // Fallback a tracking normal
                trackViewTime(currentContentType, currentContentTitle, Math.round(totalViewTime))
            }

            console.log('📊 Enviando tiempo de visualización:', currentContentType, totalViewTime + 's')
        }

        // Reset
        totalViewTime = 0
        currentContentType = null
        currentContentTitle = null
        isTracking = false

        // Desinstalar event listeners al parar tracking
        removeEventListeners()
    }

    // Función auxiliar para obtener categoría de tiempo (duplicada del composable principal)
    const getTimeCategory = (seconds) => {
        const timeRanges = [
            { min: 3600, category: '60min' },
            { min: 2700, category: '45min' },
            { min: 1800, category: '30min' },
            { min: 900,  category: '15min' },
            { min: 600,  category: '10min' },
            { min: 300,  category: '5min' },
            { min: 120,  category: '2min' },
            { min: 60,   category: '1min' },
            { min: 0,    category: '0min' }
        ]
        return timeRanges.find(range => seconds >= range.min).category
    }

    // Detectar cambios de visibilidad de la ventana
    const handleVisibilityChange = () => {
        if (document.hidden) {
            isWindowVisible = false
            pauseTracking()
        } else {
            isWindowVisible = true
            if (isTracking) {
                resumeTracking()
            }
        }
    }

    // Detectar cuando la ventana pierde/gana foco
    const handleWindowBlur = () => {
        isWindowVisible = false
        pauseTracking()
    }

    const handleWindowFocus = () => {
        isWindowVisible = true
        if (isTracking) {
            resumeTracking()
        }
    }

    // Detectar antes de cerrar/recargar la página
    const handleBeforeUnload = (event) => {
        console.log('🚪 Detectando cierre de navegador/pestaña')
        stopTracking()
        // No mostrar diálogo de confirmación, solo enviar datos
    }

    // Detectar cuando se descarga/abandona la página
    const handlePageHide = () => {
        console.log('📄 Página ocultándose (cierre, navegación, etc.)')
        stopTracking()
    }

    // Configurar event listeners
    const setupEventListeners = () => {
        document.addEventListener('visibilitychange', handleVisibilityChange)
        window.addEventListener('blur', handleWindowBlur)
        window.addEventListener('focus', handleWindowFocus)
        window.addEventListener('beforeunload', stopTracking)
        window.addEventListener('unload', stopTracking)
    }

    const removeEventListeners = () => {
        document.removeEventListener('visibilitychange', handleVisibilityChange)
        window.removeEventListener('blur', handleWindowBlur)
        window.removeEventListener('focus', handleWindowFocus)
        window.removeEventListener('beforeunload', stopTracking)
        window.removeEventListener('unload', stopTracking)
    }

    return {
        startTracking,
        pauseTracking,
        resumeTracking,
        stopTracking,
        isTracking: () => isTracking,
        getCurrentViewTime: () => totalViewTime + (viewStartTime ? (Date.now() - viewStartTime) / 1000 : 0)
    }
}
