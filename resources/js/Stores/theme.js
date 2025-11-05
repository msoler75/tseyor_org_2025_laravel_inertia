import { defineStore } from "pinia"
import { ref, watch } from "vue"
import { useDark } from "@vueuse/core"
import { usePage } from "@inertiajs/vue3"
import { getApiUrl } from "@/composables/api"

const TOGGLE_DEBOUNCE_MS = 200
const FONT_STORAGE_KEY = "tseyor_tamanyoFuente"
const DEFAULT_FONT_SIZE = 16

/**
 * SISTEMA DE MANEJO DE TEMAS
 * ========================
 *
 * Implementación Pinia compatible con SSR que mantiene la sincronización
 * entre estado reactivo, localStorage, cookie del servidor y atributo data-theme.
 */
export const useThemeStore = defineStore("theme", () => {
    const page = usePage()
    const initialTheme = page?.props?.initialTheme ?? "light"
    const initialDark = initialTheme === "dark" || initialTheme === "night"
    const isClient = typeof window !== "undefined"
    const initialFontSize = normalizeFontSize(page?.props?.initialFontSize, DEFAULT_FONT_SIZE)

    const darkComposable = isClient
        ? useDark({
            storageKey: "theme",
            selector: "html",
            valueDark: "night",
            initialValue: initialDark,
            onChanged: () => {},
        })
        : null

    const isDark = darkComposable ?? ref(initialDark)
    const fontSize = ref(initialFontSize)

    if (isClient) {
        watch(
            isDark,
            value => {
                applyTheme(value)
            },
            { immediate: true }
        )

        const storedFontSize = readPersistedFontSize()
        if (storedFontSize !== null) {
            fontSize.value = storedFontSize
        }

        applyFontSize(fontSize.value)
        persistFontSizeLocally(fontSize.value)

        if (storedFontSize !== null && storedFontSize !== initialFontSize) {
            syncFontSizeToServer(fontSize.value)
        }

        watch(fontSize, value => {
            applyFontSize(value)
            persistFontSizeLocally(value)
            syncFontSizeToServer(value)
        })
    }

    let lastToggleTime = 0

    function applyTheme(value) {
        if (!isClient) {
            return
        }

        const themeValue = value ? "night" : "light"

        document.documentElement.setAttribute("data-theme", themeValue)

        try {
            localStorage.setItem("theme", themeValue)
        } catch (error) {
            console.warn("Error persisting theme locally:", error)
        }

        try {
            fetch(`${getApiUrl()}/update-theme`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({ theme: themeValue }),
            }).catch(error => {
                console.warn("Error updating theme on server:", error)
            })
        } catch (error) {
            console.warn("Error sending theme update:", error)
        }
    }

    function applyFontSize(value) {
        if (!isClient) {
            return
        }

        try {
            document.documentElement.style.setProperty("--text-base", `${value}px`)
        } catch (error) {
            console.warn("Error applying font size:", error)
        }
    }

    function persistFontSizeLocally(value) {
        if (!isClient) {
            return
        }

        try {
            localStorage.setItem(FONT_STORAGE_KEY, String(value))
        } catch (error) {
            console.warn("Error persisting font size locally:", error)
        }
    }

    function syncFontSizeToServer(value) {
        if (!isClient) {
            return
        }

        try {
            fetch("/update-font-size", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify({ fontSize: value }),
            }).catch(error => {
                console.warn("Error updating font size on server:", error)
            })
        } catch (error) {
            console.warn("Error sending font size update:", error)
        }
    }

    function readPersistedFontSize() {
        if (!isClient) {
            return null
        }

        try {
            const stored = localStorage.getItem(FONT_STORAGE_KEY)
            if (stored === null) {
                return null
            }

            const parsed = parseInt(stored, 10)
            return Number.isNaN(parsed) ? null : parsed
        } catch (error) {
            console.warn("Error reading persisted font size:", error)
            return null
        }
    }

    function setDark(value) {
        if (darkComposable) {
            darkComposable.value = value
        } else {
            isDark.value = value
        }
    }

    function setFontSize(value) {
        fontSize.value = value
    }

    function toggleDark() {
        const now = Date.now()

        if (now - lastToggleTime < TOGGLE_DEBOUNCE_MS) {
            console.log("Toggle ignored due to debounce")
            return
        }

        lastToggleTime = now
        setDark(!isDark.value)
    }

    return {
        isDark,
        toggleDark,
        setDark,
        fontSize,
        setFontSize,
    }
})

export function useTheme() {
    return useThemeStore()
}

function normalizeFontSize(value, fallback) {
    const parsed = typeof value === "string" ? parseInt(value, 10) : Number(value)
    return Number.isFinite(parsed) ? parsed : fallback
}
