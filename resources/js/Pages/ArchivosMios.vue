<template>
    <AdminLinks modelo="nodo" necesita="administrar archivos" class="mb-3" />

    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietarioRef="propietarioRef" @updated="reloadPage"
        ruta="" rutaBase=""
        rootLabel="mis_archivos:"
        rootUrl=""
        :mostrarRutas="true"
        />
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
