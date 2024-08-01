<template>
    <Modal :show="modalMiembros" maxWidth="sm" @close="modalMiembros = false">
        <div class="bg-base-200 p-5 flex flex-col gap-5 max-h-[90vh] select-none">
            <h3>Miembros del Equipo</h3>

            <input type="search" class="input shadow flex-shrink-0" placeholder="Buscar..." v-model="miembroBuscar">

            <div class="overflow-y-auto bg-base-100 shadow" v-if="miembrosListado.length">
                <table class="table w-full">
                    <tbody class="divide-y">
                        <tr v-for="user of miembrosListado" :key="user.id" class="cursor-pointer"
                            :class="user.pivot.rol == 'coordinador' ? 'bg-blue-50 dark:bg-blue-800' : ''">
                            <td>{{ user.nombre }}</td>
                            <td>
                                <select v-model="user.pivot.rol" class="select" @change="cambiarRol(user)">
                                    <option value="coordinador">coordinador</option>
                                    <option value="miembro">miembro</option>
                                    <option v-if="user.pivot.rol != 'coordinador'" value="eliminar">eliminar</option>
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
    <ConfirmationModal :show="confirmarEliminar != null" @close="confirmarEliminar = null" centered>
        <template #title>
            Eliminar usuario del equipo
        </template>

        <template #content>
            <div>¿Quieres eliminar a
                <User v-if="confirmarEliminar" :user="confirmarEliminar" /> del equipo?
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="confirmarEliminar = null">
                Cancelar
            </SecondaryButton>

            <DangerButton class="ml-3" @click="eliminarMiembro">
                Eliminar
            </DangerButton>
        </template>
    </ConfirmationModal>


    <!-- Confirmación de degradación de coordinador -->
    <ConfirmationModal :show="confirmarDegradacion != null" @close="confirmarDegradacion = null" centered>
        <template #title>
            Confirmación de cambio
        </template>

        <template #content>
            <p v-if="confirmarDegradacion.id == $page.props.auth.user.id" class="text-error">
                ¿Quieres dejar de ser coordinador/a de este equipo?
            </p>
            <p v-else>
                ¿Quieres remover los permisos de coordinador/a a
                <User v-if="confirmarDegradacion" :user="confirmarDegradacion" />?
            </p>
            <p v-if="coordinadores.length == 1">Si lo haces el equipo se quedará sin coordinadores, y se asignará la
                coordinación al miembro más antiguo.</p>
        </template>

        <template #footer>
            <SecondaryButton @click="confirmarDegradacion = null">
                Cancelar
            </SecondaryButton>

            <DangerButton class="ml-3" @click="degradarCoordinador">
                <span v-if="confirmarDegradacion.id == $page.props.auth.user.id">Dejar la coordinación</span>
                <span v-else>Remover</span>
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

const emit = defineEmits(['updated'])

const page = usePage()
const prevState = ref({}) // para guardar el estado previo de cada usuario en el select
const confirmarEliminar = ref(null)
const confirmarDegradacion = ref(null)

// Diálogo de ADMINISTRAR Miembros

const modalMiembros = ref(false)
const miembroBuscar = ref("")
const usuariosFuse = useFuse(miembroBuscar, () => props.equipo.miembros, { fuseOptions: { keys: ['nombre', 'email'], threshold: 0.3 } })
const coordinadores = computed(() => props.equipo ? props.equipo.miembros.filter(u => u.pivot.rol == 'coordinador') : [])

const miembrosFiltrado = computed(() => {
    if (!props.equipo.miembros) return []
    return miembroBuscar.value ?
        usuariosFuse.results.value.map(x => x.item)
        : props.equipo.miembros
});

//
const miembrosListado = computed(() => miembrosFiltrado.value.map(u => {
    if (u.pivot.rol === null) {
        u.pivot.rol = '';
    }
    return u;
})
    .sort((a, b) => (a.pivot.rol == 'coordinador' ? -1 : 0) - (b.pivot.rol == 'coordinador' ? -1 : 0)));

// mostrar modal
function mostrar() {
    miembroBuscar.value = ''
    modalMiembros.value = true
}

function cambiarRol(user) {
    console.log('changedRol', prevState.value[user.id], '->', user.pivot.rol)
    console.log('coordinadores', coordinadores.value.length)
    if (user.pivot.rol == 'eliminar') {
        confirmarEliminar.value = user
        // restauramos su estado actual
        user.pivot.rol = prevState.value[user.id]
    }
    else if (prevState.value[user.id] == 'coordinador') {
        console.log('era coordinador')
        // para degradarnos a nosotros mismos hemos de confirmar tal acción
        // también hemos de confirmarlo si ya solo queda un solo coordinador
        if ((page.props.auth.user && page.props.auth.user.id == user.id) ||
            coordinadores.value.length == 0
        ) {
            console.log('modal confirmación degradar')
            confirmarDegradacion.value = user

            // restauramos su estado actual
            user.pivot.rol = prevState.value[user.id]
        }
        else modificarRol(user)
    }
    else modificarRol(user)
}

function modificarRol(user) {
    console.log('modificarRol', user.id, user.pivot.rol)
    axios.put(route('equipo.modificarRol', { idEquipo: props.equipo.id, idUsuario: user.id, rol: user.pivot.rol || 'miembro' }))
        .then(response => {
            console.log({ response })
            prevState.value[user.id] = user.pivot.rol
            if (response.data.nuevoCoordinador) {
                const coordinador = props.equipo.miembros.find(u => u.id == response.data.nuevoCoordinador)
                if (coordinador) {
                    coordinador.pivot.rol = 'coordinador'
                    alert((coordinador.name || coordinador.nombre) + ' ha adoptado el rol de coordinación del equipo')
                }
                emit('updated')
            }
        })
        .catch(err => {
            console.log({ err })
            alert(err.response.data.error || 'Hubo un error')
            // restauramos el estado previo
            user.pivot.rol = prevState.value[user.id]
        });
}


function degradarCoordinador() {
    // marcamos el nuevo rol que queremos para el usuario
    confirmarDegradacion.value.pivot.rol = ''
    // llamamos al servidor
    modificarRol(confirmarDegradacion.value)
    // anulamos el modal de confirmación
    confirmarDegradacion.value = null
    // si ya no somos coordinadores, cerramos el modal
    modalMiembros.value = false
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
    const idx = props.equipo.miembros.findIndex(u => u.id == confirmarEliminar.value.id)
    axios.put(route('equipo.remover', { idEquipo: props.equipo.id, idUsuario: confirmarEliminar.value.id }))
        .then(respuesta => {
            if (idx > -1)
                props.equipo.miembros.splice(idx, 1)
            //reload()
            emit('updated')
            console.log(respuesta.data)
        })
        .catch(err => {
            console.log({ err })
            alert(err.response.data.error || 'Hubo un error')
        })

    confirmarEliminar.value = null
}

</script>
