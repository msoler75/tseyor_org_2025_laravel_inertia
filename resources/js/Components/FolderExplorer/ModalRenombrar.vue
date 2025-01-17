<template>
    <!-- Modal Renombrar Item -->
    <Modal :show="modalRenombrarItem" @close="modalRenombrarItem = false">
        <form
            class="p-7 space-y-7"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-headline"
            @submit.prevent="renombrarItem"
        >
            <div class="flex flex-col gap-4">
                <label for="nuevoNombre">Nuevo nombre:</label>
                <div class="flex items-center gap-1 flex-wrap">
                    <div>{{ itemRenombrando.carpeta }}/</div>
                    <input
                        id="nuevoNombre"
                        v-model="nuevoNombre"
                        type="text"
                        required
                        class="max-w-[32ch]"
                    />
                </div>
            </div>

            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button
                    @click.prevent="renombrarItem"
                    type="button"
                    class="btn btn-primary btn-sm"
                    :disabled="renombrandoItem"
                >
                    <div v-if="renombrandoItem" class="flex items-center gap-x">
                        <Spinner />
                        Renombrando...
                    </div>
                    <span v-else> Renombrar </span>
                </button>

                <button
                    @click.prevent="modalRenombrarItem = false"
                    type="button"
                    class="btn btn-neutral btn-sm"
                >
                    Cancelar
                </button>
            </div>
        </form>
    </Modal>
</template>

<script setup>
import useFolderExplorerStore from "@/Stores/folderExplorer";
const store = useFolderExplorerStore();

store.on("renombrar", abrirModalRenombrar);

const nuevoNombre = ref("");
const itemRenombrando = ref(null);
const modalRenombrarItem = ref(false);
const renombrandoItem = ref(false);

// operaciones de renombrar
function abrirModalRenombrar(item) {
    // item.seleccionado = false // para el caso de renombrar un item seleccionado
    renombrandoItem.value = false;
    itemRenombrando.value = item;
    nuevoNombre.value = item.nombre_original
        ? item.nombre_original
        : item.nombre;
    modalRenombrarItem.value = true;
    // establecemos focus en el input
    setTimeout(() => {
        if (modalRenombrarItem.value) {
            const elem = document.querySelector("#nuevoNombre");
            if (elem) elem.focus();
        }
    }, 500);
}

function renombrarItem() {
    const {nombre:_nombre, url:_url, ruta:_ruta} =  itemRenombrando.value
    console.log('renombrarItem()', _nombre, _url, _ruta);
    itemRenombrando.value.seleccionado = false;
    renombrandoItem.value = true;
    axios
        .post("/files/rename", {
            folder: itemRenombrando.value.carpeta,
            oldName: itemRenombrando.value.nombre,
            newName: nuevoNombre.value,
        })
        .then((response) => {
            console.log({ response });
            modalRenombrarItem.value = false;
            itemRenombrando.value.ruta = itemRenombrando.value.carpeta + "/" + nuevoNombre.value;
            itemRenombrando.value.url = itemRenombrando.value.url.replace(new RegExp(itemRenombrando.value.nombre + '(?=[^/]*$)'), nuevoNombre.value);
            itemRenombrando.value.nombre = nuevoNombre.value;
            const {nombre:_nombre, url:_url, ruta:_ruta} =  itemRenombrando.value
            console.log('renombrarItem_END()', _nombre, _url, _ruta);
            const item = itemRenombrando.value
            if (!store.embed)
                if (item.actual) {
                    // reemplazar la URL actual en el historial del navegador
                    router.replace(item.url);

                    // reemplazar el título de la página
                    document.title = item.ruta;
                }
            // else
            // actualizarPage()
        })
        .catch((err) => {
            console.warn({ err });
            const errorMessage =
                err.response?.data?.error ||
                "Ocurrió un error al renombrar el elemento";
            alert(errorMessage);
            renombrandoItem.value = false;
        });
}
</script>
