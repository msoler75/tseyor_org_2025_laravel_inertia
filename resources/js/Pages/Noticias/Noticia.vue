<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back>Noticias</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="noticia" necesita="administrar contenidos" :contenido="noticia" />
            </div>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ noticia.titulo }}</h1>
                <p class="text-sm mb-2">
                    <TimeAgo :date="noticia.published_at" />
                </p>
            </div>

            <Content :content="noticia.texto" class="mx-auto" />

        </div>

        <Comentarios :url="route('noticia', noticia.id)" />

    </Page>
</template>

<script setup>



const props = defineProps({
    noticia: {
        type: Object,
        required: true,
    },
});
</script>
