<template>
    <AdminPanel modelo="nodo" necesita="administrar archivos" class="mb-3"/>
    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietarioRef="propietarioRef" @updated="reloadPage"/>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
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
