<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-[900px] mx-auto flex flex-col md:flex-row gap-10 mt-12">


            <div class=" card bg-base-100 shadow-2xl w-fit flex justify-center mx-auto">
                <img :src="libro.imagen" :alt="libro.titulo" class="object-contain rounded-[2px]">
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
        <div class="grid gap-4 mt-4" :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(28rem, 1fr))` }">

            <div v-for="libro in relacionados" :key="libro.id" class="card flex-row bg-base-100 shadow">
                <img :src="libro.imagen" :alt="libro.titulo" class="w-1/2 object-cover" />
                <div class="p-4 flex flex-col">
                    <h2 class="text-lg font-bold leading-6 mb-4">{{ libro.titulo }}</h2>
                    <div class="flex flex-wrap justify-between text-xs gap-3">
                        <div class="badge badge-primary badge-outline whitespace-nowrap">
                            <Link :href="`${route('libros')}?categoria=${libro.categoria}`">
                            {{ libro.categoria }}
                            </Link>
                        </div>
                        <TimeAgo :date="libro.published_at" />
                    </div>
                    <p class="text-gray-700 text-sm">{{ libro.descripcion }}</p>
                    <Link :href="route('libro', libro.slug)" class="btn mt-auto">
                    Ver libro
                    </Link>
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
