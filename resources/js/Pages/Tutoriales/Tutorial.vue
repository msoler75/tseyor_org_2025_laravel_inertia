<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back>Tutoriales</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="tutorial" necesita="administrar contenidos" :contenido="tutorial" />
            </div>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ tutorial.titulo }}</h1>

                <div class="text-sm mb-20 flex justify-between">
                    <TimeAgo :date="tutorial.updated_at" :includeTime="false" />
                </div>
            </div>

            <div v-if="tutorial.video" class="flex justify-center aspect-w-16 aspect-h-9 mb-12">
                <iframe :src="getEmbedYoutube(tutorial.video)" frameborder="0" allowfullscreen></iframe>
            </div>

            <Content :content="tutorial.texto" format="md" class="mx-auto" />

        </div>

        <Comentarios :url="route('tutorial', tutorial.id)" />

    </Page>
</template>

<script setup>
import {getEmbedYoutube} from '@/composables/srcutils.js'


const props = defineProps({
    tutorial: {
        type: Object,
        required: true,
    }
});

</script>
