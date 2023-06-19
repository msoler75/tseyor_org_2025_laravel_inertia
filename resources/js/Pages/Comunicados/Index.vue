
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">

        <tabs>
            <tab name="Recientes">

                <div class="flex gap-12">
                    <div>
                        <h1>Comunicados</h1>
                        <p>Aquí puedes encontrar los últimas comunicados de Tseyor.</p>
                    </div>

                    <Planets />

                </div>

            </tab>

            <tab name="Archivados" class="flex justify-center">

                <div class="flex flex-col items-center gap-4">

                    <Link class=" btn btn-primary" :href="route('archivo.comunicados')">
                        <Icon icon="ph:arrow-fat-lines-right-duotone" /> Archivo de comunicados
                    </Link>
                    <div class="text-sm">Consulta todos los comunicados</div>
                </div>
            </tab>

        </tabs>


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
                        <img :src="comunicado.imagen_url" :alt="comunicado.titulo" class="h-48 object-cover w-full" />
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
.tabs-component {
    margin: 4em 0;
}

.tabs-component-tabs {
    border: solid 1px #ddd;
    border-radius: 6px;
    margin-bottom: 5px;
}

@media (min-width: 700px) {
    .tabs-component-tabs {
        border: 0;
        align-items: stretch;
        display: flex;
        justify-content: flex-start;
        margin-bottom: -1px;
    }
}

.tabs-component-tab {
    color: #999;
    font-size: 14px;
    font-weight: 600;
    margin-right: 0;
    list-style: none;
}

.tabs-component-tab:not(:last-child) {
    border-bottom: dotted 1px #ddd;
}

.tabs-component-tab:hover {
    color: #666;
}

.tabs-component-tab.is-active {
    color: #000;
}

.tabs-component-tab.is-disabled * {
    color: #cdcdcd;
    cursor: not-allowed !important;
}

@media (min-width: 700px) {
    .tabs-component-tab {
        background-color: #fff;
        border: solid 1px #ddd;
        border-radius: 3px 3px 0 0;
        margin-right: .5em;
        transform: translateY(2px);
        transition: transform .3s ease;
        z-index:1;
    }

    .tabs-component-tab.is-active {
        border-bottom: solid 1px #fff;
        z-index: 2;
        transform: translateY(0);
    }
}

.tabs-component-tab-a {
    align-items: center;
    color: inherit;
    display: flex;
    padding: .75em 1em;
    text-decoration: none;
}

.tabs-component-panels {
    padding: 4em 0;
    position: relative;
    z-index: 1;
}

@media (min-width: 700px) {
    .tabs-component-panels {
        background-color: #fff;
        border: solid 1px #ddd;
        border-radius: 0 6px 6px 6px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .05);
        padding: 4em 2em;
    }
}

.tabs-component-btn {
    cursor: pointer;
    background: #e1ecf4;
    border-radius: 3px;
    border: 1px solid #7aa7c7;
    padding: 4px 8px;
    color: #39739d;
}

.tabs-component-btn:hover {
    background-color: #b3d3ea;
    color: #2c5777;
}

.tabs-component-btn:active {
    background-color: #a0c7e4;
    box-shadow: none;
    color: #2c5777;
}
</style>
