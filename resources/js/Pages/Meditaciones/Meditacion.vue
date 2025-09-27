<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back>Meditaciones</Back>
                <div class="flex gap-2">
                    <Share />
                    <AdminLinks modelo="meditacion" necesita="administrar contenidos" :contenido="meditacion" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>


            <div class="prose mx-auto">
                <h1>{{ meditacion.titulo }}</h1>

                <div class="text-sm mb-20 flex justify-between">
                    <Audios :audios="parseFiles(meditacion.audios)" :numerados="true" :titulo="meditacion.titulo" />
                    <TimeAgo :date="meditacion.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="meditacion.texto" class="mx-auto" />

     </div>

        </PageContent>

        <Comentarios :url="route('meditacion', meditacion.id)" />

    </Page>
</template>

<script setup>
import { parseFiles } from '@/composables/parseFiles'


const props = defineProps({
    meditacion: {
        type: Object,
        required: true,
    }
});

</script>
