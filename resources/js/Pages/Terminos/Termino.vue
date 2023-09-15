<template>
    <div class="container mx-auto px-4 py-8 w-full">

        <div class="flex justify-between items-center mb-20">
            <Back>Glosario</Back>
            <AdminPanel modelo="termino" necesita="administrar contenidos" :contenido="termino"/>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 rounded-xl">

<div class="prose mx-auto">
                <h1 class="text-center lg:text-left capitalize lowercase">{{ termino.nombre }}</h1>
                <p class="text-gray-600 text-xs my-5 text-right">
                    Última actualización:
                    <TimeAgo :date="termino.updated_at" />
                </p>
                <div class="mb-4"></div>
                <Content :content="termino.texto"/>
            </div>

        </div>

        <hr class="my-12" />


    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    termino: {
        type: Object,
        required: true,
    },
});
</script>
