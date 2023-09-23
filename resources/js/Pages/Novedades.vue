
<template>
    <div class="container py-12 mx-auto">

        <AdminPanel necesita="administrar contenidos" class="mb-3" />

        <h1>Novedades</h1>
        <p>Todas las novedades de los contenidos de Tseyor.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <SearchResultsHeader :results="listado" />

        <div v-if="listado.data.length > 0" class="grid gap-4"
            :style="{ 'grid-template-columns': `repeat(auto-fill, minmax(28rem, 1fr))` }">
            <CardContent v-for="contenido in listado.data" :key="contenido.slug"
            image-left
            :title="contenido.titulo"
            :image="contenido.imagen"
            :href="route(contenido.coleccion)+'/'+contenido.slug_ref"
            :tag="traducir(contenido.coleccion)"
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


const traducciones = {
    paginas: 'páginas',
    guias: 'guías estelares',
    terminos: 'glosario',
    lugares: 'lugares de la galaxia'
}

function traducir(col) {
    return traducciones[col] || col
}


</script>
