<template>
    <div class="container py-12 mx-auto">

        <AdminLinks modelo="video" necesita="administrar contenidos" class="mb-3" />


        <section class="py-10 space-y-10 mx-auto">
            <h1 class="text-4xl font-bold text-center mb-10">Vídeos TSEYOR</h1>
            <div class="text-lg text-center">Visita nuestro canal de Youtube @tseyor</div>
            <div
                class="flex flex-wrap justify-center items-center gap-10 card flex-row bg-base-100 shadow rounded-lg p-12 w-fit mx-auto">
                <a href="https://www.youtube.com/@tseyor" target="_blank"
                    class="text-blue-600 hover:text-blue-800 font-bold text-2xl">youtube.com/@tseyor</a>
                <a href="https://www.youtube.com/@tseyor" target="_blank">
                    <Image src="/almacen/medios/logos/youtube_big_logo.svg" alt="Logo de Youtube"
                        class="h-12 mx-auto dark:mix-blend-exclusion" />
                </a>
            </div>

            <ContentMain class="flex justify-end mb-5">
                <SearchInput />
            </ContentMain>

            <GridAppear class="mt-2 grid-cols-1 lg:grid-cols-2 gap-7">
                <div v-for="video in listado.data" :key="video.id"
                    class="card px-5 py-2 h-full flex flex-col items-center gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <div class="relative h-0 overflow-hidden max-w-full w-full" style="padding-bottom: 56.25%">
                        <iframe :src="getEmbedYoutube(video.enlace)" frameborder="0" allowfullscreen
                            class="absolute top-0 left-0 w-full h-full bg-gray-800"></iframe>
                    </div>
                    <Link :href="route('video', video.slug)"
                        class="hover:text-primary transition-color duration-200  text-xl font-bold" v-html="video.titulo">
                    </Link>
                    <div class="text-gradient opacity-75 transition duration-300 group-hover:opacity-90 text-sm text-ellipsis overflow-hidden "
                        v-html="video.descripcion" />
                </div>
            </GridAppear>

            <pagination class="mt-6" :links="listado.links" />
        </section>
    </div>
</template>


<script setup>
import { getEmbedYoutube } from '@/composables/srcutils.js'


const props = defineProps({
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    /* categoriaActiva: { default: () => '' },
    categorias: {
        default: () => []
    }*/
});



</script>
