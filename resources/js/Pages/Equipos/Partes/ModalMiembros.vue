<template>
    <Modal :show="modalMiembros" maxWidth="sm" @close="modalMiembros = false">
        <div class="bg-base-200 p-5 flex flex-col gap-5 max-h-[90vh] select-none">
            <h3>Miembros del Equipo</h3>

            <input type="search" class="input shadow flex-shrink-0" placeholder="Buscar..." v-model="miembroBuscar">

            <div class="overflow-y-auto bg-base-100 shadow" v-if="miembrosFiltrado.length">
                <table class="table w-full">
                    <tbody class="divide-y">
                        <tr v-for="user of miembrosFiltrado" :key="user.id" class="cursor-pointer"
                            :class="user.pivot.rol == 'coordinador' ? 'bg-blue-50' : ''">
                            <td>{{ user.nombre }}</td>
                            <td>
                                <select v-model="user.pivot.rol" class="select" @change="cambiarRol(user)">
                                    <option value="coordinador">coordinador</option>
                                    <option value="">miembro</option>
                                    <option v-if="user.pivot.rol!='coordinador'" value="eliminar">eliminar</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="py-3" v-else-if="miembroBuscar">
                No hay usuarios con ese nombre</div>


            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="modalMiembros = false" type="button" class="btn btn-neutral">
                    cerrar
                </button>
            </div>
        </div>
    </Modal>


    <!-- Confirmación de eliminación de miembro -->
    <ConfirmationModal :show="usuarioEliminar != null" @close="usuarioEliminar = null" centered>
        <template #title>
            Eliminar usuario del equipo
        </template>

        <template #content>
            <div>¿Quieres eliminar a
                <User v-if="usuarioEliminar" :user="usuarioEliminar" /> del equipo?
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="usuarioEliminar = null">
                Cancelar
            </SecondaryButton>

            <DangerButton class="ml-3" @click="eliminarMiembro">
                Eliminar
            </DangerButton>
        </template>
    </ConfirmationModal>


     <!-- Confirmación de degradación de coordinador -->
     <ConfirmationModal :show="coordinadorDegradar != null" @close="coordinadorDegradar = null" centered>
        <template #title>
            Quitar permisos de coordinador/a
        </template>

        <template #content>
            <div v-if="coordinadorDegradar.id==$page.props.user.id">
                ¿Quieres dejar de ser coordinador/a de este equipo?
            </div>
            <div v-else>
                ¿Quieres remover los permisos de coordinador/a a
                <User v-if="coordinadorDegradar" :user="coordinadorDegradar" />?
            </div>
            <p  v-if="coordinadores.length==1">Si lo haces el equipo se quedará sin coordinadores, y se asignará la coordinación al miembro más antiguo.</p>
        </template>

        <template #footer>
            <SecondaryButton @click="coordinadorDegradar = null">
                Cancelar
            </SecondaryButton>

            <DangerButton class="ml-3" @click="degradarCoordinador">
                Quitar permisos
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>


<script setup>
import { useFuse } from '@vueuse/integrations/useFuse'

defineExpose({
    mostrar
});

const props = defineProps({ equipo: { type: Object, required: true } })
const page = usePage()
const prevState = ref({}) // para guardar el estado previo de cada usuario en el select
const usuarioEliminar = ref(null)
const coordinadorDegradar = ref(null)

// Diálogo de ADMINISTRAR Miembros

const modalMiembros = ref(false)
const miembroBuscar = ref("")
const usuariosFuse = useFuse(miembroBuscar, () => props.equipo.miembros, { fuseOptions: { keys: ['nombre', 'email'], threshold: 0.3 } })
const coordinadores = computed(()=>equipo.miembros.filter(u=>u.pivot.rol=='coordinador'))

const miembrosFiltrado = computed(() => {
    if (!props.equipo.miembros) return []
    if (miembroBuscar.value)
        return usuariosFuse.results.value.map(r => ({ id: r.item.id, nombre: r.item.nombre /* +r.refIndex*/, pivot: r.item.pivot, rol: '' + r.item.pivot.rol }))
    return props.equipo.miembros.map(u => ({ ...u, rol: '' + u.pivot.rol }))
});

// mostrar modal
function mostrar() {
    miembroBuscar.value = ''
    modalMiembros.value = true
}

function cambiarRol(user) {
    console.log('changedRol', prevState.value[user.id], '->', user.pivot.rol)
    console.log('coordinadores', coordinadores.value.length)
    if (user.pivot.rol == 'eliminar') {
        usuarioEliminar.value = user
        // restauramos su estado actual
        user.pivot.rol = prevState.value[user.id]
    }
    else if (user.pivot.rol == 'coordinador') {
        // para degradarnos a nosotros mismos hemos de confirmar tal acción
        // también hemos de confirmarlo si ya solo queda un solo coordinador
        if((page.props.user && coordinadores.value.includes(page.props.user.id)) ||
        coordinadores.value.length==1
        ) {
            coordinadorDegradar.value = user

            // restauramos su estado actual
            user.pivot.rol = prevState.value[user.id]
        }
    }
    else modificarRol(user)
}

function modificarRol(user) {
    axios.put(route('equipo.modificarRol', { idEquipo: props.equipo.id, idUsuario: user.id, rol: user.pivot.rol || 'miembro' }))
            .then(response => {
                prevState.value[user.id] = user.pivot.rol
                if(response.data.nuevoCoordinador) {
                    const idx = props.equipo.miembros.findIndex(u=>u.id==nuevoCoordinador)
                    if(idx>-1) {
                        const u = props.equipo.miembros[idx]
                        u.pivot.rol='coordinador'
                        alert(u.name||u.nombre + ' ha adoptado el rol de coordinación del equipo')
                    }
                }
            })
            .catch(err => {
                alert("No se han podido guardar los cambios")
            });
}


function degradarCoordinador() {
    coordinadorDegradar.value.pivot.rol = null
    modificarRol(coordinadorDegradar.value)
    coordinadorDegradar.value = null
}


// guardamos el estado de cada miembro, porque el select lo cambiará y a veces queremos cancelar el cambio
function guardaEstadoPrevio() {
    prevState.value = {}
    for (var usuario of props.equipo.miembros) {
        prevState.value[usuario.id] = usuario.pivot.rol
    }
}

watch(() => props.equipo, () => guardaEstadoPrevio())

onMounted(() => guardaEstadoPrevio())

function eliminarMiembro() {
    const idx = props.equipo.miembros.findIndex(u => u.id == usuarioEliminar.value.id)
    axios.put(route('equipo.remover', { idEquipo: props.equipo.id, idUsuario: usuarioEliminar.value.id }))
        .then(respuesta => {
            if (idx > -1)
                props.equipo.miembros.splice(idx, 1)
            //reload()
            console.log(respuesta.data)
        })
        .catch(err => {
            console.log({ err })
            alert("Hubo un error")
        })
    usuarioEliminar.value = null
}



</script>
