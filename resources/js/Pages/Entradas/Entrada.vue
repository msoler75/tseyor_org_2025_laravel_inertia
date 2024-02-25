<template>
    <div class="container py-12 mx-auto">
        <div class="flex justify-between items-center mb-20">
            <Back>Entradas</Back>
            <AdminPanel modelo="entrada" necesita="administrar contenidos" :contenido="entrada" />
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ entrada.titulo }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <TimeAgo :date="entrada.fecha_entrada" :includeTime="false" />
                </div>
            </div>

            <Content :content="entrada.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('entrada', entrada.id)" />
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    entrada: {
        type: Object,
        required: true,
    },
});
</script>
