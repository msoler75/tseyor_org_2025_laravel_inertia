<template>
    <!-- Modal Confirmación de eliminar Archivo -->
    <ConfirmationModal :show="modalEliminarItem" @close="modalEliminarItem = false">
        <template #title>
            <div v-if="itemAEliminar">Confirmación de eliminación</div>
            <div v-else>Eliminar {{ plural(store.itemsSeleccionados.length, 'elemento') }}</div>
        </template>
        <template #content>
            <div v-if="itemAEliminar">
                ¿Quieres eliminar {{ itemAEliminar.nombre }}?
            </div>
            <div v-else>
                ¿Quieres eliminar {{ plural(store.itemsSeleccionados.length, 'elemento') }}?
            </div>
        </template>
        <template #footer>
            <form class="w-full space-x-4" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
                @submit.prevent="eliminarArchivos">

                <button @click.prevent="modalEliminarItem = false" type="button" class="btn btn-neutral btn-sm">
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
import { plural } from '@/composables/textutils'

const store = useFolderExplorerStore()

store.on('eliminar', abrirEliminarModal)

const itemAEliminar = ref(null)
const modalEliminarItem = ref(false)


// eliminar item
function abrirEliminarModal(item) {
    itemAEliminar.value = item;
    modalEliminarItem.value = true;
}

function eliminarArchivos() {
    console.log("eliminarArchivos");
    if (itemAEliminar.value) eliminarArchivo(itemAEliminar.value);
    else {
        for (var item of store.itemsSeleccionados)
            eliminarArchivo(item);
    }
    modalEliminarItem.value = false;
}

function eliminarArchivo(item) {
    console.log("eloiminar¡", item);
    const url =
        "/files" + ("/" + item.ruta).replace(/\/\//g, "/").replace(/%2F/g, "/");
    console.log({ url });
    return axios
        .delete(url)
        .then((response) => {
            item.eliminado = true;
            store.seleccionando = false
        })
        .catch((err) => {
            const errorMessage =
                err.response.data.error ||
                "Ocurrió un error al eliminar el archivo " + item.nombre;
            alert(errorMessage);
        });
}



</script>
