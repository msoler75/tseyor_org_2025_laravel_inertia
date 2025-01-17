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
    console.log("abrirModalRenombrar()", item);
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

async function renombrarItem() {
    const { nombre, nombre_original, url, carpeta } = itemRenombrando.value;

    itemRenombrando.value.seleccionado = false;
    renombrandoItem.value = true;

    const nombreActual = nombre_original || nombre;

    try {
        await axios.post("/files/rename", {
            folder: carpeta,
            oldName: nombreActual,
            newName: nuevoNombre.value,
        });

        modalRenombrarItem.value = false;

        const nuevaRuta = `${carpeta}/${nuevoNombre.value}`;
        const nuevaUrl = url.replace(
            new RegExp(`/${nombreActual}(?=/|$)`),
            `/${nuevoNombre.value}`
        );

        Object.assign(itemRenombrando.value, {
            ruta: nuevaRuta,
            url: nuevaUrl,
            [nombre_original ? "nombre_original" : "nombre"]: nuevoNombre.value,
        });

        if (!store.embed && itemRenombrando.value.actual) {
            router.replace(nuevaUrl);
            document.title = nuevaRuta;
        }
    } catch (err) {
        alert(
            err.response?.data?.error ||
                "Ocurrió un error al renombrar el elemento"
        );
    } finally {
        renombrandoItem.value = false;
    }
}
</script>
