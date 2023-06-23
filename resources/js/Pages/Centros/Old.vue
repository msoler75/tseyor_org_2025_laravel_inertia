
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <div class="flex gap-4 items-start">
            <div>
                <h1>Centros Tseyor</h1>
                <p>Casas y Muulasterios Tseyor a los que puedes acudir para dirigir tu camino hacia las estrellas.</p>
            </div>
            <Icon icon="vscode-icons:file-type-lighthouse" class="text-9xl text-gray-500 flex-shrink-0" />
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">

            <div class="card bg-base-100 shadow flex-wrap flex-row  md:flex-col p-5 lg:p-10 gap-4 mx-auto  self-baseline">
                <Link :href="`${route('centros')}`" :class="!filtrado && !paisActivo ? 'text-blue-700 font-bold' : ''">
                <span class="capitalize">Novedades</span>
                </Link>

                <div v-for="pais of paises" :key="pais.nombre" class="flex gap-2"
                    :class="paisActivo == pais.nombre ? 'text-blue-700 font-bold' : ''">
                    <Link :href="`${route('centros')}?pais=${pais.codigo}`">
                    <span class="capitalize">{{ pais.nombre }}</span>
                    <small v-if="pais.total > 0">({{ pais.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">


                <SearchResultsHeader :results="listado" />


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(16rem, 1fr))` }">
                    <div v-for="centro in listado.data" :key="centro.id" class="card bg-base-100 shadow">
                        <img :src="centro.imagen_url" :alt="centro.nombre" class="h-48 object-cover w-full" />
                        <div class="p-5 flex flex-col flex-grow">
                            <h2 class="text-lg font-bold mb-2">{{ centro.nombre }}</h2>
                            <div class="my-4 badge badge-primary badge-outline">{{
                                centro.pais }}</div>
                            <p class="text-xs">{{
                                centro.poblacion }}</p>
                            <Link :href="`/centros/${centro.slug}`" class="btn btn-primary mt-auto">
                            Ver centro
                            </Link>

                            <p v-if="false" class="text-gray-600 mb-2 w-full text-xs text-right">
                                <TimeAgo :date="centro.updated_at" />
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

import AppLayout from '@/Layouts/AppLayout.vue'


defineOptions({ layout: AppLayout })

const props = defineProps({
    paisActivo: { default: () => '' },
    filtrado: { default: () => '' },
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
