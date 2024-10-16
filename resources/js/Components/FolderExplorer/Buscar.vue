<template>

<div v-if="buscando || store.mostrandoResultadosBusqueda" class="w-full text-center h-[3rem] mt-3">
                <div v-if="buscando">Buscando...</div>
                <div v-else-if="store.mostrandoResultadosBusqueda && !store.resultadosBusqueda.length">No hay resultados</div>
            </div>

    <!-- Modal Search -->
    <Modal :show="modalBuscar" @close="modalBuscar = false" maxWidth="sm">

        <form class="p-5 flex flex-col gap-5 items-center" @submit.prevent="onSearch">
            <input ref="inputSearch" type="search" placeholder="Nombre de archivo..." v-model="store.textoBuscar">

            <div class="py-3 flex justify-between sm:justify-end gap-5">
                <button @click.prevent="onSearch" type="button" class="btn btn-primary btn-sm" :disabled="!store.textoBuscar">
                    Buscar archivos
                </button>

                <button @click.prevent="modalBuscar = false" type="button" class="btn btn-neutral btn-sm">
                    Cancelar
                </button>
            </div>
        </form>

    </Modal>

</template>





<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
const store = useFolderExplorerStore()

store.on('buscar', abrirModalBuscar)
store.resultadosBusqueda = []

// BUSCAR ARCHIVOS

const modalBuscar = ref(false)
const inputSearch = ref(null)
const buscando = ref(false)
const id_busqueda = ref(null)

function abrirModalBuscar() {
    modalBuscar.value = true
    store.textoBuscar = ""
    nextTick(() => {
        inputSearch.value.focus()
    })
}


function onSearch() {
    if (!store.textoBuscar) {
        // cerramos el modal
        // modalBuscar.value = false
        return
    }
    modalBuscar.value = false
    buscando.value = true
    store.mostrandoResultadosBusqueda = true
    store.resultadosBusqueda = []

    axios('/archivos_buscar', {
        params: {
            ruta: store.rutaActual,
            nombre: store.textoBuscar
        }
    })
        .then(response => {
            const data = response.data
            console.log({ data })
            id_busqueda.value = data.id_busqueda
            store.resultadosBusqueda = data.resultados
            buscarMasResultados()
        })
}

function buscarMasResultados() {
    if (!store.mostrandoResultadosBusqueda) return
    axios('/archivos_buscar?id_busqueda=' + id_busqueda.value)
        .then(response => {
            const data = response.data
            console.log({ data })
            for (const resultado of data.resultados) {
                // agregamos el resultado si acaso no estaba ya
                if (!store.resultadosBusqueda.find(item => item.ruta == resultado.ruta))
                    store.resultadosBusqueda.push(resultado)
            }
            if (data.finalizado)
                buscando.value = false // fin de la busqueda
            else
                buscarMasResultados()
        })
}


</script>
