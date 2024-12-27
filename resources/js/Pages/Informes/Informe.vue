<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back class="hover:underline" :href="route('equipo.informes', equipo.slug)">Informes del equipo</Back>
            <Link v-if="equipo && equipo.slug && equipo.nombre" :href="route('equipo', equipo.slug)"
                class="flex h-fit gap-2 text-sm items-center hover:underline">
            {{ equipo.nombre }}
            <Icon icon="ph:arrow-right" />
            </Link>
            <span v-else></span>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="informe" necesita="administrar equipos" :contenido="informe" :es-autor="soyCoordinador"/>
            </div>
        </div>


        <div class="py-[5ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto mb-12">

                <div class="flex justify-end w-full mb-10"></div>

                <h1>{{ informe.titulo }}</h1>

                <div class="text-neutral text-sm mb-12 flex justify-between gap-3">
                    <span class="badge uppercase text-xs badge-primary">{{ informe.categoria }}</span>
                    <TimeAgo :date="informe.updated_at" :includeTime="false" class="whitespace-nowrap"/>
                </div>

                <Audios class="mb-20" :audios="parseFiles(informe.audios)" :numerados="false" />

            </div>

            <Content v-if="informe.texto" :content="informe.texto" class="mb-12 mx-auto" />

            <div v-if="informe.archivos" class="max-w-[65ch] mx-auto">
                <div class="font-bold text-neutral text-sm mb-4">Archivos adjuntos:</div>
                <div class="flex flex-wrap gap-4">
                    <a download v-for="archivo, index of parseFiles(informe.archivos)" :key="index" class="bg-neutral text-xs flex p-1 rounded btn-primary
                        max-w-full gap-2 flex-nowrap" title="archivo.title" :href="archivo.src">
                        <Icon icon="ph:download-duotone" />
                        <div class="break-all">
                            {{ archivo.filename }}
                        </div>
                    </a>
                </div>
            </div>

        </div>

        <Comentarios :url="route('informe', informe.id)" />

    </Page>
</template>

<script setup>
import { parseFiles } from '@/composables/parseFiles'


const props = defineProps({
    informe: {
        type: Object,
        required: true,
    },
    equipo: {
        type: Object,
        required: true
    },
    soyCoordinador: Boolean
});



</script>
