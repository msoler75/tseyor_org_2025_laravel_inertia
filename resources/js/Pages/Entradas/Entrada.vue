<template>
    <div class="container py-12 mx-auto">
        <div class="flex justify-between items-center mb-20">
            <Back>Entradas</Back>
            <AdminPanel modelo="entrada" necesita="administrar contenidos" :contenido="entrada"/>
        </div>

        <h1>{{ entrada.titulo }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            <TimeAgo :date="entrada.published_at" />
        </p>
        <div class="mb-4">
            <Image :src="entrada.imagen" :alt="entrada.titulo" class="w-full h-64 object-cover"/>
        </div>
        <div class="prose" v-html="entrada.texto"></div>
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
