<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <AdminLinks modelo="normativa" necesita="administrar legal" />
            </div>
        </div>

        <h1>Normativas</h1>
        <p>Reglamentos, protocolos, normativas, lineamientos, acuerdos y otros documentos para uso de la comunidad
            Tseyor.
        </p>

        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>


        <div class="w-full flex gap-7 lg:gap-10 flex-wrap md:flex-nowrap">

            <Categorias :categorias="categorias" :url="route('normativas')" columna-breakpoint="md"
                select-breakpoint="sm" div-class="w-full md:w-fit " />

            <div class="w-full grow card shadow-2xs bg-base-100 px-5 py-7">

                <SearchResultsHeader :results="listado" :category="categoriaActiva" />

                <GridAppear class="gap-2 py-4" :time-lapse="0.01" col-width="24rem">
                    <Link v-for="contenido in listado.data" :key="contenido.id"
                        :href="route('normativa', contenido.slug)"
                        class=" hover:text-primary transition-color duration-200 px-5 py-2 h-full flex flex-row items-baseline gap-3 hover:bg-base-200/40 rounded-xl w-full">
                    <Icon icon="ph:dot-fill" class="shrink-0" />
                    <div class="w-full">
                        <div v-html="contenido.titulo" class="capitalize lowercase font-bold" />
                        <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3" />
                        <div class="flex items-center mt-4">
                            <span v-if="!categoriaActiva || categoriaActiva == '_'" class="badge text-xs">{{
                                contenido.categoria
                                }}</span>
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
    </div>
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
