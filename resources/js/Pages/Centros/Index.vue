
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <div class="flex gap-4 items-start">
            <div>
                <h1>Centros Tseyor</h1>
                <p>Casas y Muulasterios Tseyor a los que puedes acudir para dirigir tu camino hacia las estrellas.</p>
            </div>
            <Icon icon="vscode-icons:file-type-lighthouse" class="text-9xl text-gray-500 flex-shrink-0" />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="card bg-base-100 shadow flex-wrap flex-row md:mt-[5rem] md:flex-col p-5 lg:p-10 gap-4 mx-auto">
                <Link :href="`${route('centros')}`">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="pais of paises" :key="pais.nombre" class="flex gap-2">
                    <Link :href="`${route('centros')}?pais=${pais.nombre}`">
                    <span class="capitalize">{{ pais.nombre }}</span>
                    <small v-if="pais.total > 0">({{ pais.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">
                <div class="flex justify-end mb-5">
                    <form :action="`/centros?buscar=${filtro}`">
                        <div class="flex gap-4">
                            <input name="buscar" type="search" placeholder="País, dirección..." v-model="filtro"
                                class="w-full max-w-[200px] border border-gray-200 rounded focus:outline-none focus:border-gray-400" />

                            <button type="submit" @click.prevent="buscar()" class="btn btn-primary"
                                :disabled="filtro == filtrado">
                                Buscar
                            </button>

                            <button v-if="filtrado" type="button" @click.prevent="filtro = ''" class="btn btn-secondary">
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
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <div v-for="centro in listado.data" :key="centro.id" class="card bg-base-100">
                        <img :src="centro.imagen_url" :alt="centro.nombre" class="h-48 object-cover w-full" />
                        <div class="p-5 flex flex-col">
                            <h2 class="text-lg font-bold mb-2">{{ centro.nombre }}</h2>
                            <div class="flex gap-2">
                                <p class="inline-block text-xs bg-gray-500 text-gray-50 rounded-full py-1 px-3">{{
                                    centro.pais }}</p>
                                <p class="inline-block text-xs bg-gray-500 text-gray-50 rounded-full py-1 px-3">{{
                                    centro.poblacion }}</p>

                            </div>
                            <Link :href="`/centros/${centro.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Ver centro
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="centro.updated_at" />
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
import { Icon } from '@iconify/vue';

defineOptions({ layout: AppLayout })

const props = defineProps({
    filtrado: { default: () => "" },
    listado: {
        default: () => { data: [] }
    },
    paises: {
        default: () => []
    }
});

const filtro = ref(props.filtrado)
const listado = ref(props.listado);
const paises = ref(props.paises)

function urlPaginacion(path) {
    return route('centros', {
        page: 'pagina',
        buscar: props.filtrado,
    }).replace('pagina', path);
}

watch(filtro, () => {
    if (filtro.value == "" && props.filtrado)
        router.visit(route('centros'))
})

function buscar() {
    router.get(route('centros'), { buscar: filtro.value }, { replace: true })
}
</script>
