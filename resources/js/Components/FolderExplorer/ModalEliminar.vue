<template>
    <!-- Modal Confirmación de eliminar Archivo -->
    <ConfirmationModal :show="store.modalEliminarItem" @close="store.modalEliminarItem = false">
        <template #title>
            <div v-if="store.itemAEliminar">Confirmación de eliminación</div>
            <div v-else>Eliminar {{ plural(store.itemsSeleccionados.length, 'elemento') }}</div>
        </template>
        <template #content>
            <div v-if="store.itemAEliminar">
                ¿Quieres eliminar {{ store.itemAEliminar.nombre }}?
            </div>
            <div v-else>
                ¿Quieres eliminar {{ plural(store.itemsSeleccionados.length, 'elemento') }}?
            </div>
        </template>
        <template #footer>
            <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="store.eliminarArchivos">

                <button @click.prevent="store.modalEliminarItem = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>

                <button type="submit" class="btn btn-primary btn-sm">
                    Eliminar
                </button>
            </form>
        </template>
    </ConfirmationModal>
</template>


<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
import {plural} from '@/composables/textutils'

let store = useFolderExplorerStore()
</script>
