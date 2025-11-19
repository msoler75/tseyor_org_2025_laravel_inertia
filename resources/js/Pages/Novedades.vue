<template>

    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks necesita="administrar contenidos" />
            </div>
        </div>

        <h1>Novedades</h1>
        <p>Todas las novedades de los contenidos de Tseyor.</p>

        <Link href="/pepito">Enlace erróneo</Link>

        <div class="flex justify-end mb-5">
            <SearchInput placeholder="Buscar en novedades..."/>
        </div>

        </PageHeader>

        <PageWide>

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

        </PageWide>

    </Page>
</template>



<script setup>
import traducir from '@/composables/traducciones'

const props = defineProps({
    listado: {
        default: () => { data: [] }
    }
});


</script>
