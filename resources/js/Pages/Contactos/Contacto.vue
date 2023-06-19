<template>
    <div class="container mx-auto px-4 py-8">
        <h1>{{ contacto.nombre }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            Última actualización: <TimeAgo :date="contacto.updated_at" />
        </p>
        <div class="mb-4">
            <img :src="contacto.imagen" :alt="contacto.nombre" class="w-full h-64 object-cover">
        </div>
        <p>{{ contacto.pais }}</p>
        <p>{{ contacto.poblacion }}</p>
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    contacto: {
        type: Object,
        required: true,
    },
});
</script>
