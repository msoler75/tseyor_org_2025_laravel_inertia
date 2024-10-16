<template>
    <!-- Modal Crear Carpeta -->
    <Modal :show="modalCrearCarpeta" @close="modalCrearCarpeta = false" maxWidth="sm">

        <form class="p-7 space-y-7" role="dialog" aria-modal="true" aria-labelledby="modal-headline"
            @submit.prevent="crearCarpeta">

            <div class="flex flex-col gap-4">
                <label for="nombreCarpeta">Nombre de la nueva carpeta:</label>
                <input id="nombreCarpeta" v-model="nombreCarpeta" type="text" required>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="crearCarpeta" type="button" class="btn btn-primary btn-sm"
                    :disabled="creandoCarpeta">
                    <div v-if="creandoCarpeta" class="flex items-center gap-x">
                        <Spinner />
                        Creando...
                    </div>
                    <span v-else>
                        Crear Carpeta
                    </span>
                </button>

                <button @click.prevent="modalCrearCarpeta = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>
            </div>
        </form>
    </Modal>
</template>



<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
const store = useFolderExplorerStore()

store.on('crearCarpeta', abrirModalCrearCarpeta)

const modalCrearCarpeta = ref(false)
const nombreCarpeta = ref("")
const creandoCarpeta = ref(false)

function abrirModalCrearCarpeta() {
    creandoCarpeta.value = false;
    modalCrearCarpeta.value = true;
    nombreCarpeta.value = "";
    setTimeout(() => {
        if (modalCrearCarpeta.value) {
            const elem = document.querySelector("#nombreCarpeta");
            if (elem) elem.focus();
        }
    }, 500);
}

function crearCarpeta() {
    console.log('crearCarpeta')
    creandoCarpeta.value = true;
    if (!nombreCarpeta.value) return;

    axios
        .put("/files/mkdir", {
            folder: store.rutaActual,
            name: nombreCarpeta.value,
        })
        .then((response) => {
            console.log({ response });
            modalCrearCarpeta.value = false;
            store.actualizar();
        })
        .catch((err) => {
            console.log({ err });
            alert(err.response.data.error);
            creandoCarpeta.value = false;
        });
}

</script>
