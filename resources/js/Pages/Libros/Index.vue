
<template>
    <div class="container py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Libros</h1>
        <p>Libros que recogen toda la información de las conversaciones interdimensionales mantenidas con nuestros Guías
            Estelares.</p>

        <div class="flex w-full justify-between mb-5">
            <SearchInput class="flex-grow">
                <div class="flex items-baseline gap-3 w-full pl-1"><input id="titulos" type="checkbox"
                        v-model="selectors.soloTitulosLibros"> <label for="titulos" class="mb-0">Solo títulos</label></div>
            </SearchInput>
        </div>

        <div class="w-full flex gap-5 flex-wrap xl:flex-nowrap">


            <div
                class="card bg-base-100 shadow flex-wrap flex-row xl:flex-col p-5 lg:p-10 gap-4 self-baseline xl:sticky xl:top-20">
                <Link :href="`${route('libros')}`" :class="!filtrado && !categoriaActiva ? 'text-blue-700 font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                    :class="categoriaActiva == categoria.nombre ? 'text-blue-700 font-bold' : ''">
                    <Link :href="`${route('libros')}?categoria=${categoria.nombre}`">
                    <span class="capitalize">{{ categoria.nombre }}</span>
                    <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />

                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-show="selectors.soloTitulosLibros" v-for="libro in listado.data" :key="libro.id"
                        class="card shadow bg-base-100 p-5">
                        <Link :href="route('libro', libro.slug)" class="flex items-center gap-3">
                        <Icon icon="ph:book-duotone" /> {{ libro.titulo }}</Link>
                    </div>
                    <div v-show="!selectors.soloTitulosLibros" v-for="libro in listado.data" :key="libro.id"
                        class="card flex-row bg-base-100 shadow">
                        <img :src="libro.imagen" :alt="libro.titulo" class="w-1/2 object-cover" />
                        <div class="p-4 flex flex-col">
                            <h2 class="text-lg font-bold leading-6 mb-4">{{ libro.titulo }}</h2>
                            <div class="flex justify-between text-xs">
                                <div class="badge badge-primary badge-outline">
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


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { useSelectors } from '@/Stores/selectors'
defineOptions({ layout: AppLayout })
const selectors = useSelectors()


const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    }
});

const listado = ref(props.listado);
const categorias = ref(props.categorias)

</script>
