<template>
    <div class="container mx-auto py-12 flex flex-col justify-center h-full">
        <div v-if="codigo" class="text-7xl opacity-60 font-bold pt-12 text-center">{{ codigo }}</div>
        <Hero :title="titulo" :subtitle="mensaje">
        </Hero>
    </div>
</template>


<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    codigo: {type: Number, required: false},
    titulo: { type: String, required: true },
    mensaje: {
        type: String,
        required: true,
    },
})

</script>
