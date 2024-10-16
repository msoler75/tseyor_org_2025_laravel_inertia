<template>
    <!-- Modal Cambiar ACL -->
    <Modal :show="modalCambiarAcl" @close="modalCambiarAcl = false" maxWidth="md">
        <div class="p-5">
            <div class="font-bold text-lg">Acceso adicional</div>
            <form class="overflow-x-auto">
                <p>/{{ itemCambiandoAcl.ruta }}</p>
                <table v-if="itemCambiandoAcl?.aclEditar?.length">
                    <thead class="text-sm">
                        <tr>
                            <th></th>
                            <th>Usuario/Grupo</th>
                            <th>Leer</th>
                            <th>Escribir</th>
                            <th v-if="itemCambiandoAcl.tipo == 'carpeta'">Listar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="acl of itemCambiandoAcl.aclEditar" :key="acl.id">
                            <td :title="acl.usuario ? 'usuario' : 'grupo'">
                                <Icon v-if="acl.usuario" icon="ph:user-duotone" />
                                <Icon v-else icon="ph:users-three-duotone" />
                            </td>
                            <td :title="acl.usuario ? 'usuario' : 'grupo'"><span class="font-bold">{{ acl.usuario ||
                                acl.grupo }}</span></td>
                            <td class="text-center"><input type="checkbox" v-model="acl.leer"></td>
                            <td class="text-center"><input type="checkbox" v-model="acl.escribir"></td>
                            <td v-if="itemCambiandoAcl.tipo == 'carpeta'" class="text-center"><input type="checkbox"
                                    v-model="acl.ejecutar"></td>
                            <td><button @click="eliminarAcl(acl.id)" title="eliminar acceso" class="flex">
                                    <Icon icon="ph:trash-duotone" />
                                </button></td>
                        </tr>
                    </tbody>
                </table>
                <div v-else>
                    No hay accesos adicionales
                </div>

                <div class="flex gap-x my-4">
                    <button class="btn btn-xs text-xs btn-secondary" @click.prevent="abrirModalBuscarUsuario">+
                        Usuario</button>
                    <button class="btn btn-xs text-xs btn-secondary" @click.prevent="abrirModalBuscarGrupo">+
                        Grupo</button>
                </div>

            </form>

            <div class="py-3 flex justify-between sm:justify-end gap-5">


                <button @click.prevent="cambiarAcl" type="button" class="btn btn-primary btn-sm"
                    :disabled="guardandoAcl">
                    <div v-if="guardandoAcl" class="flex items-center gap-x">
                        <Spinner />
                        Guardando...
                    </div>
                    <span v-else>
                        Guardar
                    </span>
                </button>

                <button @click.prevent="modalCambiarAcl = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </Modal>



    <!-- Modal buscar usuario -->
    <Modal :show="modalBuscarUsuario" @close="modalBuscarUsuario = false" max-width="sm">
        <div class="p-5">
            <div class="font-bold text-lg">Buscar usuario</div>
            <input type="search" id="buscar_usuario"
                class="input shadow flex-shrink-0 rounded-none border-b border-gray-500" placeholder="Buscar usuario..."
                v-model="usuarioBuscar">

            <div class="overflow-y-auto max-h-[200px] shadow">
                <table v-if="usuariosParaAgregar.length" class="table w-full bg-base-100 rounded-none">
                    <tbody class="divide-y">
                        <tr v-for="user of usuariosParaAgregar" :key="user.id">
                            <td>{{ user.nombre }}</td>
                            <td>
                                <div v-if="user.acceso" class="btn bg-base-100 border-none pointer-events-none">
                                    <Icon icon="ph:check-circle-duotone" /> Ya tiene acceso
                                </div>
                                <div v-else class="btn" @click="agregarUsuarioAcl(user)">
                                    Seleccionar
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else-if="usuarioBuscar" class="p-2 bg-base-100">
                    No hay resultados
                </div>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5">

                <button @click.prevent="modalBuscarUsuario = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>

            </div>
        </div>

    </Modal>



    <!-- Modal elegir grupo -->
    <Modal :show="modalBuscarGrupo" @close="modalBuscarGrupo = false" max-width="sm">
        <div class="p-5">
            <div class="font-bold text-lg">Buscar grupo</div>
            <select v-model="grupoElegido" class="select w-full border border-primary" placeholder="Elige un grupo">
                <option v-for="grupo of gruposElegibles" :key="grupo.id" :value="grupo.id">{{ grupo.nombre }}
                </option>
            </select>


            <div class="py-3 flex justify-between sm:justify-end gap-5">

                <button @click.prevent="agregarGrupoAcl" type="button" class="btn btn-primary btn-sm">
                    Elegir
                </button>

                <button @click.prevent="modalBuscarGrupo = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>

            </div>
        </div>
    </Modal>


</template>



<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
import { useDebounce } from '@vueuse/core';

