<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <span />
            <div class="flex gap-2">
                <Share />
                <Link href="/libros/los-guias-estelares" class="btn btn-xs btn-primary flex gap-2 items-center"
                    title='Descarga todo el glosario en pdf'>
                <Icon icon="ph:download-duotone" />Descargar libro</Link>
                <AdminLinks modelo="guia" necesita="administrar contenidos" />
            </div>
        </div>


        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Consulta</h1>
            <ConsultaTabs />
        </div>


        <div class="flex justify-between mb-5">
         <TitleInfo title="" classes="!mb-0">
                <p>Los Guías Estelares son tutores de la <Referencia>Confederación de Mundos Habitados de la Galaxia</Referencia> que mantienen contacto <Referencia r="telepatía">
                telepático</Referencia> con el <Referencia>grupo TSEYOR</Referencia> a través del canalizador <Referencia>Chac-Mool</Referencia> <Referencia>Puente</Referencia>.
                </p>
                <p>El objetivo de este contacto y <Referencia r="Contacto entre ambas civilizaciones">tutelaje entre civilizaciones</Referencia> es servir de referenciación para que la humanidad planetaria de la Tierra pueda prepararse para la llegada del <Referencia>Rayo Sincronizador</Referencia> que resituará la humanidad a su posición real en el <Referencia>Tiempo Real del Universo</Referencia>.</p>
         </TitleInfo>
            <SearchInput placeholder="Buscar guía estelar..."/>
        </div>

        </PageHeader>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <span />
                <div @click="useNav().scrollToTopPage" class="flex items-center gap-2 font-bold">Consulta
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
                <span />
            </div>
        </ContentBar>

        <PageWide>

        <ContentMain class="animate-fade-in" :fade-on-navigate="false">

            <Categorias v-if="false" title="Vibración" :novedades="false" :categorias="categorias" :counters="false" :url="route('guias')"
            :resultados="!!filtrado" div-class="top-32! min-w-[150px] lg:min-w-[200px]" div-select-class="top-28!"  />

            <FadeOnNavigate class="w-full grow">

                <GridAppear class="gap-8" col-width="12rem">
                    <CardContent v-for="contenido in listado.data" :key="contenido.id" :image="contenido.imagen"
                        :href="route('guia', contenido.slug)" imageClass="h-60">
                        <div
                            class="text-center p-2 text-xl font-bold transition duration-300 text-primary group-hover:text-secondary  group-hover:drop-shadow-xs">
                            {{ contenido.nombre }}</div>
                    </CardContent>
                </GridAppear>


            </FadeOnNavigate>


        </ContentMain>

    </PageWide>
    </Page>
</template>

<script setup>



const props = defineProps({
    listado: {
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
