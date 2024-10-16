<template>
 <!-- Modal Propiedades -->
 <Modal :show="modalPropiedades" @close="modalPropiedades = false">
    <div class="p-5">
        <div v-for="item, index of itemsPropiedades" :key="item.url" class="pb-4"
            :class="index > 0 ? 'pt-4 border-t border-base-200' : ''">
            <table class="propiedades border-separate border-spacing-y-3">
                <tbody>
                    <tr>
                        <th>Archivo </th>
                        <td>{{ item.nombre }}</td>
                    </tr>
                    <tr>
                        <th>Carpeta</th>
                        <td>{{ item.carpeta == '.' ? '' : item.carpeta }}</td>
                    </tr>
                    <tr>
                        <th>Ruta completa</th>
                        <td>/{{ item.ruta }}</td>
                    </tr>
                    <tr>
                        <th>Propietario</th>
                        <td class="flex flex-wrap gap-x items-center">
                            <span class="flex items-center gap-x" title="usuario">
                                <Icon icon="ph:user-duotone" /> {{ item.propietario?.usuario.nombre }}
                            </span>
                            <span class="opacity-30">|</span>
                            <span class="flex items-center gap-x" title="grupo">
                                <Icon icon="ph:users-three-duotone" /> {{ item.propietario?.grupo.nombre }}
                            </span>

                            <div v-if="item.propietario?.usuario.id == user?.id"
                                class="badge badge-warning text-xs whitespace-nowrap">
                                Eres el propietario</div>
                        </td>
                    </tr>
                    <tr v-if="!store.embed && store.propietarioRef">
                        <th><span v-if="store.propietarioRef.tipo == 'equipo'">Equipo propietario</span>
                            <span v-else>Usuario propietario</span>
                        </th>
                        <td class="flex flex-wrap gap-x items-center">
                            <Icon
                                :icon="store.propietarioRef.tipo == 'equipo' ? 'ph:users-four-duotone' : 'ph:user-duotone'" />
                            <span>{{ store.propietarioRef.nombre }}</span>
                            <Link
                                class="flex gap-x items-center btn btn-xs text-xs btn-neutral whitespace-nowrap"
                                :href="store.propietarioRef.url" :title="store.tituloPropietario">
                            <span v-if="store.propietarioRef.tipo == 'equipo'">Ver equipo</span>
                            <span v-else>Ver usuario</span>
                            </Link>
                        </td>
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td>
                            <TimeAgo :date="item.fecha_modificacion" />
                        </td>
                    </tr>
                    <tr>
                        <th>Nodo ID</th>
                        <td>{{ item.nodo_id }}</td>
                    </tr>
                    <tr>
                        <th class="align-top">Permisos</th>
                        <td>
                            <PermisosNodo :es-carpeta="item.tipo != 'archivo'" :permisos="item.permisos" />
                            <button v-if="store.esAdministrador || item.propietario?.usuario.id == user?.id"
                                class="my-2 btn btn-xs btn-secondary text-xs"
                                @click="store.call('permisos', item)">Cambiar permisos</button>
                        </td>
                    </tr>
                    <tr>
                        <th class="align-top">Acceso adicional</th>
                        <td>
                            <div v-if="item.acl && item.acl.length">
                                <div v-for="acl of item.acl" :key="acl.id" class="flex gap-1 items-center">
                                    <PermisosAcl :acl="acl" :tipo="item.tipo" />
                                </div>
                            </div>
                            <div v-else>
                                No hay
                            </div>
                            <button v-if="store.esAdministrador || item.propietario?.usuario.id == user?.id"
                                class="my-2 btn btn-xs btn-secondary text-xs"
                                @click="store.call('acceso', item)">Cambiar acceso</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="py-3 flex justify-between sm:justify-end gap-5">
            <button @click.prevent="modalPropiedades = false" type="button" class="btn btn-neutral btn-sm">
                Cerrar
            </button>
        </div>
    </div>
</Modal>
</template>

<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
let store = useFolderExplorerStore()

const page = usePage()
const user = computed(() => page?.props?.auth?.user)

store.on('propiedades', abrirModalPropiedades)
store.permisosModificados = false

// PROPIEDADES

const modalPropiedades = ref(false)
const itemsPropiedades = ref(null)
function abrirModalPropiedades(item) {
    itemsPropiedades.value = item ? [item] : store.itemsSeleccionados
    modalPropiedades.value = true
}

watch(modalPropiedades, (newValue) => {
    if (!newValue && store.permisosModificados) {
        // actualizamos vista de la carpeta
        store.actualizar()
    }
})

</script>

<style scoped>
.propiedades th {
    text-align: left
}
</style>
