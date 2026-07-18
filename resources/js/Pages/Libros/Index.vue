<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="libro" necesita="administrar contenidos"  />
            </div>
        </div>

        <div class="flex justify-between items-start">
            <TitleInfo title="Libros Tseyor">
                <div>Libros que recogen toda la información de las <Referencia r="comunicados">conversaciones interdimensionales</Referencia>
                    (o comunicados telepáticos) mantenidas con nuestros <Referencia>Guías Estelares</Referencia>.
                </div>
                <div>La información se recoge en monografías (recopilación sobre un tema) y volúmenes de comunicados. También podéis encontrar obras vivenciales (memorias de convivencias realizadas por el grupo Tseyor), presentaciones, boletines, y otras obras temáticas.</div>
                <div class="my-3">El autor de todas las obras es la <Referencia>Universidad Tseyor de Granada</Referencia>.</div>
            </TitleInfo>
            <FontSizeControls/>
        </div>

        <div class="flex items-center justify-between gap-4 mb-5">
            <div class="flex items-center gap-2 shrink-0">
                <input id="titulos" type="checkbox" v-model="selectors.soloTitulosLibros">
                <label for="titulos" class="mb-0 whitespace-nowrap text-sm">Solo títulos</label>
            </div>

            <SearchInput v-model="query" compact
            placeholder="Buscar libros..." />
        </div>

        </PageHeader>

        <PageWide>

            <ScrollToHere if-same-page class="w-full flex gap-5 flex-wrap xl:flex-nowrap" :fade-on-navigate="false">

                <Categorias :categorias="categorias" :url="route('libros')" columna-breakpoint="xl" select-breakpoint="md"
                    :resultados="!!filtrado" select-class="w-full" />



                <FadeOnNavigate class="w-full grow">

                    <SearchResultsHeader :results="listado" :valid-search="busquedaValida" />

                    <GridAppear v-if="selectors.soloTitulosLibros" class="max-w-full grid gap-4" col-width="100%">
                        <div v-for="libro in listado.data" :key="libro.id"
                            class="card shadow-2xs bg-base-100 p-5 hover:text-primary transition-colors duration-250">

                            <Link :href="route('libro', libro.slug)" class="flex items-center gap-3 text-primary font-bold">
                            <span v-html="libro.titulo" /><span class="shrink-0 -order-1">📘</span></Link>
                        </div>
                    </GridAppear>

                    <GridAppear v-else
                        class="grid gap-4 grid-cols-1 sm:grid-cols-[repeat(auto-fill,minmax(22rem,1fr))]"
                        col-width="">
                        <CardContent v-for="contenido in listado.data" :key="contenido.id" :title="contenido.titulo"
                            :image="contenido.imagen" :href="route('libro', contenido.slug)"
                            :description="contenido.descripcion" :date="contenido.published_at" :tag="contenido.categoria"
                            imageClass="w-1/3 h-full   sm:w-[130px] lg:w-[170px]"
                            image-left class="h-[43vw] sm:h-[200px] lg:h-[250px]"
                            image-contained :image-view-transition-name="`imagen-libro-${contenido.id}`">
                            <template #imagex>
                                <div class="flex  w-full h-full items-center justify-center">
                                    <Libro3d :libro="contenido" imageClass="w-[120px] lg:w-[180px] 2xl:w-[213px]" />
                                </div>
                            </template>
                        </CardContent>

                    </GridAppear>


                    <pagination class="mt-6" :links="listado.links" />

                </FadeOnNavigate>


            </ScrollToHere>

        </PageWide>
    </Page>
</template>



<script setup>

const selectors = useSelectors()

const props = defineProps({
    categoriaActiva: { default: () => '' },
    filtrado: { default: () => '' },
    listado: {
        default: () => { data: [] }
    },
    categorias: {
        default: () => []
    },
    busquedaValida: Boolean
});

const query = ref(props.filtrado)

</script>
