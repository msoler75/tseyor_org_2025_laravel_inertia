
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">

        <tabs>
            <tab name="Recientes" class=".comunicados-recientes">

                <div class="flex gap-12">
                    <div>
                        <h1>Comunicados Recientes</h1>
                        <p>Aquí puedes encontrar los últimas comunicados de Tseyor.</p>
                    </div>

                    <Planets />

                </div>


                <div class="flex justify-end mb-5">
                    <SearchInput />
                </div>


                <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

                    <div class="flex-grow">

                        <SearchResultsHeader :results="listado" />


                        <div v-if="listado.data.length > 0" class="grid gap-4"
                            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
                            <div v-if="listado.data.length > 0" v-for="comunicado in listado.data" :key="comunicado.id"
                                class="card bg-base-100 shadow">
                                <img :src="comunicado.imagen_url" :alt="comunicado.titulo"
                                    class="h-48 object-cover w-full" />
                                <div class="p-4">
                                    <h2 class="text-lg font-bold mb-2">{{ comunicado.titulo }}</h2>
                                    <p class="text-gray-700 text-sm">{{ comunicado.descripcion }}</p>
                                    <Link :href="`/comunicados/${comunicado.slug}`"
                                        class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    Leer más
                                    </Link>

                                    <p class="text-gray-600 mb-2 w-full text-xs text-right">
                                        <TimeAgo :date="comunicado.published_at" />
                                    </p>
                                </div>
                            </div>
                        </div>


                        <pagination class="mt-6" :links="listado.links" />

                    </div>

                    <div class="min-w-[250px] lg:min-w-[440px]" v-if="listado.first_page_url.indexOf('?buscar=') < 0">
                        <div class="card bg-base-100 shadow  p-10 space-y-7">

                            <h2 class="mb-5">Recientes</h2>
                            <ul class="list-disc">
                                <li v-for="comunicado in recientes" :key="comunicado.id">
                                    <Link :href="`/comunicados/${comunicado.slug}`"
                                        class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                    {{ comunicado.titulo }}
                                    </Link>
                                </li>
                            </ul>


                        </div>
                    </div>
                </div>

            </tab>

            <tab name="Archivados ⭐" class="flex justify-center">

                <div class="flex flex-col items-center gap-4">

                    <Link class=" btn btn-primary" :href="route('archivo.comunicados')">
                    <Icon icon="ph:arrow-fat-lines-right-duotone" /> Archivo de comunicados
                    </Link>
                    <div class="text-sm">Consulta todos los comunicados</div>
                </div>
            </tab>

        </tabs>

    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Tabs, Tab } from 'vue3-tabs-component';


import { Icon } from '@iconify/vue';

defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => []
    },
    recientes: {
        default: () => []
    }
});

const listado = ref(props.listado)
const recientes = ref(props.recientes)


</script>

<style>
@import url('./resources/css/tabs.css');
</style>
