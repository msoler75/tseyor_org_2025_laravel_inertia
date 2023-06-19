<template>
    <div class="container mx-auto px-4 py-8">
        <h1>{{ comunicado.titulo }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            <TimeAgo :date="comunicado.published_at" />
        </p>
        <div class="mb-4">
            <img :src="comunicado.imagen" :alt="comunicado.titulo" class="w-full h-64 object-cover">
        </div>
        <div class="prose" v-html="comunicado.texto"></div>
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    comunicado: {
        type: Object,
        required: true,
    },
});
</script>