let store = useFolderExplorerStore()

store.on('acceso', abrirModalCambiarAcl)

// ACL

const modalCambiarAcl = ref(false)
const itemCambiandoAcl = ref(null)
const guardandoAcl = ref(false)
// agregar usuario:
const modalBuscarUsuario = ref(false)
const usuarioBuscar = ref("")
const debouncedBuscar = useDebounce(usuarioBuscar, 800);
const usuariosEncontrados = ref([]);
const usuariosParaAgregar = computed(() => usuariosEncontrados.value
    .map(u => ({
        ...u,
        acceso: itemCambiandoAcl.value.propietario.usuario?.id == u.id  //  no es el propietario,
            || itemCambiandoAcl.value.aclEditar.find(acl => acl.user_id == u.id) // y no está ya en la lista de acceso
    })
    ))
// agregar grupo
const modalBuscarGrupo = ref(false)
const grupoElegido = ref(null)
const grupos = ref([])
const gruposElegibles = computed(() => grupos.value.filter(g => !itemCambiandoAcl.value.aclEditar.find(acl => acl.group_id == g.id)))

watch(debouncedBuscar, buscarUsuarios)

function abrirModalCambiarAcl(item) {
    itemCambiandoAcl.value = item
    if (!item.acl)
        item.acl = []
    // guardamos los valores antes de la edición
    itemCambiandoAcl.value.aclEditar = [...itemCambiandoAcl.value.acl]
    itemCambiandoAcl.value.aclEditar.forEach(acl => {
        ['leer', 'escribir', 'ejecutar'].forEach(verbo => acl[verbo] = !!acl.verbos.match(verbo))
    })
    modalCambiarAcl.value = true
    guardandoAcl.value = false
}


function eliminarAcl(id) {
    const idx = itemCambiandoAcl.value.aclEditar.findIndex(acl => acl.id == id)
    if (idx == -1) {
        alert("Hubo un error. Comunicarlo al administrador")
        return
    }
    itemCambiandoAcl.value.aclEditar.splice(idx, 1)
}

function abrirModalBuscarUsuario() {
    modalBuscarUsuario.value = true
    setTimeout(() => {
        if (modalBuscarUsuario.value)
            document.querySelector('#buscar_usuario').focus()
    }, 500)
}

function buscarUsuarios() {
    const query = debouncedBuscar.value.trim();

    if (query.length >= 3) {
        axios
            .get(route('usuarios.buscar', query))
            .then(response => {
                console.log('response', response.data)
                usuariosEncontrados.value = response.data;
            })
            .catch(error => {
                console.error(error);
            });
    }
    else usuariosEncontrados.value = []
}

function agregarUsuarioAcl(user) {
    itemCambiandoAcl.value.aclEditar.push({
        id: -10000 - user.id,
        user_id: user.id,
        leer: false,
        escribir: false,
        ejecutar: false,
        usuario: user.name
    })
    modalBuscarUsuario.value = false
    usuarioBuscar.value = ""
}


function abrirModalBuscarGrupo() {
    grupoElegido.value = null
    modalBuscarGrupo.value = true
    if (!grupos.value.length) {
        fetch(route('grupos'))
            .then(response => response.json())
            .then(response => {
                console.log({ response })
                grupos.value = response
            })
    }
}

function agregarGrupoAcl() {
    const grupo = grupos.value.find(g => g.id == grupoElegido.value)
    itemCambiandoAcl.value.aclEditar.push({
        id: -grupo.id,
        group_id: grupo.id,
        leer: false,
        escribir: false,
        ejecutar: false,
        grupo: grupo.nombre
    })
    modalBuscarGrupo.value = false
    grupoElegido.value = null
}

function cambiarAcl() {
    var newAcl = JSON.parse(JSON.stringify(itemCambiandoAcl.value.aclEditar))
    newAcl.forEach(acl => {
        acl.verbos = ['leer', 'escribir', 'ejecutar'].filter(verbo => acl[verbo]).join(',')
        delete acl.leer
        delete acl.escribir
        delete acl.ejecutar
        delete acl.usuario
        delete acl.grupo
        delete acl.updated_at
        delete acl.created_at
    })
    newAcl = newAcl.filter(acl => acl.verbos)
    guardandoAcl.value = true
    axios.post('/files/update', {
        ruta: itemCambiandoAcl.value.ruta,
        acl: newAcl
    })
        .then(response => {
            console.log({ response })
            // cierra el modal y actualiza los permisos
            itemCambiandoAcl.value.acl = response.data.acl
            modalCambiarAcl.value = false
            store.permisosModificados = true
        })
        .catch(err => {
            const errorMessage = err.response.data.error || 'Ocurrió un error al guardar los cambios'
            alert(errorMessage)
            guardandoAcl.value = false
        })
}



</script>


<style scoped>

table td,
table th {
    @apply px-2;
}
</style>
