
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Eventos</h1>
        <p>Eventos que recogen toda la información de las conversaciones interdimensionales mantenidas con nuestros Guías Estelares.</p>
        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div class="card flex-wrap flex-row md:mt-[5rem] md:flex-col bg-base-100 p-5 lg:p-10 gap-4 mx-auto self-baseline md:sticky top-3">
                    <Link :href="`${route('eventos')}`">
                    <span class="capitalize">Novedades</span>
                    </Link>

                    <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2">
                        <Link :href="`${route('eventos')}?categoria=${categoria.nombre}`">
                        <span class="capitalize">{{ categoria.nombre }}</span>
                        <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                        </Link>
                    </div>
                </div>

            <div class="w-full flex-grow">
                <div class="flex justify-end mb-5">
                    <form :action="`/eventos?buscar=${filtro}`">
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
                    <div v-if="listado.data.length > 0" v-for="evento in listado.data" :key="evento.id"
                        class="card bg-base-100">
                        <img :src="evento.imagen_url" :alt="evento.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ evento.titulo }}</h2>
                            <p class="text-gray-700 text-sm">{{ evento.descripcion }}</p>
                            <Link :href="`/eventos/${evento.slug||evento.id}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Leer más
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="evento.published_at" />
                            </p>
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
      return route('eventos', {
        page: 'pagina',
        buscar: props.filtrado,
      }).replace('pagina', path);
    }

watch(filtro, () => {
    if (filtro.value == "" && props.filtrado)
        router.visit(route('eventos'))
})

function buscar() {
    router.get(route('eventos'), { buscar: filtro.value }, { replace: true })
}
</script>
