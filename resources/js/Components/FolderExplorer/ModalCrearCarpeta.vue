<template>
    <!-- Modal Crear Carpeta -->
    <Modal :show="store.modalCrearCarpeta" @close="store.modalCrearCarpeta = false" maxWidth="sm">

        <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
            @submit.prevent="store.crearCarpeta">

            <div class="flex flex-col gap-4">
                <label for="nombreCarpeta">Nombre de la nueva carpeta:</label>
                <input id="nombreCarpeta" v-model="store.nombreCarpeta" type="text" required>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="store.crearCarpeta" type="button" class="btn btn-primary btn-sm"
                    :disabled="store.creandoCarpeta">
                    <div v-if="store.creandoCarpeta" class="flex items-center gap-x">
                        <Spinner />
                        Creando...
                    </div>
                    <span v-else>
                        Crear Carpeta
                    </span>
                </button>

                <button @click.prevent="store.modalCrearCarpeta = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>
            </div>
        </form>
    </Modal>
</template>



<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
let store = useFolderExplorerStore()
</script>
