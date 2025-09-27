<template>
    <Page>
        <PageHeader>
            <div class="flex justify-between items-center mb-20">
                <Back>Blog</Back>
                <div class="flex gap-2">
                    <Share />
                    <a class="btn btn-xs btn-error w-fit flex gap-3" :href="route('blog.entrada.pdf', entrada.slug)"
                        target="_blank" title="Descargar PDF">
                        <Icon icon="ph:download-duotone" />PDF
                    </a>
                    <AdminLinks modelo="entrada" necesita="administrar contenidos" :contenido="entrada" />
                </div>
            </div>
        </PageHeader>

        <PageContent class="sm:max-w-[80ch]">
            <div class="py-[10ch] mb-12 relative">

                <div class="prose mx-auto">
                    <h1>{{ entrada.titulo }}</h1>

                    <div class="text-sm mb-2 flex justify-between">
                        <span />
                        <TimeAgo :date="entrada.published_at" :includeTime="false" />
                    </div>
                </div>

                <Content :content="entrada.texto" class="pb-6 mx-auto" format="md" />

            </div>
        </PageContent>

        <PageRelated>
            <CardContent v-if="anterior" :imageLeft="true" :key="anterior.id" :title="'Anterior: ' + anterior.titulo"
                class="rounded-none sm:rounded-lg" :image="anterior.imagen" :href="route('entrada', anterior.slug)"
                :description="anterior.descripcion" :date="anterior.published_at" imageClass="h-80" />
            <CardContent v-if="siguiente" :imageLeft="true" :key="siguiente.id" class="rounded-none sm:rounded-lg"
                :title="'Siguiente: ' + siguiente.titulo" :image="siguiente.imagen"
                :href="route('entrada', siguiente.slug)" :description="siguiente.descripcion"
                :date="siguiente.published_at" imageClass="h-80" />
        </PageRelated>


        <PageFooter>
            <Comentarios :url="route('entrada', entrada.id)" />
        </PageFooter>
    </Page>
</template>

<script setup>
import {saveImagesInfo} from "@/Stores/image";

const props = defineProps({
    entrada: {
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
