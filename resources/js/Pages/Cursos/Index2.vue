<template>
    <Sections height="500">

        <!-- 1. Hero: Curso Holístico Tseyor -->
        <Section class="py-12">
            <EvaluacionSeccion seccion="Hero curso" :idoneidad="95" :claridad="90" :composicion="85" :posicion="95" />
            <Hero
                title="Curso Holístico Tseyor"
                srcImage="/almacen/medios/paginas/curso.png"
                srcWidth="373" srcHeight="482"
                buttonLabel="Inscríbete gratis"
                :href="route('cursos.inscripcion.nueva')"
            >
                <p class="text-lg">Un curso <strong>gratuito</strong>, guiado y sin dogmas para conocer la filosofía de las estrellas que nos transmiten nuestros Guías Estelares.</p>
                <p class="text-lg">Se vive desde la retroalimentación y la experimentación personal. Al terminarlo recibirás tu <strong>nombre simbólico</strong>.</p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="badge badge-primary">100% gratuito</span>
                    <span class="badge badge-secondary">Sin dogmas</span>
                    <span class="badge badge-accent">Con acompañamiento</span>
                    <span class="badge badge-ghost">Nombre simbólico al terminar</span>
                </div>
                <Link href="/libros/curso-holistico-tseyor" class="btn btn-ghost btn-sm mt-2">Ver el material del curso</Link>
            </Hero>
        </Section>

        <!-- 2. Qué ofrece el curso -->
        <Section class="py-12">
            <EvaluacionSeccion seccion="Qué ofrece" :idoneidad="85" :claridad="90" :composicion="85" :posicion="85" />
            <div class="container mx-auto text-center space-y-6">
                <h2>Qué ofrece el curso</h2>
                <p class="max-w-2xl mx-auto">Exploración cósmica, desarrollo personal, conciencia cuántica y herramientas prácticas — todo desde la retroalimentación y la propia comprobación.</p>
                <FeatureColumns :features="[
                    {
                        title: 'Exploración Cósmica',
                        icon: 'ph:alien-duotone',
                        description: 'El papel de la Confederación de Mundos Habitados en nuestra evolución.',
                    },
                    {
                        title: 'Desarrollo personal',
                        icon: 'ph:lightbulb-duotone',
                        description: 'Herramientas prácticas para el autoconocimiento y la transformación interior.',
                    },
                    {
                        title: 'Conciencia cuántica',
                        icon: 'ph:atom-duotone',
                        description: 'Comprende cómo la conciencia moldea la realidad y el próximo salto cuántico.',
                    },
                    {
                        title: 'Retroalimentación',
                        icon: 'ph:arrows-clockwise-duotone',
                        description: 'Aprendizaje mutuo en comunidad, sin jerarquías ni dogmas.',
                    },
                ]" />
                <div class="flex justify-center gap-3">
                    <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary">Empezar el curso gratis</Link>
                    <Link :href="route('filosofia')" class="btn btn-secondary">Conocer la filosofía</Link>
                </div>
            </div>
        </Section>

        <!-- 3. Libro del Curso -->
        <Section class="py-12 container lg:max-w-[1024px]">
            <EvaluacionSeccion seccion="Libro del curso" :idoneidad="80" :claridad="85" :composicion="80" :posicion="75" />
            <TextImage title="Libro del Curso"
                textClass="container"
                buttonLabel="Descargar Libro" href="/libros/curso-holistico-tseyor">
                <template v-slot:image>
                    <Libro3d :libro="libro" alt="Curso Holístico Tseyor"/>
                </template>
                <p class="text-lg">El curso se basa en un libro de enseñanzas de nuestros Guías Estelares, con cuentos y reflexiones filosóficas.</p>
                <p class="text-sm text-gray-500 mt-2">Puedes leer el material antes o durante el curso. Descarga gratuita.</p>
                <div class="flex flex-wrap gap-3 mt-3">
                    <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-sm">Inscribirme al curso</Link>
                    <Link href="/libros/curso-holistico-tseyor" class="btn btn-ghost btn-sm">Descargar libro</Link>
                </div>
            </TextImage>
        </Section>

        <!-- 4. Próximos cursos y eventos -->
        <Section v-if="proximosCursos?.length" class="py-12 space-y-12">
            <EvaluacionSeccion seccion="Próximos cursos" :idoneidad="50" :claridad="60" :composicion="60" :posicion="65" />
            <FeaturedPosts v-if="proximosCursos.length" title="Próximos cursos" :posts="cursos" />
            <div class="container text-center">
                <Link :href="route('eventos')" class="btn btn-primary">Ver todos los eventos</Link>
            </div>
        </Section>

        <!-- 5. Inscripción final -->
        <Section>
            <EvaluacionSeccion seccion="CTA final inscripción" :idoneidad="90" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Comienza tu camino de autodescubrimiento"
                buttonLabel="Inscribirme gratuitamente"
                :href="route('cursos.inscripcion.nueva')"
                srcWidth="2432" srcHeight="1664" srcImage="/almacen/medios/paginas/inscribirse.jpg"
                class="py-20">
                <p>Sin compromiso, sin dogmas, sin coste. Solo tú y tu experiencia.</p>
                <div class="flex flex-wrap justify-center gap-3 mt-4">
                    <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-lg">Inscribirme gratis</Link>
                    <Link :href="route('biblioteca')" class="btn btn-secondary btn-sm">Explorar biblioteca</Link>
                </div>
            </Hero>
        </Section>

    </Sections>
</template>

<script setup>
import EvaluacionSeccion from "@/Components/EvaluacionSeccion.vue"

const props = defineProps({
    proximosCursos: {
        type: Array,
        default: () => [],
    },
    libro: {
        type: Object,
        required: true,
    },
})

const cursos = computed(() => props.proximosCursos.map(c => ({
    title: c.titulo,
    description: c.descripcion,
    date: c.fecha_inicio,
    url: route('evento', c.slug),
})))
</script>
