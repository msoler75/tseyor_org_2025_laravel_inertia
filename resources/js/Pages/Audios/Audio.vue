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

            <div v-if="audio.audio" class="btn p-0 w-12 h-5 min-h-auto text-3xl"
                :class="player.music?.src == audio.src ? 'btn-secondary' : 'btn-primary'" @click="clickPlayPause(audio)"
                :title="audio.src">
                <AudioStateIcon :src="audio.src" />
            </div>
            <a target="_blank" v-else :href="audio.enlace" class="btn p-0 w-12 h-5 min-h-auto text-3xl"
                title="abrir enlace">
                <Icon icon="ph:arrow-up-right-duotone" />
            </a>


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
