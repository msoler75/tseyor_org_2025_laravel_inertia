<template>
    <div class="container mx-auto px-4 py-8 mb-32">
        <div class="flex justify-between items-center mb-20">
            <Back>Videos</Back>
            <AdminLinks modelo="video" necesita="administrar contenidos" :contenido="video" />
        </div>

        <div class="max-w-full lg:max-w-[640px] mx-auto">

            <div class="flex flex-col items-center">

                <h1>{{ video.titulo }}</h1>
                <p class="text-gray-600 text-sm mb-2">
                    Última actualización:
                    <TimeAgo :date="video.updated_at" />
                </p>
                <Prose class="my-7">
                    {{ video.descripcion }}
                </Prose>

                <div class="relative h-0 overflow-hidden max-w-full w-full" style="padding-bottom: 56.25%">
                    <iframe :src="getEmbedYoutube(video.enlace)" frameborder="0" allowfullscreen
                        class="absolute top-0 left-0 w-full h-full"></iframe>
                </div>
            </div>

        </div>
    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { getEmbedYoutube } from '@/composables/srcutils.js'

defineOptions({ layout: AppLayout })

const props = defineProps({
    video: {
        type: Object,
        required: true,
    },
})

</script>
