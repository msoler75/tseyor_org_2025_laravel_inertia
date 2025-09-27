<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back>Comunicados</Back>
                <div class="flex gap-2">
                    <Share />
                    <a class="btn btn-xs btn-error text-white w-fit flex gap-3"
                        :href="route('comunicado.pdf', comunicado.slug)" target="_blank" title="Descargar PDF">
                        <Icon icon="ph:download-duotone" />PDF
                    </a>
                    <AdminLinks modelo="comunicado" necesita="administrar contenidos" :contenido="comunicado" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

                <div class="prose mx-auto">

                    <h1>{{ comunicado.titulo }}</h1>

                    <div class="text-sm mb-20 flex justify-between">
                        <Audios :audios="parseFiles(comunicado.audios)" :numerados="true" :titulo="comunicado.titulo" />

                        <!-- <a target="_blank" class="btn btn-xs btn-error w-fit flex gap-3" :href="comunicado.pdf"
                            title="Ver en formato PDF">
                            <Icon icon="ph:file-pdf" />
                        </a> -->
                        <TimeAgo :date="comunicado.fecha_comunicado" :includeTime="false" />
                    </div>

                </div>

                <LazyHydrate>
                    <Content :content="comunicado.texto" class="mx-auto" />
                </LazyHydrate>

            </div>

        </PageContent>

        <PageRelated>
            <CardContent v-if="anterior" :imageLeft="true" :key="anterior.id" :title="'Anterior: ' + anterior.titulo"
                class="rounded-none sm:rounded-lg" :image="anterior.imagen" :href="route('comunicado', anterior.slug)"
                :description="anterior.descripcion" :date="anterior.published_at" imageClass="h-80" />
            <CardContent v-if="siguiente" :imageLeft="true" :key="siguiente.id" class="rounded-none sm:rounded-lg"
                :title="'Siguiente: ' + siguiente.titulo" :image="siguiente.imagen"
                :href="route('comunicado', siguiente.slug)" :description="siguiente.descripcion"
                :date="siguiente.published_at" imageClass="h-80" />
        </PageRelated>

        <PageFooter>
            <Comentarios :url="route('comunicado', comunicado.id)" />
        </PageFooter>

    </Page>
</template>

<script setup>
import {saveImagesInfo} from "@/Stores/image";
import { parseFiles } from '@/composables/parseFiles'
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js'

const { trackDirectAccess } = useGoogleAnalytics()

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

onMounted(() => {
    // Tracking de acceso directo/externo para comunicados
    trackDirectAccess('comunicado', props.comunicado.titulo)
})
</script>
