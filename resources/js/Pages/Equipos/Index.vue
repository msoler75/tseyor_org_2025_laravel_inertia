
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Equipos</h1>
        <p>Equipos de trabajo y departamentos de la UTG.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <div class="w-full flex gap-5 flex-wrap md:flex-nowrap">


            <div
                class="card bg-base-100 shadow flex-wrap flex-row mb-3 md:flex-col p-5 lg:p-10 gap-4 mx-auto self-baseline w-full justify-evenly md:w-auto md:sticky md:top-20">
                <Link :href="`${route('equipos')}`" :class="!filtrado && !categoriaActiva ? 'text-blue-700 font-bold' : ''">
                <span class="capitalize">Nuevos</span>
                </Link>

                <div v-for="categoria of categorias" :key="categoria.nombre" class="flex gap-2"
                    :class="categoriaActiva == categoria.nombre ? 'text-blue-700 font-bold' : ''">
                    <Link :href="`${route('equipos')}?categoria=${categoria.nombre}`">
                    <span class="capitalize">{{ categoria.nombre }}</span>
                    <small v-if="categoria.total > 0">({{ categoria.total }})</small>
                    </Link>
                </div>
            </div>

            <div class="w-full flex-grow">

                <SearchResultsHeader :results="listado" />


                <div v-if="listado.data.length > 0" class="grid gap-4"
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(20rem, 1fr))` }">
                    <div v-if="listado.data.length > 0" v-for="equipo in listado.data" :key="equipo.id"
                        class="card flex-row bg-base-100 shadow overflow-hidden" relative>

                        <Link :href="`/equipos/${equipo.slug || equipo.id}`" class="w-1/2 h-full absolute left-0 top-0 " />
                        <img :src="equipo.imagen" :alt="equipo.nombre" class="w-1/2 object-cover cursor-pointer"
                            @click="" />

                        <div class="card-body p-5">
                            <h2 class="text-lg font-bold mb-2 capitalize">{{ equipo.nombre }}</h2>
                            <p class="text-gray-700 text-sm">{{ equipo.descripcion }}</p>
                            <div class="card-actions justify-end">
                                <Link :href="`/equipos/${equipo.slug || equipo.id}`"
                                    class="flex gap-3 items-center text-sm">
                                <Icon icon="ph:arrow-fat-line-right-duotone" />Ver Equipo
                                </Link>
                            </div>
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
