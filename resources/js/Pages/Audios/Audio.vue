<template>
    <div class="container mx-auto px-4 py-8 mb-32">
        <div class="flex justify-between items-center mb-20">
            <Back>Audios</Back>
            <AdminPanel modelo="audio" necesita="administrar contenidos" :contenido="audio" />
        </div>

        <div class="flex flex-col items-center">
            <h1>{{ audio.titulo }}</h1>
            <p class="text-gray-600 text-sm mb-2">
                Última actualización:
                <TimeAgo :date="audio.updated_at" />
            </p>
            <p>
                {{ audio.descripcion }}
            </p>

            <Audios :audios="parseFiles(audio.audio)" :titulo="audio.titulo" />
        </div>
    </div>
</template>

<script setup>

import { parseFiles } from '@/composables/parseFiles'

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    audio: {
        type: Object,
        required: true,
    },
});
</script>
