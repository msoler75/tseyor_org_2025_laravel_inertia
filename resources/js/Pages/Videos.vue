<template>
    <div class="container py-12 mx-auto">

        <AdminPanel modelo="video" necesita="administrar contenidos" class="mb-3" />


        <section class="py-10 space-y-10 container mx-auto">
            <h1 class="text-4xl font-bold text-center mb-10">Vídeos TSEYOR</h1>
            <div class="text-lg text-center">Visita nuestro canal de Youtube @tseyor</div>
            <div class="flex flex-wrap justify-center items-center gap-10 card flex-row bg-base-100 shadow rounded-lg p-12 w-fit mx-auto">
                <a href="https://www.youtube.com/@tseyor" target="_blank"
                    class="text-blue-600 hover:text-blue-800 font-bold text-2xl">youtube.com/@tseyor</a>
                    <a href="https://www.youtube.com/@tseyor" target="_blank">
                        <Image src="/almacen/medios/logos/youtube_big_logo.svg" alt="Logo de Youtube"
                            class="h-12 mx-auto dark:mix-blend-exclusion" />
                    </a>
            </div>

            <div class="flex justify-end mb-5">
                <SearchInput />
            </div>

            <GridAppear class="mt-2 gap-4">
                <div v-for="contenido in listado.data" :key="contenido.id" :href="route('normativa', contenido.slug)"
                    class="card hover:text-primary transition-color duration-200 px-5 py-2 h-full flex flex-col items-center gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe :src="videoUrl(contenido.enlace)" frameborder="0" allowfullscreen></iframe>
                    </div>
                    <div class="text-xl font-bold">{{ contenido.titulo }}</div>
                    <div class="text-sm">{{ contenido.descripcion }}</div>
                </div>
            </GridAppear>

            <pagination class="mt-6" :links="listado.links" />
        </section>
    </div>
</template>


<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

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


function videoUrl(url) {
    const u = url.match(/(?:https:\/\/)?(?:www.)?youtube.com\/watch\?v=([^&]+)/);
    if (u && u[1]) {
        const videoId = u[1];
        return 'https://www.youtube.com/embed/' + videoId
    }
    else {
        return url
    }
}
</script>
