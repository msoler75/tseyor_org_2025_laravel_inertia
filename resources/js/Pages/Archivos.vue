<template>
    <FolderExplorer :items="items" :puedeEscribir="puedeEscribir" :propietario="propietario" @updated="reloadPage"/>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    items: Array,
    puedeEscribir: Boolean,
    propietario: Object
});

function reloadPage() {
    router.reload({
        only: ['items']
    })
}

</script>
