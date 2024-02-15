<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Tutoriales</Back>
            <AdminPanel modelo="tutorial" necesita="administrar contenidos" :contenido="tutorial" />
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0">

            <div class="prose mx-auto">
                <h1>{{ tutorial.titulo }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <Audios :audios="parseFiles(tutorial.audios)" :numerados="true" :titulo="tutorial.titulo"/>
                    <TimeAgo :date="tutorial.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="tutorial.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('tutorial', tutorial.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { parseFiles } from '@/composables/parseFiles'

defineOptions({ layout: AppLayout })

const props = defineProps({
    tutorial: {
        type: Object,
        required: true,
    }
});

</script>
