<template>

    <div class="p-4 lg:px-0 lg:container py-12 mx-auto">

        <AdminLinks necesita="administrar contenidos" class="mb-3" />

        <h1>Novedades</h1>
        <p>Todas las novedades de los contenidos de Tseyor.</p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <SearchResultsHeader :results="listado" />

        <GridAppear class="max-w-[100vw] gap-4
        grid-cols-[repeat(auto-fill,minmax(20rem,1fr))]
        sm:grid-cols-[repeat(auto-fill,minmax(24rem,1fr))]
        xl:grid-cols-[repeat(auto-fill,minmax(28rem,1fr))]
        ">
            <CardContent v-for="contenido in listado.data" :key="contenido.slug" image-left
                :title="contenido.titulo + (contenido.visibilidad == 'B' ? ' (borrador)' : contenido.visibilidad == 'O' ? ' (privado)' : '')"
                :draft="contenido.visibilidad != 'P'" :image="contenido.imagen"
                :href="'/' + contenido.coleccion + '/' + contenido.slug_ref" :tag="traducir(contenido.coleccion)"
                :description="contenido.descripcion" :date="contenido.fecha" class="max-w-full"
                description-class="max-h-[15ch]" />
        </GridAppear>

        <pagination class="mt-6" :links="listado.links" />

    </div>
</template>



<script setup>
import traducir from '@/composables/traducciones'

const props = defineProps({
    listado: {
        default: () => { data: [] }
    }
});

const listado = ref(props.listado);




</script>
