<template>
    <Page>
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <Back :href="route('terminos')" inline>Términos</Back>
            <div class="flex gap-2">
                <Share />
                <Link href="/libros/glosario-terminologico" class="btn btn-xs btn-primary flex gap-2 items-center"
                    title='Descarga todo el glosario en pdf'>
                <Icon icon="ph:download-duotone" />Descargar libro</Link>
                <AdminLinks modelo="termino" necesita="administrar contenidos" :contenido="termino" />
            </div>
        </div>

        <!-- para compartir enlace correctamente -->
        <h1 class="hidden">{{ termino.nombre }}</h1>
        <h1 class="hidden">Glosario</h1>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Consulta</h1>
            <ConsultaTabs />
        </div>

        <div class="flex justify-end mb-5">
            <SearchInput :doSearch="false" @search="buscarClick" placeholder="Buscar en glosario..."/>
        </div>

        </PageHeader>


        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <Back :href="route('terminos')" inline class="opacity-100!">Términos</Back>
                <div @click="useNav().scrollToTopPage" class="flex items-center gap-2 font-bold">Consulta
                    <Icon icon="ph:arrow-circle-up-duotone" class="transform scale-150" />
                </div>
                <Back :href="route('terminos')" inline class="pointer-events-none opacity-0!">Términos</Back>
            </div>
        </ContentBar>

        <PageWide>
            <div class="w-full flex justify-between gap-7 lg:gap-12 flex-wrap md:flex-nowrap">

            <div class="w-full md:w-[7rem] shrink-0 card bg-base-100 shadow-2xs p-5 h-fit md:sticky md:top-20">
                <div class="flex flex-wrap md:hidden  gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2 hover:text-secondary"
                        :href="route('terminos') + '?letra=' + letraItem">
                    {{ letraItem }}
                    </Link>
                </div>

                <div class="hidden md:grid grid-flow-dense grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2 hover:text-secondary"
                        :style="{ 'grid-column': Math.floor(index / (letras.length / 2)) + 1 }"
                        :href="route('terminos') + '?letra=' + letraItem">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>

            <!-- scroll aqui -->
            <ContentMain class="animate-fade-in" fade-on-navigate>
                <div class="py-[5ch] bg-base-100 md:max-w-[80ch] mx-auto shadow-xl mb-20 px-7 rounded-xl">

                    <div class="prose mx-auto">
                        <h1 class="text-center xl:text-left capitalize lowercase">{{ termino.nombre }}</h1>
                        <p class="text-gray-600 text-xs my-5 text-right">
                            Última actualización:
                            <TimeAgo :date="termino.updated_at" />
                        </p>
                        <div class="mb-4"></div>
                        <Content :content="termino.texto" />
                    </div>
                </div>

                <div v-if="referencias.terminos.length"
                    class="flex flex-wrap sm:flex-nowrap gap-6 items-baseline mb-12 max-w-[80ch] mx-auto">
                    <div class="text-2xl font-bold mr-5 whitespace-nowrap">Véase también:</div>
                    <div class="flex gap-6 flex-wrap">
                        <Link v-for="contenido in referencias.terminos" :key="contenido.id"
                            :href="route('termino', contenido.slug)"
                            class="capitalize lowercase text-primary hover:text-secondary transition-color duration-200 w-fit h-fit font-bold text-lg card shadow-2xs hover:shadow-lg px-5 py-2 bg-base-100">
                        {{ contenido.nombre }}
                        </Link>
                    </div>
                </div>

                <GridAppear v-if="referencias.libros.length" col-width="200px" class="gap-5">
                    <Libro3d v-for="libro of referencias.libros" :key="libro.slug" :libro="libro"
                        imageClass="w-[150px]" />
                </GridAppear>
            </ContentMain>




            <div class="w-[7rem] card bg-base-100 shadow-2xs p-5 h-fit sticky top-20 opacity-0 hidden lg:flex">
                <div class="letras grid grid-cols-2 gap-2">
                    <Link v-for="letraItem, index in letras" :key="index" class="p-2"
                        :href="route('terminos') + '?letra=' + letraItem">
                    {{ letraItem }}
                    </Link>
                </div>
            </div>

        </div>

        <hr class="my-12" />


        <div class="flex justify-between my-12">
            <Link v-if="anterior" :href="anterior.slug" class="hover:underline">
            ‹&nbsp;&nbsp; {{ anterior.nombre }}</Link>
            <span v-else />
            <Link v-if="siguiente" :href="siguiente.slug" class="hover:underline">{{
                siguiente.nombre }} &nbsp;&nbsp;›
            </Link>
            <span v-else />
        </div>
</PageWide>
    </Page>
</template>

<script setup>
import PageHeader from '@/Components/PageHeader.vue';

const props = defineProps({
    termino: {
        type: Object,
        required: true,
    },
    siguiente: {
        type: [Object, null],
        required: true,
    },
    anterior: {
        type: [Object, null],
        required: true,
    },
    letras: {},
    referencias: {
        type: Object,
        required: true,
    },
});

function buscarClick(query) {
    router.visit(route('terminos') + '?buscar=' + query)
}
</script>
