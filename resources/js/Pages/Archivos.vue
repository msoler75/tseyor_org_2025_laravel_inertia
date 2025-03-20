<template>

    <div class="container">
        <AdminLinks soloInforma modelo="nodo" necesita="administrar archivos" />
    </div>

    <h1 class="hidden" >{{ ruta }}</h1>

    <FolderExplorer :items="items" :propietarioRef="propietarioRef" @updated="reloadPage"
        :ruta="ruta" :rutaBase="$page.props.auth.user?.id ? 'archivos_raiz' : 'archivos'" />

</template>

<script setup>
const props = defineProps({
    ruta: {},
    items: Array,
    // puedeEscribir: Boolean,
    propietarioRef: Object
});


function reloadPage() {
    router.reload({
        only: ['items']
    })
}

</script>
