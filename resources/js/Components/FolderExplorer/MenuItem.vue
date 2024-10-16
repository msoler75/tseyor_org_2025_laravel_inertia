<template>

    <Dropdown align="right" width="48" v-if="item.tipo !== 'disco'"
        :class="[!store.infoCargada || !item.puedeLeer ? 'opacity-0 pointer-events-none' : '', store.seleccionando ? 'hide-if-touchable' : '']"
        menu>
        <template #trigger>
            <span class="cursor-pointer">
                <Icon :icon="vertical?'mdi:dots-vertical':'mdi:dots-horizontal'" class="text-xl" />
            </span>
        </template>

        <template #content>
            <div class="select-none">
                <div v-if="(store.esAdministrador || item.puedeEscribir) && !store.seleccionando"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer"
                    @click="store.call('renombrar',item)">
                    <Icon icon="ph:cursor-text-duotone" />
                    <span>Renombrar</span>
                </div>

                <div v-if="(store.esAdministrador || item.puedeEscribir) && !store.seleccionando && !item.padre"
                    class="flex gap-x  items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                    @click="store.call('eliminar',item)">
                    <Icon icon="ph:trash-duotone" />
                    <span>Eliminar</span>
                </div>

                <div v-if="(store.esAdministrador || item.puedeLeer) && !store.buscandoCarpetaDestino && !item.padre"
                    class="flex gap-x  items-center px-4 py-2  hover:bg-base-100 cursor-pointer"
                    @click="store.seleccionando = true; item.seleccionado = !item.seleccionado">
                    <template v-if="!item.seleccionado">
                        <Icon icon="ph:check-fat-duotone" />
                        <span>Seleccionar</span>
                    </template>
                    <template v-else>
                        <Icon icon="ph:square" />
                        <span>Deseleccionar</span>
                    </template>
                </div>

                <div v-if="store.seleccionando"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                    @click="cancelarSeleccion">
                    <Icon icon="ph:x-square-duotone" />
                    <span>Cancelar selección</span>
                </div>

                <a :href="item.url"
                    class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                    download>
                    <Icon icon="ph:download" />
                    <span>Descargar</span>
                </a>


                <div class="flex gap-x items-center px-4 py-2 hover:bg-base-100 cursor-pointer whitespace-nowrap"
                    @click.prevent="store.call('propiedades', item)">
                    <Icon icon="ph:info-duotone" />
                    <span>Propiedades</span>
                </div>

            </div>

        </template>
    </Dropdown>
</template>

<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';

const props = defineProps({
    item: Object,
    vertical: {type: Boolean, default: true}
})

let store = useFolderExplorerStore()

</script>
