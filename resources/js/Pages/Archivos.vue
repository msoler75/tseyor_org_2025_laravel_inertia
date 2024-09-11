<template>
    <h1 class="hidden" >{{ ruta }}</h1>
    <AdminLinks soloInforma modelo="nodo" necesita="administrar archivos" class="mt-5 container mb-3" />
    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietarioRef="propietarioRef" @updated="reloadPage"
        :ruta="ruta" :rutaBase="$page.props.auth.user?.id ? 'archivos_raiz' : 'archivos'" />
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
