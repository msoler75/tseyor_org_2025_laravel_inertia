<template>
    <div class="container mx-auto py-12">
        <div class="container mx-auto flex justify-between items-center mb-20">
            <span></span>
            <AdminPanel modelo="pagina" necesita="administrar contenidos" :contenido="pagina" />
        </div>
    <h1 class="text-center capitalize">{{ pagina.titulo }}</h1>
    <Content :content="pagina.texto" class="pb-12" />
</div>
</template>


<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })


const props = defineProps({
    pagina: {
        type: Object,
        required: true,
    },
})

</script>
