<template>

    <div class="container mb-3">
        <AdminLinks modelo="nodo" necesita="administrar archivos"  />
    </div>

    <h1 class="hidden" >{{ ruta }}</h1>

    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietarioRef="propietarioRef" @updated="reloadPage"
        ruta="" rutaBase="" rootLabel="mis_archivos:" rootUrl="" :mostrarRutas="true" />
</template>

<script setup>

const props = defineProps({
    ruta: {},
    items: Array,
    puedeEscribir: Boolean,
    propietarioRef: Object
});


function reloadPage() {
    router.reload({
        only: ['items']
    })
}

</script>
