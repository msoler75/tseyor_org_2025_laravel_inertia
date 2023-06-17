
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8 bg-white">
        <h1>Libros</h1>
        <p>Libros que recogen toda la información de las conversaciones interdimensionales mantenidas con nuestros Guías Estelares.</p>
        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div>
                <div class="card shadow rounded p-10 space-y-7">
                    <ul>
                        <li class="flex gap-2">
                        <Link :href="`${route('libros')}`">
                            <span class="capitalize">Novedades</span>
                        </Link>
                        </li>

                        <li v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                        >
                        <Link :href="`${route('libros')}?categoria=${categoria.nombre}`">
                            <span class="capitalize">{{ categoria.nombre }}</span>
                            <small v-if="categoria.total>0">({{ categoria.total }})</small>
                        </Link>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="w-full flex-grow">
                <div class="flex justify-end mb-5">
                    <form :action="`/libros?buscar=${filtro}`">
                        <div class="flex gap-4 mt-5">
                            <input name="buscar" type="search" placeholder="Palabras clave..." v-model="filtro" class="w-full max-w-[200px] border border-gray-200 rounded focus:outline-none focus:border-gray-400" />

                            <button type="submit" @click.prevent="buscar()" class="btn-primary"
                                :disabled="filtro == filtroResultados">
                                Buscar
                            </button>

                            <button v-if="filtroResultados" type="button" @click.prevent="filtro = ''"
                                class="btn-secondary">
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>

                <h1 v-if="resultados.data.length > 0">
                    <span v-if="filtroResultados">
                        Resultados de '{{ filtroResultados }}'
                    </span>
                </h1>

                <div v-else>No hay resultados</div>


                <div class="grid grid-cols-1 gap-8 mt-8">
                    <div v-if="resultados.data.length > 0" v-for="libro in resultados.data" :key="libro.id"
                        class="rounded-lg flex">
                        <img :src="libro.imagen_url" :alt="libro.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ libro.titulo }}</h2>
                            <p class="inline-block text-xs bg-gray-500 text-gray-50 rounded-full py-1 px-3">{{ libro.categoria}}</p>
                            <p class="text-gray-700 text-sm">{{ libro.descripcion }}</p>
                            <Link :href="`/libros/${libro.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Leer más
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="libro.published_at" />
                            </p>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="resultados.links" :url="urlPaginacion(resultados.path)" />

            </div>


        </div>
    </div>
</template>



<script setup>
import { ref, watch } from 'vue';
import TimeAgo from '@/Components/TimeAgo.vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    filtroResultados: { default: () => "" },
    resultados: {
        default: () => []
    },
    categorias: {
        default: () => []
    }
});

const filtro = ref(props.filtroResultados)
const resultados = ref(props.resultados);
const categorias = ref(props.categorias)

function urlPaginacion(path) {
      return route('libros', {
        page: 'pagina',
        buscar: props.filtroResultados,
      }).replace('pagina', path);
    }

watch(filtro, () => {
    if (filtro.value == "" && props.filtroResultados)
        router.visit(route('libros'))
})

function buscar() {
    router.get(route('libros'), { buscar: filtro.value }, { replace: true })
}
</script>
