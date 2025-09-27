<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back>Noticias</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="noticia" necesita="administrar contenidos" :contenido="noticia" />
            </div>
        </div>
        </PageHeader>

       <PageContent class="sm:max-w-[80ch]">

            <div class="py-[10ch] mb-12 relative">

                <FontSizeControls class="hidden lg:flex absolute right-4 top-4"/>

                <div class="prose mx-auto">
                <h1>{{ noticia.titulo }}</h1>
                <p class="text-sm mb-2">
                    <TimeAgo :date="noticia.published_at" />
                </p>
            </div>

            <Content :content="noticia.texto" class="mx-auto" />

        </div>
         </PageContent>

        <PageFooter>
        <Comentarios :url="route('noticia', noticia.id)" />

    </PageFooter>
</Page>
</template>

<script setup>
import PageHeader from '@/Components/PageHeader.vue';




const props = defineProps({
    noticia: {
        type: Object,
        required: true,
    },
});
</script>
