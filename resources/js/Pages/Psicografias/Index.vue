<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="psicografia" necesita="administrar contenidos" />
            </div>
        </div>

        <TitleInfo title="Psicografías">
            <p>Dibujos psicografiados (recibidos por <Referencia>telepatía externa</Referencia> e ilustrados) por el hermano <Referencia>Puente</Referencia>, originalmente transmitidos por <Referencia>Aumnor</Referencia>.</p>
            <p>Ahora puedes consultar las <Link href="/libros?categoria=Psicografías">monografías</Link> correspondientes en nuestra sección de libros.</p>
        </TitleInfo>

        </PageHeader>

        <PageWide>

            <ContentMain class="flex justify-end mb-5">
                <SearchInput />
            </ContentMain>

        <div class="w-full flex gap-7 lg:gap-10 flex-wrap md:flex-nowrap">

            <Categorias :categorias="categorias" :url="route('psicografias')" :resultados="filtrado?'Resultados':false" />

            <div class="w-full">

                <SearchResultsHeader :results="listado" :category="categoriaActiva" />

                <GridAppear class="w-full gap-4" col-width="22rem">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                    :href="route('psicografia', contenido.slug)" imageClass="h-60"
                    imageWidth="600">
                    <div
                    class="text-center p-2 text-md font-bold transition duration-300 text-primary group-hover:text-secondary  group-hover:drop-shadow-xs">
                    <span v-html="contenido.titulo"/>
                </div>
            </CardContent>
        </GridAppear>

        <pagination class="mt-6" :links="listado.links" />
    </div>





        </div>

        </PageWide>
    </Page>
</template>


<script setup>


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
// const categorias = ref(props.categorias)

</script>
