<template>
    <div class="container mx-auto px-4 py-8">
        <h1>{{ entrada.titulo }}</h1>
        <p class="text-gray-600 text-sm mb-2">
            <TimeAgo :date="entrada.published_at" />
        </p>
        <div class="mb-4">
            <img :src="entrada.imagen" :alt="entrada.titulo" class="w-full h-64 object-cover">
        </div>
        <div class="prose" v-html="entrada.texto"></div>
    </div>
</template>

<script setup>
import TimeAgo from '@/Components/TimeAgo.vue';
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    entrada: {
        type: Object,
        required: true,
    },
});
</script>
