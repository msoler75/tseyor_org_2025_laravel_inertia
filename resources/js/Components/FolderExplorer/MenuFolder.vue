<template>
    <Dropdown v-if="!store.enRaiz && !store.seleccionando" align="right" width="48"
        :class="!store.infoCargada ? 'opacity-50 pointer-events-none' : ''">
        <template #trigger>
            <div class="btn btn-neutral btn-sm cursor-pointer">
                <Icon icon="mdi:dots-vertical" class="transform scale-150" />
            </div>
        </template>

        <template #content>
            <div class="select-none">
                <!-- Account Management -->
                <div v-if="store.puedeEscribir && !store.seleccionando"
                    class="flex gap-x items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                    @click="store.call('subirArchivos')">
                    <Icon icon="ph:upload-duotone" />
                    <span>Subir archivos</span>
                </div>

                <div v-if="store.items[1]?.padre && (store.esAdministrador || store.items[1]?.puedeEscribir) && !store.seleccionando && store.itemsShow[0].ruta != 'archivos' && store.itemsShow[0].ruta != 'medios'"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                    @click="store.call('renombrar', store.itemsShow[0])">
                    <Icon icon="ph:cursor-text-duotone" />
                    <span>Renombrar</span>
                </div>

                <div v-if="store.puedeEscribir && !store.seleccionando"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                    @click="store.call('crearCarpeta')">
                    <Icon icon="ph:folder-plus-duotone" />
                    <span>Crear carpeta</span>
                </div>

                <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                @click="store.call('buscar')">
                    <Icon icon="ph:magnifying-glass-duotone" />
                    <span>Buscar</span>
                </div>

                <div v-if="!store.enRaiz && store.puedeLeer && !store.seleccionando && store.itemsShow.filter(x => !x.padre).length > 1"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                    @click="store.seleccionando = true; store.seleccionAbierta = true">
                    <Icon icon="ph:check-duotone" />
                    <span>Abrir Selección</span>
                </div>

                <div v-else-if="store.seleccionando"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                    @click="store.cancelarSeleccion">
                    <Icon icon="ph:x-square-duotone" />
                    <span>Cancelar selección</span>
                </div>

                <Link v-if="!store.embed && store.propietarioRef && !store.seleccionando"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                    :href="store.propietarioRef.url" :title="store.tituloPropietario">
                <Icon :icon="store.propietarioRef.tipo == 'equipo' ? 'ph:users-four-duotone' : 'ph:user-duotone'" />
                <span v-if="store.propietarioRef.tipo == 'equipo'">Ver equipo</span>
                <span v-else>Ver usuario</span>
                </Link>

                <Share>
                    <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap">
                        <Icon icon="ph:share-network-duotone" />
                        <span>Compartir</span>
                    </div>
                </Share>


                <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                    @click.prevent="store.call('propiedades', store.itemsShow[0])">
                    <Icon icon="ph:info-duotone" />
                    <span>Propiedades</span>
                </div>

            </div>

        </template>
    </Dropdown>
</template>




<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
const store = useFolderExplorerStore()
</script>
