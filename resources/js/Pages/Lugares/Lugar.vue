<template>
    <div class="container mx-auto px-4 py-8">
        <h1>{{ lugar.nombre }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            Última actualización: <TimeAgo :date="lugar.updated_at" />
        </p>
        <div class="mb-4">
            <img :src="lugar.imagen" :alt="lugar.nombre" class="w-full h-64 object-cover">
        </div>
        <div class="prose" v-html="lugar.texto"></div>
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    lugar: {
        type: Object,
        required: true,
    },
});
</script>
