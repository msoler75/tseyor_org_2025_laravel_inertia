import { storeToRefs } from "pinia"
import { useFolderExplorerStore } from "@/Stores/folderExplorer"
import { useSelectorsStore } from "@/Stores/selectors"
import { usePlayerStore } from "@/Stores/player"
import { useToolsStore } from "@/Stores/tools"
import { useNavStore } from "@/Stores/nav"
import { useTheme } from "@/Stores/theme"

/**
 * COMPOSABLE UI - Agregador de Stores
 * ===================================
 *
 * Proporciona acceso unificado a todos los stores de UI mediante un objeto reactivo.
 * Mantiene compatibilidad con código existente que usa `useUi()`.
 *
 * VENTAJAS:
 * - Todas las propiedades mantienen reactividad completa
 * - Los componentes pueden destructurar sin perder reactividad
 * - No necesita singleton, Pinia ya maneja instancias únicas
 * - Compatible con SSR (cada request tiene su propio estado)
 * - API simplificada: `ui.player.play()`, `ui.nav.toggleSidebar()`, etc.
 * - Los componentes NO necesitan importar Pinia directamente
 */
export default function useUi() {
    const folderExplorer = useFolderExplorerStore()
    const selectors = useSelectorsStore()
    const player = usePlayerStore()
    const tools = useToolsStore()
    const themeStore = useTheme()
    const nav = useNavStore()

    // Extraer refs reactivos del theme store para permitir destructuring
    const { isDark, fontSize } = storeToRefs(themeStore)

    // Retornar objeto con referencias directas a stores
    // Para theme, devolvemos un objeto con refs y métodos separados
    return {
        folderExplorer,
        selectors,
        player,
        tools,
        theme: {
            isDark,
            fontSize,
            toggleDark: themeStore.toggleDark,
            setDark: themeStore.setDark,
            setFontSize: themeStore.setFontSize,
        },
        nav,
    }
}
