<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Meditaciones</Back>
            <AdminPanel modelo="meditacion" necesita="administrar contenidos" :contenido="meditacion" />
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ meditacion.titulo }}</h1>

                <div class="text-neutral text-sm mb-20 flex justify-between">
                    <Audios :audios="parseFiles(meditacion.audios)" :numerados="true" :titulo="meditacion.titulo"/>
                    <TimeAgo :date="meditacion.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="meditacion.texto" class="mx-auto" />

        </div>

        <Comentarios :url="route('meditacion', meditacion.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { parseFiles } from '@/composables/parseFiles'

defineOptions({ layout: AppLayout })

const props = defineProps({
    meditacion: {
        type: Object,
        required: true,
    }
});

</script>
