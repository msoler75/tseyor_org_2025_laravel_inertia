<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Comunicados</Back>
            <AdminPanel modelo="comunicado" necesita="administrar contenidos" :contenido="comunicado" />
        </div>


        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ comunicado.titulo }}</h1>

                <div class="text-neutral text-sm mb-20 flex justify-between">
                    <Audios :audios="parseFiles(comunicado.audios)" :numerados="true" :titulo="comunicado.titulo"/>
                    <TimeAgo :date="comunicado.fecha_comunicado" :includeTime="false" />
                    <Link :href="route('comunicado.pdf', comunicado.slug)">PDF</Link>
                </div>

            </div>

            <Content :content="comunicado.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('comunicado', comunicado.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { parseFiles } from '@/composables/parseFiles'

defineOptions({ layout: AppLayout })

const props = defineProps({
    comunicado: {
        type: Object,
        required: true,
    }
});

</script>
