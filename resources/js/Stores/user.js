import { defineStore } from "pinia"
import { ref } from "vue"
import { usePage } from "@inertiajs/vue3"
import { getApiUrl } from "@/composables/api"

/**
 * SISTEMA DE GESTIÓN DE USUARIO
 * ========================
 *
 * Store Pinia compatible con SSR que maneja permisos y saldo del usuario.
 */
export const useUserStorePinia = defineStore("user", () => {
    const page = usePage()
    const isClient = typeof window !== "undefined"

    // Estado inicial desde servidor si está disponible
    const initialPermisos = page?.props?.userPermisos ?? []
    const initialSaldo = page?.props?.userSaldo ?? ""
    const initialSaldoError = page?.props?.userSaldoError ?? ""

    const permisos = ref(initialPermisos)
    const saldo = ref(initialSaldo)
    const saldoError = ref(initialSaldoError)

    // Cargar permisos desde API
    async function cargarPermisos() {
        if (!isClient) {
            return
        }

        try {
            const response = await fetch(`${getApiUrl()}/usuario/_permisos`)
            const data = await response.json()
            console.log("permisos response", data)
            permisos.value = data
        } catch (error) {
            console.error("Error al cargar los permisos:", error)
        }
    }

    // Comprobar si el usuario tiene un permiso
    function tienePermiso(permiso) {
        return permisos.value.includes(permiso)
    }

    // Eliminar todos los permisos
    function borrarPermisos() {
        permisos.value = []
    }

    // Cargar saldo desde API
    async function cargarSaldo() {
        if (!isClient) {
            return
        }

        saldoError.value = ""
        try {
            const response = await fetch(`${getApiUrl()}/usuario/_saldo_muulares`)
            const data = await response.json()
            console.log("saldo response", data)

            if (data.error) {
                console.warn("Error al cargar el saldo:", data.error)
                saldoError.value = data.error
                saldo.value = "Error"
                return
            }

            saldo.value = data.saldo
        } catch (error) {
            console.warn("Error al cargar el saldo:", error)
            saldoError.value = error.message || error
            saldo.value = "Error"
        }
    }

    return {
        permisos,
        saldo,
        saldoError,
        cargarPermisos,
        tienePermiso,
        borrarPermisos,
        cargarSaldo,
    }
})

// Mantener compatibilidad con la API existente
export default function useUserStore() {
    return useUserStorePinia()
}
