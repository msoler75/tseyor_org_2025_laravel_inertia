import {ref} from "vue";
import {createGlobalState} from  "@vueuse/core"

export const useGlobalState = createGlobalState(() => {
    const permisos = ref([]); // permisos de usuario

    function cargarPermisos() {
        axios.get(route('usuario.permisos'))
        .then(response=>{
            //console.log('permisos response', response)
            permisos.value = response.data
        })
    }

    cargarPermisos()

    return { permisos, cargarPermisos };
});
