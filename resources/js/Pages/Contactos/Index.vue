
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Contactos</h1>
        <p>Contactos distribuidos en todo el planeta a los que puedes acudir para consultar tus inquietudes.</p>

        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

                <div class="card bg-base-100 shadow flex-wrap flex-row md:flex-col p-5 lg:p-10 gap-4">
                    <Link :href="`${route('contactos')}`">
                    <span class="capitalize">Novedades</span>
                    </Link>

                    <div v-for="pais of paises" :key="pais.nombre" class="flex gap-2">
                        <Link :href="`${route('contactos')}?pais=${pais.nombre}`">
                        <span class="capitalize">{{ pais.nombre }}</span>
                        <small v-if="pais.total > 0">({{ pais.total }})</small>
                        </Link>
                    </div>
                </div>

            <div class="w-full flex-grow">


                <SearchResultsHeader :results="listado"/>



                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <div v-for="contacto in listado.data" :key="contacto.id" class="card bg-base-100 shadow">
                        <img :src="contacto.imagen_url" :alt="contacto.nombre" class="h-48 object-cover w-full" />
                        <div class="p-5 flex flex-col">
                            <h2 class="text-lg font-bold mb-2">{{ contacto.nombre }}</h2>
                            <div class="flex gap-2">
                                <p class="inline-block text-xs bg-gray-500 text-gray-50 rounded-full py-1 px-3">{{
                                    contacto.pais }}</p>
                                <p class="inline-block text-xs bg-gray-500 text-gray-50 rounded-full py-1 px-3">{{
                                    contacto.poblacion }}</p>

                            </div>
                            <Link :href="`/contactos/${contacto.slug}`"
                                class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Ver contacto
                            </Link>

                            <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="contacto.updated_at" />
                            </p>
                        </div>
                    </div>
                </div>


                <pagination class="mt-6" :links="listado.links"  />

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
    paises: {
        default: () => []
    }
});

const listado = ref(props.listado);
const paises = ref(props.paises)

</script>
