<template>
    <div class="container mx-auto py-12">

        <div class="flex justify-between items-center mb-20">
            <Back>Noticias</Back>
            <AdminPanel modelo="noticia" necesita="administrar contenidos" :contenido="noticia"/>
        </div>

        <div class="container mx-auto px-4 py-8">
            <h1>{{ noticia.titulo }}</h1>
            <p class="text-neutral text-sm mb-2">
                <TimeAgo :date="noticia.published_at" />
            </p>
            <div class="mb-4">
                <Image :src="noticia.imagen" :alt="noticia.titulo" class="w-full h-64 object-cover" />
            </div>

            <Prose :content="noticia.texto"/>

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
