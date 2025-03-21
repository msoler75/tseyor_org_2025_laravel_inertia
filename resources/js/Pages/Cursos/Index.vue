<template>
    <Sections height="500">

        <Section  class="py-12 container lg:max-w-[1024px]">
            <TextImage title="Curso Holístico Tseyor" srcImage="/almacen/medios/paginas/curso.png" image-right
                srcWidth="373" srcHeight="482" buttonLabel="Inscríbete" :href="route('cursos.inscripcion.nueva')">
                <p class="text-lg">Aprende con nuestro curso gratuito la base de la filosofía de las estrellas, de parte de nuestros <Referencia>Guías Estelares</Referencia>.</p>
                <p class="text-lg">Al terminar el curso recibirás tu <Referencia>nombre simbólico</Referencia>.</p>
            </TextImage>
        </Section>

        <Section>
            <FondoEstrellado class="w-full h-full flex justify-center items-center py-12">
                <VideoPlayer class="" videoUrl="https://www.youtube.com/embed/AkOZbfGdXbU" />
            </FondoEstrellado>
        </Section>


        <Section class="py-12">
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

        <Section  class="py-12">
            <TextImage title="Filosofía Cósmico-Crística" buttonLabel="conoce nuestra filosofía"
                :href="route('filosofia')" srcImage="/almacen/medios/paginas/hombre-espacio.jpg" textClass=""
                image-right
                class="container lg:max-w-[1024px]">
                <p class="text-lg">El curso ha sido desarrollado en base a las enseñanzas de nuestros <Referencia>Guías Estelares
                    </Referencia>
                </p>
                <p class="text-lg">Funciona bajo el mecanismo de la retroalimentación que nos permite el aprendizaje mútuo.</p>
            </TextImage>
        </Section>

        <Section class="py-12 container lg:max-w-[1024px]">
            <TextImage title="Libro del Curso"
                textClass="container"
                buttonLabel="Descargar Libro" href="/libros/curso-holistico-tseyor">
                <template v-slot:image>
                    <Libro3d :libro="libro" alt="Curso Holístico Tseyor"/>
                </template>
                <p class="text-lg">Cuentos de nuestros <Referencia>Guías Estelares</Referencia> con enseñanzas filosóficas que ayudan enormemente a comprender la filosofía de Tseyor.</p>
            </TextImage>
        </Section>

        <Section v-if="false" class="py-12 space-y-12">
            <FeaturedPosts v-if="cursos.length" title="Próximos cursos" :posts="cursos" />
            <div v-else class="container">
                <h2>Próximos Cursos</h2>
                <p>No hay cursos programados.</p>
            </div>
            <div class="container">
                <p>Consulta regularmente los
                    <Link :href="route('eventos')">Eventos</Link> para estar informado de los próximos eventos de la
                    comunidad Tseyor.
                </p>
            </div>
            <div class="container">
                <p>También puedes acercarte al centro Tseyor más cercano y solicitar un curso para ti. Busca en
                    <Link href="donde-estamos">
                    el mapa</Link>
                </p>.
            </div>
        </Section>


    </Sections>
</template>



<script setup>



const props = defineProps({
    proximosCursos: {
        required: true
    },
    libro: {type: Object,
        required: true
    }
})

const cursos = computed(() => props.proximosCursos.map(c => ({ title: c.titulo, description: c.descripcion, date: c.fecha_inicio, url: route('evento', c.slug) })))

</script>
