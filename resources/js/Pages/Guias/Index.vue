<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-7">
            <span />
            <div class="flex gap-2">
                <Share />
                <Link href="/libros/glosario-terminologico" class="btn btn-xs btn-primary flex gap-2 items-center"
                    title='Descarga todo el glosario en pdf'>
                <Icon icon="ph:download-duotone" />Descargar libro</Link>
                <AdminLinks modelo="guia" necesita="administrar contenidos" />
            </div>
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Glosario</h1>
            <GlosarioTabs />
        </div>


        <div class="flex justify-end mb-5">
            <SearchInput />
        </div>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <span />
                <div @click="useNav().scrollToTopPage" class="flex items-center gap-2 font-bold">Glosario
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
                <span />
            </div>
        </ContentBar>

        <ContentMain class="w-full flex gap-5 flex-wrap md:flex-nowrap animate-fade-in" :fade-on-navigate="false">

            <Categorias title="Vibración" :novedades="false" :categorias="categorias" :counters="false" :url="route('guias')"
            :resultados="!!filtrado" div-class="min-w-[150px] lg:min-w-[200px]" select-class="w-full" />

            <FadeOnNavigate class="w-full flex-grow">

                <GridAppear class="gap-8" col-width="12rem">
                    <CardContent v-for="contenido in guias.data" :key="contenido.id" :image="contenido.imagen"
                        :href="route('guia', contenido.slug)" imageClass="h-60">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 text-primary group-hover:text-secondary  group-hover:drop-shadow">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </GridAppear>


            </FadeOnNavigate>


        </ContentMain>
    </div>
</template>

<script setup>



const props = defineProps({
    guias: {
        default: () => []
    },
    categorias: {
        type: Array,
        required: true
    },
    filtrado: {type: [String, null],
        required: true
    }
});

</script>
