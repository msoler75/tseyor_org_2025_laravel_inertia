<template>
    <Preguntas titulo="" backUrl="quienes-somos" backText="¿Quiénes somos?">
    </Preguntas>
    <Sections>

        <Section class="container max-w-[1024px] mx-auto pt-12">

            <h1>Preguntas frecuentes y sus respuestas</h1>

            <p>Aquí puedes encontrar respuesta a las preguntas más habituales. Accede a las siguientes secciones para
                ampliar la información.</p>

            <GridAppear col-width="14rem" class="gap-5">
                <CardContent v-for="section of sections" :key="section.title" :title="section.title"
                    :description="section.description" :href="route(section.route)" :gradient="false" />
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
import AppLayout from '@/Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })



const sections = [
    {
        title: "Extraterrestres o hermanos mayores, y otros seres",
        description: "Explora la naturaleza de nuestros amigos extraterrestres.",
        icon: "https://icones.org/icons/extraterrestres.png",
        route: "preguntas1"
    },
    {
        title: "El Salto Cuántico",
        description: "Descubre el concepto del salto cuántico y su impacto en este planeta.",
        icon: "https://icones.org/icons/salto_cuantico.png",
        route: "preguntas2"
    },
    {
        title: "Sobre el grupo TSEYOR",
        description: "Conoce más sobre el grupo TSEYOR y su misión.",
        icon: "https://icones.org/icons/tseyor.png",
        route: "preguntas3"
    }
];

</script>
