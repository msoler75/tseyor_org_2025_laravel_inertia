<template>

    <button v-if="store.isMovingFiles || store.isCopyingFiles" class="btn btn-secondary flex gap-x items-center"
        @click.prevent="cancelarOperacion">
        <Icon icon="material-symbols:close-rounded" />
        <span>Cancelar</span>
    </button>

    <button v-if="store.isMovingFiles" class="btn btn-secondary flex gap-x items-center"
        :disabled="store.sourcePath == store.rutaActual || !store.puedeEscribir" @click.prevent="moverItems"
        title="Mover los elementos seleccionados a esta carpeta">
        <Icon icon="ph:clipboard-duotone" />
        <span v-if="store.puedeEscribir">Mover aquí</span>
        <span v-else>No tienes permisos aquí</span>
    </button>

    <button v-else-if="store.isCopyingFiles" class="btn btn-secondary flex gap-x items-center"
        :disabled="store.sourcePath == store.rutaActual || !store.puedeEscribir" @click.prevent="copiarItems"
        title="Copiar los elementos seleccionados a esta carpeta">
        <Icon icon="ph:clipboard-duotone" />
        <span v-if="store.puedeEscribir">Pegar aquí</span>
        <span v-else>No tienes permisos aquí</span>
    </button>

    <template v-else>

        <button v-if="store.itemsSeleccionados.length && store.puedeMoverSeleccionados"
            class="btn btn-secondary flex gap-x items-center" @click.prevent="prepararMoverItems">
            <Icon icon="ph:scissors-duotone" /><span>Mover</span>
        </button>

        <button v-if="store.itemsSeleccionados.length" class="btn btn-secondary flex gap-x items-center"
            @click.prevent="prepararCopiarItems">
            <Icon icon="ph:copy-simple-duotone" /><span>Copiar</span>
        </button>

        <button v-if="store.itemsSeleccionados.length && store.puedeBorrarSeleccionados"
            class="btn btn-secondary flex gap-x items-center" @click.prevent="store.call('eliminar', null)">
            <Icon icon="ph:trash-duotone" />
            <span>Eliminar</span>
        </button>

        <button v-if="store.itemsSeleccionados.length == 1 && store.puedeBorrarSeleccionados"
            class="md:hidden btn btn-secondary flex gap-x items-center"
            @click.prevent="store.call('renombrar', store.itemsSeleccionados[0])">
            <Icon icon="ph:cursor-text-duotone" />
            <span>Renombrar</span>
        </button>

        <button v-if="store.seleccionando && store.itemsSeleccionados.length > 0" class="btn btn-secondary"
            @click.prevent="store.call('propiedades')">
            <Icon icon="ph:info-duotone" />
            <span>Propiedades</span>
        </button>

    </template>
</template>





<script setup>
import useFolderExplorerStore from '@/Stores/folderExplorer';
const store = useFolderExplorerStore()


// COPIAR Y MOVER ITEMS

function prepararMoverItems() {
    console.log('prepararMoverItems')
    store.seleccionando = false
    store.isMovingFiles = true
    store.sourcePath = store.rutaActual
    store.filesToMove = [...store.itemsSeleccionados.map(item => item.nombre)]
}

function prepararCopiarItems() {
    console.log('prepararCopiarItems')
    store.seleccionando = false
    store.isCopyingFiles = true
    store.sourcePath = store.rutaActual
    store.filesToCopy = [...store.itemsSeleccionados.map(item => item.nombre)]
}

function copiarItems() {
    axios.post('/files/copy', {
        sourceFolder: store.sourcePath,
        targetFolder: store.rutaActual,
        items: store.filesToCopy
    }).then(response => {
        console.log({ response })
        store.actualizar()
    })
        .catch(err => {
            console.warn({err})
            const errorMessage = err.response.data?.error || 'Ocurrió un error al copiar los elementos'
            alert(errorMessage)
        })

    cancelarOperacion()
}

function moverItems() {
    axios.post('/files/move', {
        sourceFolder: store.sourcePath,
        targetFolder: store.rutaActual,
        items: store.filesToMove
    }).then(response => {
        console.log({ response })
        store.actualizar()
    })
        .catch(err => {
            console.warn({err})
            const errorMessage = err.response.data?.error || 'Ocurrió un error al mover los elementos'
            alert(errorMessage)
        })

    cancelarOperacion()
}

function cancelarOperacion() {
    console.log('cancelarOperacion')
    store.isMovingFiles = false
    store.isCopyingFiles = false
    store.filesToMove = []
    store.filesToCopy = []
    store.itemsShow.forEach(item => { item.seleccionado = false })
}


</script>
