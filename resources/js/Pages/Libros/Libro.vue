<template>
    <div class="container mx-auto py-12">

        <div class="flex justify-between items-center mb-20">
            <Back>Libros</Back>
            <AdminPanel modelo="libro" necesita="administrar contenidos" :contenido="libro"/>
        </div>

        <div class="max-w-[900px] mx-auto flex flex-col md:flex-row gap-10 mt-12">


            <div class=" card bg-base-100 shadow-2xl w-fit h-fit flex justify-center mx-auto">
                <ImageShadow :src="libro.imagen" :alt="libro.titulo" zclass="object-contain rounded-[2px]"/>
            </div>

            <div class="p-6">

                <h1 class="text-2xl font-bold mb-4">{{ libro.titulo }}</h1>
                <p class="text-gray-600 text-sm mb-2 flex justify-between">
                    <Link :href="`${route('libros')}?categoria=${libro.categoria}`"
                        class="no-underline badge badge-primary badge-outline">{{ libro.categoria }}</Link>
                    <TimeAgo :date="libro.published_at" />
                </p>
                <p class="mb-8 text-xs">Edición: {{ libro.edicion }}, páginas: {{ libro.paginas }}</p>
                <div class="prose" v-html="libro.descripcion"></div>
                <div class="w-full flex justify-center sm:justify-end mt-7">

                    <a class="btn btn-primary w-fit flex gap-3" :href="libro.pdf" download>
                        <Icon icon="ph:download-duotone" /> Descargar en PDF
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-14" />

        <h2 class="text-xl font-bold mt-8">Libros relacionados</h2>
        <div class="grid gap-4 mt-4" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(32rem, 1fr))` }">

            <CardContent v-for="contenido in relacionados" :key="contenido.id"
                        :title="contenido.titulo" :image="contenido.imagen" :href="route('libro', contenido.slug)"
                        :description="contenido.descripcion" :date="contenido.published_at"
                        :tag="contenido.categoria"
                        image-left
                        imageClass="h-80"/>
        </div>


    </div>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    libro: {
        type: Object,
        required: true,
    },
    relacionados: {
        type: Array, required: true
    }
});
</script>
