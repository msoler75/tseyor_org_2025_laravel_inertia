<template>
    <div class="py-8">

        <div class="container mx-auto flex justify-between items-center mb-20">
            <Back>Publicaciones</Back>
            <AdminLinks modelo="publicacion" necesita="administrar contenidos" :contenido="publicacion" />
        </div>


        <div class="py-[10ch] bg-base-100 max-w-[80ch] mx-auto shadow-xl mb-12 px-7 md:px-0 animate-fade-in">

            <div class="prose mx-auto">
                <h1>{{ publicacion.titulo }}</h1>

                <div class="text-neutral text-sm mb-2 flex justify-between">
                    <div />
                    <TimeAgo :date="publicacion.updated_at" :includeTime="false" />
                </div>
            </div>

            <Content :content="publicacion.texto" class="mx-auto" />

        </div>

        <Comentarios :url="route('publicacion', publicacion.id)" />

    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
// import usePlayer from '@/Stores/player'

// const player = usePlayer()

defineOptions({ layout: AppLayout })

const props = defineProps({
    publicacion: {
        type: Object,
        required: true,
    }
});


</script>
