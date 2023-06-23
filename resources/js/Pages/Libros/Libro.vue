<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-[900px] mx-auto flex flex-col md:flex-row gap-10 mt-12">


            <div class=" card bg-base-100 shadow-2xl w-fit flex justify-center mx-auto">
                <img :src="libro.imagen" :alt="libro.titulo" class="object-contain">
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

                    <a class="btn btn-primary  w-fit" :href="libro.pdf" download>Descargar en PDF</a>
                </div>
            </div>
        </div>

        <hr class="my-14" />

        <h2 class="text-xl font-bold mt-8">Libros relacionados</h2>
        <div class="grid gap-4 mt-4" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">

            <div v-for="libroRelacionado in relacionados" :key="libroRelacionado.id"
                class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                <img :src="libroRelacionado.imagen" :alt="libroRelacionado.titulo" class="w-full h-48 object-cover">
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="text-lg font-bold mb-2">{{ libroRelacionado.titulo }}</h3>
                    <p class="text-gray-600 text-sm mb-2 flex justify-between">
                        <Link :href="`${route('libros')}?categoria=${libro.categoria}`"
                            class="no-underline badge badge-primary badge-outline">{{ libro.categoria }}</Link>
                        <TimeAgo :date="libro.published_at" />
                    </p>
                    <p class="text-sm mb-4">{{ libroRelacionado.descripcion }}</p>
                    <a class="btn btn-primary mt-auto" :href="libroRelacionado.pdf" download>Descargar en PDF</a>
                </div>
            </div>
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
