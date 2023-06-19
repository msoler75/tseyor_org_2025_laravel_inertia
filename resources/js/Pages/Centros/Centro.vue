<template>
    <div class="container mx-auto px-4 py-8">
        <h1>{{ centro.nombre }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            Última actualización: <TimeAgo :date="centro.updated_at" />
        </p>
        <div class="mb-4">
            <img :src="centro.imagen" :alt="centro.nombre" class="w-full h-64 object-cover">
        </div>
        <p>{{ centro.pais }}</p>
        <p>{{ centro.poblacion }}</p>
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    centro: {
        type: Object,
        required: true,
    },
});
</script>
