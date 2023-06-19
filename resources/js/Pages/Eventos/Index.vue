
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Eventos</h1>
        <p>Cursos y encuentros de la comunidad Tseyor a los que puedes acudir.</p>
        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div
                class="card bg-base-100 shadow flex-wrap flex-row md:mt-[4.4rem] md:flex-col p-5 lg:p-10 gap-4 mx-auto self-baseline md:sticky top-3">
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
                    <SearchInput />
                </div>

                <SearchResultsHeader :results="listado" />


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                    <div v-if="listado.data.length > 0" v-for="evento in listado.data" :key="evento.id"
                        class="card bg-base-100 shadow">
                        <img :src="evento.imagen_url" :alt="evento.titulo" class="h-48 object-cover w-full" />
                        <div class="p-4">
                            <h2 class="text-lg font-bold mb-2">{{ evento.titulo }}</h2>
                            <p class="text-gray-700 text-sm">{{ evento.descripcion }}</p>
                            <Link :href="`/eventos/${evento.slug || evento.id}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Leer más
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="evento.published_at" />
                            </p>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links" />

            </div>


        </div>
    </div>
</template>



<script setup>
import { ref } from 'vue';
import TimeAgo from '@/Components/TimeAgo.vue';
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import SearchInput from '@/Components/SearchInput.vue'
import SearchResultsHeader from '@/Components/SearchResultsHeader.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
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
