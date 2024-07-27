<template>
    <AppLayout title="Novedades de la web">
        <div class="container py-12 mx-auto">

            <AdminLinks necesita="administrar contenidos" class="mb-3" />

            <h1>Novedades</h1>
            <p>Todas las novedades de los contenidos de Tseyor.</p>

            <div class="flex justify-end mb-5">
                <SearchInput />
            </div>

            <SearchResultsHeader :results="listado" />

            <GridAppear col-width="28rem" class="gap-4">
                <CardContent v-for="contenido in listado.data" :key="contenido.slug" image-left
                    :title="contenido.titulo + (contenido.visibilidad != 'P' ? ' (borrador)' : '')"
                    :draft="contenido.visibilidad != 'P'" :image="contenido.imagen"
                    :href="'/' + contenido.coleccion + '/' + contenido.slug_ref" :tag="traducir(contenido.coleccion)"
                    :description="contenido.descripcion" :date="contenido.fecha" />
            </GridAppear>

            <pagination class="mt-6" :links="listado.links" />

        </div>
    </AppLayout>
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
