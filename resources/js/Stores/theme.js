import { useDark } from "@vueuse/core"
import { usePage } from "@inertiajs/vue3"
import { getApiUrl } from "@/Stores/api"

/**
 * SISTEMA DE MANEJO DE TEMAS
 * ========================
 *
 * Este store centraliza el manejo del tema (modo oscuro/claro) en toda la aplicación.
 *
 * FUNCIONAMIENTO:
 * 1. El tema se guarda en localStorage para persistencia del lado del cliente
 * 2. También se guarda en una cookie del servidor para que persista entre visitas
 * 3. El usuario puede recordar su preferencia de tema aunque entre otro día
 *
 * INICIALIZACIÓN:
 * - El tema se inicializa desde el servidor (page.props.initialTheme)
 * - El NavBar es el primer componente que se monta y ejecuta useTheme()
 * - Esto garantiza que el tema se configure correctamente desde el inicio
 *
 * SINCRONIZACIÓN:
 * - localStorage: para persistencia inmediata del lado del cliente
 * - Cookie servidor: para persistencia entre sesiones y SSR
 * - HTML data-theme: para que DaisyUI aplique los estilos correctos
 *
 * USO:
 * - Importar useTheme() en cualquier componente
 * - Todas las instancias comparten el mismo estado reactivo
 * - No hay conflictos entre múltiples componentes
 */

// Store singleton para el tema - garantiza una sola instancia global
let themeStore = null

/**
 * Hook principal para acceder al sistema de temas
 *
 * PATRÓN SINGLETON:
 * - Solo crea la instancia una vez, independientemente de cuántos componentes lo usen
 * - Garantiza que todos los componentes compartan el mismo estado reactivo
 * - Evita conflictos entre múltiples instancias de useDark()
 *
 * @returns {Object} { isDark: Ref<boolean>, toggleDark: Function }
 */
export function useTheme() {
    if (!themeStore) {
        // INICIALIZACIÓN: Solo se ejecuta la primera vez (normalmente desde NavBar)
        const page = usePage()
        const initialTheme = page.props.initialTheme // Viene del servidor (cookie o default)

        console.log('Initializing theme store with:', initialTheme)

        // CONFIGURACIÓN DE VUEUSE/CORE useDark:
        // - storageKey: clave en localStorage donde se guarda el tema
        // - selector: elemento HTML donde se aplica el atributo data-theme
        // - valueDark: valor que se aplica cuando está en modo oscuro
        // - initialValue: tema inicial (desde servidor para evitar flash)
        // - onChanged: deshabilitado para controlar manualmente la sincronización
        const isDark = useDark({
            storageKey: "theme",
            selector: "html",
            valueDark: "night",
            initialValue: initialTheme !== 'dark' ? 'light' : 'night',
            onChanged: () => {}, // Deshabilitado para control manual
        })

        // CONTROL DE DEBOUNCE: Prevenir cambios muy rápidos del tema
        let lastToggleTime = 0
        const TOGGLE_DEBOUNCE_MS = 200

        /**
         * Función para alternar entre modo oscuro y claro
         *
         * CARACTERÍSTICAS:
         * - Implementa debounce para evitar clicks dobles accidentales
         * - Actualiza el estado reactivo (isDark.value)
         * - Sincroniza con localStorage, cookie y HTML inmediatamente
         */
        const toggleDark = () => {
            const now = Date.now()

            // Prevenir dobles clicks muy rápidos
            if (now - lastToggleTime < TOGGLE_DEBOUNCE_MS) {
                console.log('Toggle ignored due to debounce')
                return
            }

            console.log('Starting theme toggle from:', isDark.value)
            lastToggleTime = now

            // Cambiar el valor reactivo (esto actualiza automáticamente localStorage)
            isDark.value = !isDark.value

            // Sincronizar con cookie del servidor y aplicar cambios visuales
            updateTheme(isDark.value)

            console.log('Toggle completed to:', isDark.value)
        }

        // OBJETO DE RETORNO: Estado reactivo y funciones del tema
        themeStore = {
            isDark,    // Ref<boolean> - Estado reactivo del tema
            toggleDark // Function - Alternar tema
        }
    }

    // Retornar la instancia singleton (misma instancia para todos los componentes)
    return themeStore
}

/**
 * Función interna para sincronizar el tema en todos los niveles
 *
 * RESPONSABILIDADES:
 * 1. Aplicar cambios visuales inmediatos (HTML data-theme para DaisyUI)
 * 2. Actualizar localStorage (ya lo hace useDark automáticamente, pero por consistencia)
 * 3. Sincronizar con cookie del servidor para persistencia entre sesiones
 *
 * FLUJO:
 * - Se ejecuta cada vez que se cambia el tema
 * - No bloquea la UI (cookie update es asíncrono)
 * - Maneja errores de red graciosamente
 *
 * @param {boolean} isDarkMode - true para modo oscuro, false para modo claro
 */
function updateTheme(isDarkMode) {
    // PREVENCIÓN SSR: Evitar ejecución en servidor (sin window)
    if (typeof window === "undefined") {
        console.log("estamos en SSR")
        return
    }

    console.log("updateTheme", isDarkMode)
    const themeValue = isDarkMode ? "night" : "light"

    // 1. CAMBIOS VISUALES INMEDIATOS: Aplicar atributo data-theme para DaisyUI
    document.documentElement.setAttribute("data-theme", themeValue)

    // 2. LOCALSTORAGE: Persistencia del lado del cliente (también lo hace useDark)
    localStorage.setItem("theme", themeValue)

    // 3. COOKIE SERVIDOR: Persistencia entre sesiones (asíncrono, no bloquea UI)
    try {
        fetch(`${getApiUrl()}/update-theme`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
            body: JSON.stringify({ theme: themeValue }),
        }).catch(error => {
            console.warn('Error updating theme on server:', error)
        })
    } catch (error) {
        console.warn('Error sending theme update:', error)
    }
}
