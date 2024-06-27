<template>
    <div class="container py-12 mx-auto">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Comunicados</Back>
            <AdminPanel modelo="comunicado" necesita="administrar contenidos" :contenido="comunicado" />
            <a class="btn btn-xs btn-error text-white w-fit flex gap-3" :href="route('comunicado.pdf', comunicado.slug)"
                target="_blank" title="Descargar PDF">
                <Icon icon="ph:download-duotone" />PDF
            </a>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ comunicado.titulo }}</h1>

                <div class="text-neutral text-sm mb-20 flex justify-between">
                    <Audios :audios="parseFiles(comunicado.audios)" :numerados="true" :titulo="comunicado.titulo" />

                    <!-- <a target="_blank" class="btn btn-xs btn-error w-fit flex gap-3" :href="comunicado.pdf"
                        title="Ver en formato PDF">
                        <Icon icon="ph:file-pdf" />
                    </a> -->
                    <TimeAgo :date="comunicado.fecha_comunicado" :includeTime="false" />
                </div>

            </div>

            <Content :content="comunicado.texto" class="mx-auto" />

        </div>


        <div class="mt-12 grid gap-8 mb-12 grid-cols-1 lg:grid-cols-2">
            <CardContent v-if="anterior" :imageLeft="true" :key="anterior.id" :title="'Anterior: ' + anterior.titulo"
                :image="anterior.imagen" :href="route('comunicado', anterior.slug)" :description="anterior.descripcion"
                :date="anterior.published_at" imageClass="h-80" />
            <CardContent v-if="siguiente" :imageLeft="true" :key="siguiente.id" :title="'Siguiente: ' + siguiente.titulo"
                :image="siguiente.imagen" :href="route('comunicado', siguiente.slug)" :description="siguiente.descripcion"
                :date="siguiente.published_at" imageClass="h-80" />
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
    },
    siguiente: {
        type: Object,
        required: true,
    },
    anterior: {
        type: Object,
        required: true,
    },
});

</script>
