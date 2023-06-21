<template>
    <Sections>

        <Hero title="Curso Holístico Tseyor"
            subtitle="Conoce las enseñanzas de los Guías de las Estrellas con nuestro curso gratuito."
            srcImage="https://via.placeholder.com/1600x1000.png/00088f?text=curso-holistico.jpg" image-right
            buttonLabel="Inscríbete" href="/cursos"></Hero>

        <Section class="!bg-gray-900">
            <VideoPlayer videoUrl="https://www.youtube.com/embed/AkOZbfGdXbU" />
        </Section>


        <Section>
            <FeatureColumns :features="[
                {
                    title: 'Exploración Cósmica y Conexión',
                    icon: 'ph:alien-duotone',
                    description: 'Explora los vínculos entre la humanidad y otras civilizaciones en el cosmos. Aprende sobre la Confederación de Mundos Habitados y su papel en la evolución de la humanidad.'
                },
                {
                    title: 'Desarrollo personal y trabajo interior',
                    icon: 'ph:lightbulb-duotone',
                    description: 'Sumérgete en el camino del crecimiento personal y descubre herramientas prácticas para el desarrollo interior. Aprende a cultivar el equilibrio, fluir y la humildad en tu vida cotidiana.'
                },
                {
                    title: 'Viaje en la Nave Tseyor',
                    icon: 'ph:rocket-duotone',
                    description: 'Experimenta un fascinante viaje interdimensional a través de la Nave Tseyor. Descubre nuevas realidades y expande tu comprensión de la existencia mientras exploras otros mundos y dimensiones.'
                },
                {
                    title: 'Física Cuántica y Salto de Conciencia',
                    icon: 'ph:atom-duotone',
                    description: 'Descubre los fundamentos de la física cuántica y su relación con la evolución de la humanidad. Explora las posibilidades de un próximo salto cuántico en nuestra conciencia y cómo podemos prepararnos para este cambio transformador.'
                }

            ]" />
        </Section>





        <Section>
            <TextImage title="ONG Mundo Armónico Tseyor"
                srcImage="https://via.placeholder.com/700x600.png/0000ff?text=ONG.jpg" buttonLabel="Conoce la ONG"
                href="/ong">
                <p>Conoce nuestra ONG Mundo Armónico Tseyor, dedicada a la ayuda humanitaria en su vertiente
                    material y espiritual.</p>
            </TextImage>
        </Section>

    </Sections>
</template>



<script setup>

import AppLayout from '@/Layouts/AppLayout.vue'
import { Icon } from '@iconify/vue';
import { Link } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

</script>
