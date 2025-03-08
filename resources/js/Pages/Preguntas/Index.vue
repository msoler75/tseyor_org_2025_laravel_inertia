<template>
    <Preguntas titulo="" backUrl="quienes-somos" backText="¿Quiénes somos?">
    </Preguntas>
    <Sections>

        <Section class="container max-w-[1024px] mx-auto pt-12">

            <h1>Preguntas frecuentes y sus respuestas</h1>

            <p>Aquí puedes encontrar respuesta a las preguntas más habituales. Accede a las siguientes secciones para
                ampliar la información.</p>

            <GridAppear col-width="14rem" class="gap-5">
                <CardContent v-for="seccion of secciones" :key="seccion.titulo" :title="seccion.titulo"
                    :description="seccion.descripcion" :href="route('preguntas.seccion', seccion.slug)" :gradient="false" />
            </GridAppear>

        </Section>

        <Section class="w-full mt-12 pt-14 pb-20">

            <TextImage title="Descargar libro" class="md:max-w-[768px]" full
                srcImage="/almacen/medios/libros/313/Preguntas%20y%20respuestas%20Tseyor.th.jpg?h=300"
                buttonLabel="descargar" href="/libros/preguntas-y-respuestas-tseyor">
                <p>Aquí puedes descargar el libro de preguntas y respuestas.</p>
            </TextImage>


        </Section>

    </Sections>
</template>


<script setup>

const props = defineProps({
    secciones: {
        type: Array,
        required: true,
    },
});

</script>
