<template>
    <Page class="pb-0!">
        <PageHeader>
        <div class="flex justify-between items-center mb-20">
            <span />
            <div class="flex gap-2 items-center">
                <Link
                    href="/libros/preguntas-y-respuestas-tseyor"
                    class="btn btn-xs btn-error flex gap-2 items-center"
                    title="Descarga todo el glosario en pdf"
                    >
                    <Icon icon="ph:download-duotone" /><span class="hidden sm:inline">Descargar </span>libro
                </Link>
                <Share />
            </div>
        </div>

        <h1 class="hidden">Preguntas frecuentes</h1>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Consulta</h1>
            <ConsultaTabs />
        </div>

    </PageHeader>

        <ContentBar>
            <div class="w-full flex gap-2 items-center justify-between">
                <span />
                <div
                    @click="useNav().scrollToTopPage"
                    class="flex items-center gap-2 font-bold"
                >
                    Consulta
                    <Icon
                        icon="ph:arrow-circle-up-duotone"
                        class="transform scale-150"
                    />
                </div>
                <span />
            </div>
        </ContentBar>

        <PageWide>

        <div class="container mx-auto py-16 px-4 max-w-4xl">
            <h2 class="text-center mb-4">Explora por tema</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                    <h3 class="font-bold text-sm">Sobre TSEYOR</h3>
                    <ul class="text-sm space-y-2">
                        <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Qué es el grupo Tseyor?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Es esto una secta?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Qué significa la palabra Tseyor?</Link></li>
                    </ul>
                    <Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="btn btn-ghost btn-xs">Ver más →</Link>
                </div>
                <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                    <h3 class="font-bold text-sm">Guías Estelares</h3>
                    <ul class="text-sm space-y-2">
                        <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Cómo contactan con nosotros?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Por qué están aquí?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Por qué no se presentan?</Link></li>
                    </ul>
                    <Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="btn btn-ghost btn-xs">Ver más →</Link>
                </div>
                <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                    <h3 class="font-bold text-sm">Salto Cuántico</h3>
                    <ul class="text-sm space-y-2">
                        <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿En qué consiste el cambio?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿Cómo nos afectará?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿Podemos prepararnos?</Link></li>
                    </ul>
                    <Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="btn btn-ghost btn-xs">Ver más →</Link>
                </div>
            </div>
        </div>

        <ScrollToHere class="w-full flex gap-5 flex-wrap md:flex-nowrap">
                <Section class="container max-w-[1024px] mx-auto py-12">
                    <GridAppear col-width="16rem" class="gap-5">
                        <CardContent
                            v-for="seccion of secciones"
                            :key="seccion.titulo"
                            :title="seccion.titulo"
                            :description="seccion.descripcion"
                            :href="route('preguntas.seccion', seccion.slug)"
                            :gradient="false"
                        />
                    </GridAppear>
                </Section>
        </ScrollToHere>



    </PageWide>

     <div class="bg-base-100 w-full md:pb-16" style="--sectionHeight: 300px">
    <SectionBook
        titulo="Descarga el libro"
        :slug="libro.slug"
        :portada="libro.imagen"
        enlace="/libros/preguntas-y-respuestas-tseyor"
        buttonLabel="Ver en Biblioteca">
        <p>Aquí puedes descargar el libro de preguntas y respuestas.</p>
    </SectionBook>
    </div>

    </Page>

</template>

<script setup>
const props = defineProps({
    secciones: {
        type: Array,
        required: true,
    },
    libro: Object
});
</script>
