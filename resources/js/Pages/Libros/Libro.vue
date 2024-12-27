<template>
    <div class="container mx-auto py-12">

        <div class="flex justify-between items-center mb-20">
            <Back>Libros</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="libro" necesita="administrar contenidos" :contenido="libro" />
            </div>
        </div>

        <div class="max-w-[900px] mx-auto flex flex-col md:flex-row gap-10 mt-12">

            <Transition name="book-drop">
                <div v-show="!cargandoPortada"
                    class="card bg-base-100 shadow-2xl w-fit h-fit flex justify-center mx-auto md:sticky md:top-20 mb-14 md:mb-0">
                    <ImageShadow :src="libro.imagen" width="300" height="450" :alt="libro.titulo"
                        class="object-contain rounded-[2px]"
                        :style="{ 'view-transition-name': `imagen-libro-${libro.id}` }" @loaded="cargandoPortada = false" />
                </div>
            </Transition>
            <span v-show="cargandoPortada"
                class="card bg-base-100 shadow-2xl w-fit h-fit flex justify-center mx-auto md:sticky md:top-20 mb-14 md:mb-0 opacity-0">
                <div class="w-[300px]" />
            </span>


            <div class="p-6 card bg-base-100 shadow animate-fade-in">

                <h1 class="text-2xl font-bold mb-4">{{ libro.titulo }}</h1>
                <p class="text-gray-600 text-sm mb-2 flex justify-between">
                    <Link :href="`${route('libros')}?categoria=${libro.categoria}`"
                        class="no-underline badge badge-primary badge-outline">{{ libro.categoria }}</Link>
                    <TimeAgo :date="libro.published_at" />
                </p>
                <p class="mb-8 text-xs">{{ edicionPaginas }}</p>
                <div class="prose" v-html="libro.descripcion"></div>
                <div class="w-full flex mt-auto justify-end pt-7">

                    <a class="btn btn-error w-fit flex gap-3" :href="getSrcUrl(libro.pdf)" download>
                        <Icon icon="ph:download-duotone" /> Descargar
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-14" />

        <h2 v-if="relacionados?.length" class="text-xl font-bold mt-8">Libros relacionados</h2>
        <GridAppear class="gap-4 mt-4 grid-cols-1 sm:grid-cols-[repeat(auto-fill,minmax(26rem,1fr))]">

            <CardContent v-for="contenido in relacionados" :key="contenido.id" :title="contenido.titulo"
                :image="contenido.imagen" :href="route('libro', contenido.slug)" :description="contenido.descripcion"
                :date="contenido.published_at" :tag="contenido.categoria" image-left image-contained
                class="h-[44vw] xs:h-[200px] lg:h-[300px]"
                imageClass="w-1/3 h-full sm:w-[150px] sm:h-[250px] lg:w-[200px] lg:h-[300px]">
                <template #imagex>
                    <div class="flex  w-full h-full items-center justify-center">
                        <Libro3d :libro="contenido" imageClass="w-[180px]" />
                    </div>
                </template>
            </CardContent>
        </GridAppear>


    </div>
</template>

<script setup>
import { getSrcUrl } from '@/composables/srcutils.js'


const props = defineProps({
    libro: {
        type: Object,
        required: true,
    },
    relacionados: {
        type: Array, required: true
    }
});


const edicionPaginas = computed(() => {
    const str = []

    if (props.libro.edicion)
        str.push(`Edición: ${props.libro.edicion}ª`)

    if (props.libro.paginas)
        str.push(`${str.length ? 'p' : 'P'}áginas: ${props.libro.paginas}`)

    return str.join(", ")
})


const cargandoPortada = ref(true)

</script>

<style scoped>
.book-drop-enter-active,
.book-drop-leave-active {
    transition: all 0.5s ease;
}

.book-drop-enter-from,
.book-drop-leave-to {
    opacity: 0;
    transform: translateY(-30px);
}

.book-drop-enter-to,
.book-drop-leave-from {
    opacity: 1;
    transform: translateY(0);
}
</style>
