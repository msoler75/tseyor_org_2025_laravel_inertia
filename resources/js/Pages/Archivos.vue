<template>
    <AdminLinks soloInforma modelo="nodo" necesita="administrar archivos" class="mt-5 container mb-3" />
    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietarioRef="propietarioRef" @updated="reloadPage"
        :ruta="ruta" :rutaBase="$page.props.auth.user?.id?'archivos_raiz':ruta" />
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

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
