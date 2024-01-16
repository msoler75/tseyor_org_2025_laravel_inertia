<template>
    <FullPage>

       <Section>
            <Hero title="Curso Holístico Tseyor"
                subtitle="Conoce las enseñanzas de los Guías de las Estrellas con nuestro curso gratuito."
                srcImage="https://via.placeholder.com/1600x1000.png/00088f?text=curso-holistico.jpg" image-right
                buttonLabel="Inscríbete" :href="route('cursos.inscripcion.nueva')"></Hero>
        </Section>

        <Section :style="{
            'background-size': 'cover',
            'background-attachment': 'fixed',
            background: 'black url(/almacen/medios/fondos/space.jpg) repeat'
        }">
            <VideoPlayer class="" videoUrl="https://www.youtube.com/embed/AkOZbfGdXbU" />
        </Section>


        <Section>
            <FeatureColumns :features="[
                {
                    title: 'Exploración Cósmica',
                    icon: 'ph:alien-duotone',
                    description: 'Explora los vínculos entre la humanidad y otras civilizaciones en el cosmos. Aprende sobre la Confederación de Mundos Habitados y su papel en la evolución de la humanidad.'
                },
                {
                    title: 'Desarrollo personal',
                    icon: 'ph:lightbulb-duotone',
                    description: 'Sumérgete en el camino del crecimiento personal y descubre herramientas prácticas para el desarrollo interior. Aprende a cultivar el equilibrio, fluir y la humildad en tu vida cotidiana.'
                },
                {
                    title: 'Viaje en la Nave Tseyor',
                    icon: 'ph:rocket-duotone',
                    description: 'Experimenta un fascinante viaje interdimensional a través de la Nave Tseyor. Descubre nuevas realidades y expande tu comprensión de la existencia mientras exploras otros mundos y dimensiones.'
                },
                {
                    title: 'Cuántica y Conciencia',
                    icon: 'ph:atom-duotone',
                    description: 'Descubre los fundamentos de la cuántica y su relación con la evolución de la humanidad. Explora las posibilidades de un próximo salto cuántico en nuestra conciencia y cómo podemos prepararnos para este cambio transformador.'
                }

            ]" />
        </Section>




        <Section>
            <TextImage title="Libro del Curso Holístico"
                srcImage="https://via.placeholder.com/400x600.png/0000ff?text=CURSO HOLISTICO" buttonLabel="Descargar Libro"
                href="/ong">
                <p>Lee este conjunto de historias para comprender la filosofía de Tseyor.</p>
            </TextImage>
        </Section>

        <Section>
            <FeaturedPosts title="Próximos cursos" :posts="cursos" />
        </Section>


    </FullPage>
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
