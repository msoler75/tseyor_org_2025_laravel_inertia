<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Noticias</Back>
            <AdminPanel modelo="noticia" necesita="administrar contenidos" :contenido="noticia"/>
        </div>

        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0">

            <div class="prose mx-auto">
                <h1>{{ noticia.titulo }}</h1>
                <p class="text-neutral text-sm mb-2">
                    <TimeAgo :date="noticia.published_at" />
                </p>
            </div>

            <Content :content="noticia.texto" class="pb-12 mx-auto" />

        </div>

        <Comentarios :url="route('noticia', noticia.id)" />

    </div>
</template>

<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    noticia: {
        type: Object,
        required: true,
    },
});
</script>
