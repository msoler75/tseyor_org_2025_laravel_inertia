<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back :href="route('equipo', $page.props.equipo_interiorizacion_id)">Interiorización</Back>
                <div class="flex gap-2">
                    <Share />
                    <a class="btn btn-xs btn-error text-white w-fit flex gap-3"
                        :href="route('comunicado-interiorizacion.pdf', comunicado.slug)" target="_blank" title="Descargar PDF">
                        <Icon icon="ph:download-duotone" />PDF
                    </a>
                    <AdminLinks modelo="comunicado-interiorizacion" necesita="administrar contenidos" :contenido="comunicado" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">
                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

                <div class="prose mx-auto">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="badge badge-primary">{{ comunicado.nivel == 1 ? 'Nivel 1' : 'Nivel 2' }}</span>
                        <span class="badge badge-ghost">{{ comunicado.ciclo }}</span>
                        <span v-if="comunicado.numero" class="badge badge-outline">#{{ comunicado.numero }}</span>
                    </div>

                    <h1>{{ comunicado.titulo }}</h1>

                    <div class="text-sm mb-20 flex justify-between">
                        <Audios :audios="parseFiles(comunicado.audios)" :numerados="true" :titulo="comunicado.titulo" />
                        <TimeAgo :date="comunicado.fecha_comunicado" :includeTime="false" />
                    </div>
                </div>

                <Content :content="comunicado.texto" class="mx-auto" />

            </div>
        </PageContent>

        <PageRelated>
            <CardContent v-if="anterior" :imageLeft="true" :key="anterior.id" :title="'Anterior: ' + anterior.titulo"
                class="rounded-none sm:rounded-lg" :image="anterior.imagen" :href="route('comunicado-interiorizacion', anterior.slug)"
                :description="anterior.descripcion" :date="anterior.published_at" imageClass="h-80" />
            <CardContent v-if="siguiente" :imageLeft="true" :key="siguiente.id" class="rounded-none sm:rounded-lg"
                :title="'Siguiente: ' + siguiente.titulo" :image="siguiente.imagen"
                :href="route('comunicado-interiorizacion', siguiente.slug)" :description="siguiente.descripcion"
                :date="siguiente.published_at" imageClass="h-80" />
        </PageRelated>

        <PageFooter>
            <Comentarios :url="route('comunicado-interiorizacion', comunicado.id)" />
        </PageFooter>

    </Page>
</template>

<script setup>
import {saveImagesInfo} from "@/composables/image";
import { parseFiles } from '@/composables/parseFiles'

const props = defineProps({
    comunicado: {
        type: Object,
        required: true,
    },
    siguiente: {
        type: [Object, null],
        required: true,
    },
    anterior: {
        type: [Object, null],
        required: true,
    },
    imagenesInfo: {
        type: Object,
        required: false,
    }
});

saveImagesInfo(props.imagenesInfo)
</script>
