import {ref} from "vue";
//import {createGlobalState} from  "@vueuse/core"
import { defineStore } from "pinia";

//export const useGlobalState = createGlobalState(() => {
export const usePermisos = defineStore("permisos", () => {
    const permisos = ref([]); // permisos de usuario

    function cargarPermisos() {
        axios.get(route('usuario.permisos'))
        .then(response=>{
            console.log('permisos response', response)
            permisos.value = response.data
        })
    }

    cargarPermisos()

    function tienePermiso(permiso) {
        return permisos.value.includes(permiso)
    }

    return { permisos, cargarPermisos, tienePermiso };
});
