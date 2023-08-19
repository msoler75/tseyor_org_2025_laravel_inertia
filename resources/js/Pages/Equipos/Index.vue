
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <AdminPanel modelo="equipo" necesita="administrar equipos" class="mb-3"/>

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
                    :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">

                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                        :title="contenido.nombre" :href="route('equipo', contenido.slug)" image-left
                        image-class="min-h-[150px]" class="min-h-16" :description="contenido.descripcion">
                        <div class="mb-2 mr-6 flex sm:ml-auto gap-3 text-2xl items-center self-end justify-center">
                            <Icon icon="ph:user-duotone" />
                            {{ contenido.usuarios_count }}
                        </div>
                    </CardContent>

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
