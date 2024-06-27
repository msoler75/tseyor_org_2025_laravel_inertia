<template>
    <Sections>

        <Section height="400">
            <TextImage title="Curso Holístico Tseyor" srcImage="/almacen/medios/paginas/curso.png" image-right
                srcWidth="373" srcHeight="482" buttonLabel="Inscríbete" :href="route('cursos.inscripcion.nueva')">
                <p>Conoce las enseñanzas de los <Referencia>Guías Estelares</Referencia> con nuestro curso gratuito.</p>
            </TextImage>
        </Section>

        <Section>
            <FondoEstrellado class="w-full h-full flex justify-center items-center">
                <VideoPlayer class="" videoUrl="https://www.youtube.com/embed/AkOZbfGdXbU" />
            </FondoEstrellado>
        </Section>


        <Section>
            <FeatureColumns :features="[
                {
                    title: 'Exploración Cósmica',
                    icon: 'ph:alien-duotone',
                    description: 'Conoce el papel de la Confederación de Mundos Habitados de la Galaxia.'
                },
                {
                    title: 'Desarrollo personal',
                    icon: 'ph:lightbulb-duotone',
                    description: 'Descubre herramientas prácticas para el desarrollo interior. '
                },
                {
                    title: 'Extrapolación mental',
                    icon: 'ph:rocket-duotone',
                    description: 'Expande tu comprensión de la existencia de otros mundos.'
                },
                {
                    title: 'Cuántica y Conciencia',
                    icon: 'ph:atom-duotone',
                    description: 'Explora las posibilidades de un próximo salto cuántico.'
                }

            ]" />
        </Section>

        <Section height="500">
            <TextImage title="Filosofía Cósmico-Crística" buttonLabel="conoce nuestra filosofía"
                :href="route('filosofia')" srcImage="/almacen/medios/paginas/hombre-espacio.jpg" textClass="container"
                image-right>
                <p>El curso ha sido desarrollado en base a las enseñanzas de nuestros <Referencia>Guías Estelares
                    </Referencia>
                </p>
                <p>Funciona bajo el mecanismo de la retroalimentación que nos permite el aprendizaje mútuo.</p>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Libro del Curso Holístico"
                srcImage="https://via.placeholder.com/400x600.png/0000ff?text=CURSO HOLISTICO"
                buttonLabel="Descargar Libro" href="/ong">
                <p>Lee este conjunto de historias para comprender la filosofía de Tseyor.</p>
            </TextImage>
        </Section>

        <Section>
            <FeaturedPosts title="Próximos cursos" :posts="cursos" />
        </Section>


    </Sections>
</template>



<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    proximosCursos: {
        required: true
    }
})

const cursos = computed(() => props.proximosCursos.map(c => ({ title: c.titulo, description: c.descripcion, date: c.fecha_inicio, url: route('evento', c.slug) })))

</script>
