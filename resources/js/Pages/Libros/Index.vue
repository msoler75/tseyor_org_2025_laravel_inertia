
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Libros</h1>
        <p>Libros que recogen toda la información de las conversaciones interdimensionales mantenidas con nuestros Guías Estelares.</p>
        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div class="card flex-wrap flex-row md:flex-col bg-base-100 p-5 lg:p-10 gap-4">
                    <Link :href="`${route('libros')}`">
                    <span class="capitalize">Novedades</span>
                    </Link>

                    <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2">
                        <Link :href="`${route('libros')}?categoria=${categoria.nombre}`">
                        <span class="capitalize">{{ categoria.nombre }}</span>
                        <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                        </Link>
                    </div>
                </div>

            <div class="w-full flex-grow">
                <div class="flex justify-end mb-5">
                    <form :action="`/libros?buscar=${filtro}`">
                        <div class="flex gap-4">
                            <input name="buscar" type="search" placeholder="Palabras clave..." v-model="filtro" class="w-full max-w-[200px] border border-gray-200 rounded focus:outline-none focus:border-gray-400" />

                            <button type="submit" @click.prevent="buscar()" class="btn btn-primary"
                                :disabled="filtro == filtrado">
                                Buscar
                            </button>

                            <button v-if="filtrado" type="button" @click.prevent="filtro = ''"
                                class="btn btn-secondary">
                                Limpiar
                            </button>
                        </div>
                    </form>
                </div>

                <h1 v-if="listado.data.length > 0">
                    <span v-if="filtrado">
                        Resultados de '{{ filtrado }}'
                    </span>
                </h1>

                <div v-else>No hay resultados</div>


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-for="libro in listado.data" :key="libro.id" class="card flex-row bg-base-100">

                        <img :src="libro.imagen_url" :alt="libro.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4 flex flex-col">
                            <h2 class="text-lg font-bold mb-2">{{ libro.titulo }}</h2>
                            <div class="flex justify-between">
                                <div class="badge badge-primary badge-outline">{{ libro.categoria}}</div>
                                <TimeAgo :date="libro.published_at" />
                            </div>
                            <p class="text-gray-700 text-sm">{{ libro.descripcion }}</p>
                            <Link :href="`/libros/${libro.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Leer más
                            </Link>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" :url="urlPaginacion(listado.path)" />

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
    filtrado: { default: () => "" },
   listado: {
        default: () => {data:[]}
    },
    categorias: {
        default: () => []
    }
});

const filtro = ref(props.filtrado)
const listado= ref(props.listado);
const categorias = ref(props.categorias)

function urlPaginacion(path) {
      return route('libros', {
        page: 'pagina',
        buscar: props.filtrado,
      }).replace('pagina', path);
    }

watch(filtro, () => {
    if (filtro.value == "" && props.filtrado)
        router.visit(route('libros'))
})

function buscar() {
    router.get(route('libros'), { buscar: filtro.value }, { replace: true })
}
</script>
