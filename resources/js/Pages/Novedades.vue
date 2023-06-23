
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Novedades</h1>
        <p>Todas las novedades de los contenidos de Tseyor.</p>


        <div class="flex justify-end mb-5">
            <SearchInput/>
        </div>

        <SearchResultsHeader :results="listado"/>

        <div v-if="listado.data.length > 0" class="grid gap-4"
            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
            <div v-for="contenido in listado.data" :key="contenido.slug" class="card bg-base-100 shadow flex-row ">

                <img :src="contenido.imagen" :alt="contenido.titulo" class="h-48 object-cover w-full" />
                <div class="p-4 flex flex-col">
                    <h2 class="text-lg font-bold mb-2">{{ contenido.titulo }}</h2>
                    <div class="flex justify-between">
                        <div class="badge badge-primary badge-outline">{{ contenido.coleccion }}</div>
                        <TimeAgo :date="contenido.fecha" />
                    </div>
                    <p class="text-gray-700 text-sm">{{ contenido.descripcion }}</p>
                    <Link :href="`/${contenido.coleccion}/${contenido.slug_ref}`"
                        class="mt-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
                    Leer más
                    </Link>
                </div>
            </div>
        </div>


        <pagination class="mt-6" :links="listado.links" />

    </div>
</template>



<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    listado: {
        default: () => { data: [] }
    }
});

const listado = ref(props.listado);



</script>
