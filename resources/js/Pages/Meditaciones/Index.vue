<template>
    <Page>

        <div class="flex justify-between items-center mb-20">
            <Back :href="route('biblioteca')">Biblioteca</Back>
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="meditacion" necesita="administrar contenidos" />
            </div>
        </div>

        <div class="mb-20">
            <h1>Meditaciones</h1>
            <p>Talleres, meditaciones y otros documentos para uso de la comunidad Tseyor.</p>
        </div>

        <ContentMain class="flex justify-end mb-5">
            <SearchInput />
        </ContentMain>

        <div class="w-full flex gap-7 lg:gap-10 flex-wrap md:flex-nowrap">

            <Categorias :categorias="categorias" :url="route('meditaciones')" :resultados="filtrado?'Resultados':false" />

            <div class="w-full grow card shadow-2xs bg-base-100 px-5 py-7">

                <SearchResultsHeader :results="listado" :category="categoriaActiva" />

                <GridAppear class="grid gap-2 py-4" col-width="24rem" :time-lapse="0.01">
                    <Link v-for="contenido in listado.data" :key="contenido.id"
                        :href="route('meditacion', contenido.slug)"
                        class="text-primary hover:text-secondary transition-color duration-200 px-5 py-2 h-full flex flex-row items-baseline gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <Icon icon="ph:dot-fill" class="shrink-0" />
                    <div class="w-full">
                        <div v-html="contenido.titulo" class="capitalize lowercase font-bold" />
                        <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3" />
                        <div class="flex items-center mt-4">
                            <span v-if="!categoriaActiva || categoriaActiva == '_'" class="badge text-xs">{{
                                contenido.categoria }}</span>
                            <TimeAgo v-if="!categoriaActiva"
                                class="text-xs ml-auto opacity-50 group-hover:opacity-100 transition-opacity duration-200"
                                :date="contenido.updated_at" />
                        </div>
                    </div>
                    </Link>
                </GridAppear>


                <pagination class="mt-6" :links="listado.links" />

            </div>
        </div>
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
