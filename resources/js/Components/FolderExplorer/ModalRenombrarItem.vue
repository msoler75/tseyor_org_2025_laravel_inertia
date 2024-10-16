<template>

    <!-- Modal Renombrar Item -->
    <Modal :show="store.modalRenombrarItem" @close="store.modalRenombrarItem = false">

        <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
            @submit.prevent="store.renombrarItem">
            <div class="flex flex-col gap-4">
                <label for="nuevoNombre">Nuevo nombre:</label>
                <div class="flex items-center gap-1 flex-wrap">
                    <div>{{ store.itemRenombrando.carpeta }}/</div>
                    <input id="nuevoNombre" v-model="store.nuevoNombre" type="text" required class="max-w-[32ch]">
                </div>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5">

                <button @click.prevent="store.renombrarItem" type="button" class="btn btn-primary btn-sm"
                    :disabled="store.renombrandoItem">
                    <div v-if="store.renombrandoItem" class="flex items-center gap-x">
                        <Spinner />
                        Renombrando...
                    </div>
                    <span v-else>
                        Renombrar
                    </span>
                </button>

                <button @click.prevent="store.modalRenombrarItem = false" type="button" class="btn btn-neutral btn-sm">
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
