<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <span/>
            <div class="flex gap-2">
                <Link href="/libros/glosario-terminologico" class="btn btn-xs btn-error flex gap-2 items-center"
                    title='Descarga todo el glosario en pdf'>
                    <Icon icon="ph:download-duotone" /><span class="hidden sm:inline">Descargar </span>libro
                </Link>
                <Share />
                <AdminLinks modelo="termino" necesita="administrar contenidos" />
            </div>
        </div>

        <!-- para compartir enlace correctamente -->
        <h1 class="hidden">Glosario</h1>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Consulta</h1>
            <ConsultaTabs />
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput placeholder="Buscar en glosario..."/>
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
            <div class="w-full flex gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="w-full md:w-[7rem] shrink-0 card bg-base-100 shadow-2xs p-5 h-fit md:sticky md:top-20">
                <div class="flex flex-wrap md:hidden  gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2 hover:text-secondary"
                        :href="route('terminos') + '?letra=' + letraItem"
                        preserve-page
                        :class="letraItem == letraActual ? 'font-bold transform scale-110 text-primary' : ''">
                    {{ letraItem }}
                    </Link>
                </div>

                <div class="hidden md:grid grid-flow-dense grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2 hover:text-secondary"
                        :style="{ 'grid-column': Math.floor(index / (letras.length / 2)) + 1 }"
                        :href="route('terminos') + '?letra=' + letraItem"
                        preserve-page
                        :class="letraItem == letraActual ? 'font-bold transform scale-110 text-primary' : ''">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>


            <ScrollToHere if-same-path class="w-full grow" fade-on-navigate>

                <SearchResultsHeader v-if="!letra" :results="listado" />

                <GridAppear class="gap-8 mb-14" :time-lapse="0.01" col-width="16rem">
                    <Link v-for="contenido in listado.data" :key="contenido.id" :href="route('termino', contenido.slug)"
                        preserve-page
                        class="text-primary hover:text-secondary transition-color duration-200 w-fit card shadow-2xs hover:shadow-lg px-5 py-2 bg-base-100 h-fit">
                    <div v-html="contenido.nombre" class="capitalize lowercase font-bold text-lg" />
                    <div v-if="filtrado" v-html="contenido.descripcion" class="mt-3" />
                    </Link>
                </GridAppear>

                <pagination class="mt-6" :links="listado.links" />

            </ScrollToHere>


        </div>
        </PageWide>
    </Page>
</template>

<script setup>


const props = defineProps({
    listado: {
        default: () => { data: [] }
    },
    letras: {},
    letra: {},
    filtrado: {}
});


const letraActual = route().params.letra
</script>
