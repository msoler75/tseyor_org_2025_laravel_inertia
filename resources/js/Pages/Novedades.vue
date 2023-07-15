
<template>
    <div class="max-w-screen-xl px-4 py-12 mx-auto sm:px-6 lg:px-8">
        <h1>Novedades</h1>
        <p>Todas las novedades de los contenidos de Tseyor.</p>


        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <SearchResultsHeader :results="listado" />

        <div v-if="listado.data.length > 0" class="grid gap-4"
            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(24rem, 1fr))` }">
            <CardContent v-for="contenido in listado.data" :key="contenido.slug"
            image-left
            :title="contenido.titulo"
            :image="contenido.imagen"
            :href="`/${contenido.coleccion}/${contenido.slug_ref}`"
            :tag="contenido.coleccion"
            :description="contenido.descripcion"
            :date="contenido.fecha"/>
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
