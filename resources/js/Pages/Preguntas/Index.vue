<template>
    <div class="container py-12 mx-auto">
        <div class="flex justify-between items-center mb-7">
            <span />
            <div class="flex gap-2">
                <Share />
                <Link
                    href="/libros/preguntas-y-respuestas-tseyor"
                    class="btn btn-xs btn-primary flex gap-2 items-center"
                    title="Descarga todo el glosario en pdf"
                >
                    <Icon icon="ph:download-duotone" />Descargar libro</Link
                >
                <AdminLinks modelo="lugar" necesita="administrar contenidos" />
            </div>
        </div>

        <div class="mx-auto flex flex-col justify-center items-center">
            <h1>Consulta</h1>
            <ConsultaTabs />
        </div>

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

        <ContentMain class="w-full flex gap-5 flex-wrap md:flex-nowrap">
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
        </ContentMain>
    </div>

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
