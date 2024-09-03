<template>
    <div class="container py-12 mx-auto">

        <div class="flex justify-between items-center mb-7">
            <span/>
            <Link href="/libros/glosario-terminologico" class="btn btn-sm btn-primary flex gap-2 items-center"
            title='Descarga todo el glosario en pdf'>
            <Icon icon="ph:download-duotone" />Descargar libro</Link>
            <AdminLinks modelo="guia" necesita="administrar contenidos" />
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

        <ContentMain class="w-full flex gap-5 flex-wrap md:flex-nowrap animate-fade-in">

            <div
                class="w-full h-fit md:w-auto min-w-[150px] lg:min-w-[240px] card bg-base-100 shadow p-10 md:sticky md:top-20 flex-row md:flex-col flex-wrap md:gap-1">
                <Link v-for="guia in guias.data" :key="guia.slug" :href="route('guia', guia.slug)"
                    class="inline font-semibold p-3 md:px-0 md:py-1 w-fit">
                {{ guia.nombre }}
                </Link>
            </div>

            <div class="w-full flex-grow">

                <GridAppear class="gap-8" col-width="12rem">
                    <CardContent v-for="contenido in guias.data" :key="contenido.id" :image="contenido.imagen"
                        :href="route('guia', contenido.slug)" imageClass="h-60">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 text-primary group-hover:text-secondary  group-hover:drop-shadow">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </GridAppear>


            </div>


        </ContentMain>
    </div>
</template>

<script setup>



const props = defineProps({
    guias: {
        default: () => []
    }
});

</script>
