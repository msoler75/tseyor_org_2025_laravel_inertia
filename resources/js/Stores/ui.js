import { computed } from "vue"
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
 * - Todas las propiedades mantienen reactividad completa (son stores de Pinia)
 * - No necesita singleton, Pinia ya maneja instancias únicas
 * - Compatible con SSR (cada request tiene su propio estado)
 * - API simplificada: `ui.player.play()`, `ui.nav.toggleSidebar()`, etc.
 */
export default function useUi() {
    const folderExplorer = useFolderExplorerStore()
    const selectors = useSelectorsStore()
    const player = usePlayerStore()
    const tools = useToolsStore()
    const theme = useTheme()
    const nav = useNavStore()

    // Retornar objeto con referencias directas a stores
    // Pinia garantiza que siempre sean las mismas instancias
    return {
        folderExplorer,
        selectors,
        player,
        tools,
        theme,
        nav,
    }
}
