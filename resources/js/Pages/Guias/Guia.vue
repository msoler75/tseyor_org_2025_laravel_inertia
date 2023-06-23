<template>
    <div class="container mx-auto px-4 py-8">
        <h1>{{ guia.nombre }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            Última actualización: <TimeAgo :date="guia.updated_at" />
        </p>
        <div class="mb-4">
            <img :src="guia.imagen" :alt="guia.nombre" class="w-full h-64 object-cover">
        </div>
        <div v-if="guia.descripcion" class="prose">{{ guia.descripcion }}</div>
        <div v-if="guia.texto" class="prose" v-html="guia.texto"></div>
        <div v-if="guia.experiencia" class="prose" v-html="guia.experiencia"></div>
        <div v-if="guia.citas" class="prose" v-html="guia.citas"></div>
        <div v-if="guia.libros" class="prose" v-html="guia.libros"></div>
        <!-- ... relacionados -->
    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    guia: {
        type: Object,
        required: true,
    },
});
</script>
