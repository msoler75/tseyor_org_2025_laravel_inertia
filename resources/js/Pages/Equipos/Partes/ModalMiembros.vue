<template>
    <Modal :show="modalMiembros" maxWidth="sm" @close="modalMiembros = false">
        <div class="bg-base-200 p-5 flex flex-col gap-5 max-h-[90vh] select-none">
            <h3>Miembros del Equipo</h3>

            <input type="search" class="input shadow flex-shrink-0" placeholder="Buscar..." v-model="miembroBuscar">

            <div class="overflow-y-auto bg-base-100 shadow">
                <table class="table w-full">
                    <tbody class="divide-y">
                        <tr v-for="user of miembrosFiltrado" :key="user.id" class="cursor-pointer"
                            :class="user.pivot.rol == 'coordinador' ? 'bg-blue-50' : ''">
                            <td>{{ user.nombre }}</td>
                            <td>
                                <select v-model="user.pivot.rol" class="select" @change="changeRol(user)">
                                    <option value="coordinador">coordinador</option>
                                    <option value=""><span class="opacity-50">miembro</span></option>
                                    <option value="eliminar">eliminar</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="modalMiembros = false" type="button" class="btn btn-neutral">
                    cerrar
                </button>
            </div>
        </div>
    </Modal>
</template>


<script setup>
import { useFuse } from '@vueuse/integrations/useFuse'

defineExpose({
    mostrar
});

const props = defineProps({ equipo: { type: Object, required: true } })

// Diálogo de ADMINISTRAR Miembros

const modalMiembros = ref(false)
const miembroBuscar = ref("")
const usuariosFuse = useFuse(miembroBuscar, () => props.equipo.miembros, { fuseOptions: { keys: ['nombre', 'email'], threshold: 0.3 } })

const miembrosFiltrado = computed(() => {
    if (!props.equipo.miembros) return []
    if (miembroBuscar.value)
        return usuariosFuse.results.value.map(r => ({ id: r.item.id, nombre: r.item.nombre /* +r.refIndex*/, pivot: r.item.pivot, rol: ''+r.item.pivot.rol }))
    return props.equipo.miembros.map(u=>({...u, rol: ''+u.pivot.rol}))
});

// mostrar modal
function mostrar() {
    miembroBuscar.value = ''
    modalMiembros.value = true
}

function changeRol(user) {
    console.log('changedRol', user.rol, '->', user.pivot.rol)
    axios.put(route('equipo.modificarRol', { idEquipo: props.equipo.id, idUsuario: user.id, rol: user.pivot.rol || 'miembro' }))
        .catch(err => {
            alert("No se han podido guardar los cambios")
        });
}

</script>
