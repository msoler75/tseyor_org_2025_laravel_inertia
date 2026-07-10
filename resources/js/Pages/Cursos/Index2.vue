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
                <p class="text-lg">Aprende con nuestro curso gratuito la base de la filosofía de las estrellas, de parte de nuestros <Referencia>Guías Estelares</Referencia>.</p>
                <p class="text-lg">Al terminar el curso recibirás tu <Referencia>nombre simbólico</Referencia>.</p>
                <p class="text-lg">El curso se vive desde la retroalimentación y la experimentación personal, sin dogmas.</p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <span class="badge badge-primary">Gratuito</span>
                    <span class="badge badge-secondary">Sin dogmas</span>
                    <span class="badge badge-accent">Con acompañamiento</span>
                    <span class="badge badge-ghost">Nombre simbólico al terminar</span>
                </div>
                <Link href="/libros/curso-holistico-tseyor" class="btn btn-secondary btn-sm mt-2">Descargar libro del curso</Link>
            </Hero>
        </Section>

        <!-- 2. Vídeo de presentación -->
        <Section>
            <EvaluacionSeccion seccion="Vídeo presentación" :idoneidad="85" :claridad="85" :composicion="75" :posicion="85" />
            <div class="container mx-auto py-12 text-center space-y-6">
                <h2>Vídeo de presentación</h2>
                <p>Mira la presentación del Curso Holístico Tseyor y conoce su enfoque antes de inscribirte.</p>
                <FondoEstrellado class="w-full flex justify-center items-center py-12">
                    <VideoPlayer videoUrl="https://www.youtube.com/embed/AkOZbfGdXbU" />
                </FondoEstrellado>
                <p class="text-sm text-gray-500 italic">El curso acompaña tu exploración; no impone creencias.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary">Inscribirme gratis</Link>
            </div>
        </Section>

        <!-- 3. Qué vas a encontrar -->
        <Section class="py-12">
            <EvaluacionSeccion seccion="Qué vas a encontrar" :idoneidad="85" :claridad="90" :composicion="85" :posicion="85" />
            <div class="container mx-auto text-center space-y-6">
                <h2>Qué vas a encontrar</h2>
                <p class="max-w-2xl mx-auto">El curso reúne exploración cósmica, desarrollo personal, extrapolación mental, cuántica y conciencia. Cada tema se trabaja desde la retroalimentación y la propia comprobación.</p>
                <FeatureColumns :features="[
                    {
                        title: 'Exploración Cósmica',
                        icon: 'ph:alien-duotone',
                        description: 'Conoce el papel de la Confederación de Mundos Habitados de la Galaxia.',
                    },
                    {
                        title: 'Desarrollo personal',
                        icon: 'ph:lightbulb-duotone',
                        description: 'Descubre herramientas prácticas para el desarrollo interior.',
                    },
                    {
                        title: 'Extrapolación mental',
                        icon: 'ph:rocket-duotone',
                        description: 'Expande tu comprensión de la existencia de otros mundos.',
                    },
                    {
                        title: 'Cuántica y Conciencia',
                        icon: 'ph:atom-duotone',
                        description: 'Explora las posibilidades de un próximo salto cuántico.',
                    },
                ]" />
                <div class="flex flex-wrap justify-center gap-2">
                    <span class="badge badge-secondary text-xs">Sin dogmas</span>
                    <span class="badge badge-primary text-xs">Gratuito</span>
                </div>
                <div class="flex justify-center gap-3">
                    <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary">Empezar el curso</Link>
                    <Link :href="route('filosofia')" class="btn btn-secondary">Conocer la filosofía</Link>
                </div>
            </div>
        </Section>

        <!-- 4. Filosofía Cósmico-Crística -->
        <Section class="py-12">
            <EvaluacionSeccion seccion="Filosofía del curso" :idoneidad="75" :claridad="80" :composicion="70" :posicion="75" />
            <TextImage title="Filosofía Cósmico-Crística" buttonLabel="Conoce nuestra filosofía"
                :href="route('filosofia')" srcImage="/almacen/medios/paginas/hombre-espacio.jpg"
                image-right
                class="container lg:max-w-[1024px]">
                <p class="text-lg">El curso ha sido desarrollado en base a las enseñanzas de nuestros <Referencia>Guías Estelares</Referencia>.</p>
                <p class="text-lg">Funciona bajo el mecanismo de la retroalimentación que nos permite el aprendizaje mutuo, sin dogmas y respetando la propia comprobación.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-sm mt-2">Inscríbete</Link>
            </TextImage>
        </Section>

        <!-- 5. Testimonios del Curso Holístico -->
        <Section class="py-12">
            <EvaluacionSeccion seccion="Testimonios curso" :idoneidad="60" :claridad="65" :composicion="65" :posicion="70" />
            <div class="container mx-auto text-center space-y-6">
                <h2>Testimonios del Curso Holístico</h2>
                <p class="max-w-2xl mx-auto">Personas que han realizado el Curso Holístico comparten cómo vivieron el aprendizaje, la retroalimentación y su acercamiento a la filosofía Tseyor.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                    <div v-for="testimonio in testimonios" :key="testimonio.name" class="card bg-base-200 p-6 rounded-xl space-y-3 text-left">
                        <p class="italic text-sm">"{{ testimonio.text }}"</p>
                        <p class="font-bold text-sm">{{ testimonio.name }}</p>
                        <p class="text-xs text-gray-500">{{ testimonio.role }}</p>
                    </div>
                </div>
                <div class="flex justify-center gap-3">
                    <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary">Inscribirme gratis</Link>
                    <Link href="/libros/curso-holistico-tseyor" class="btn btn-secondary">Descargar libro del curso</Link>
                </div>
            </div>
        </Section>

        <!-- 6. Libro del Curso -->
        <Section class="py-12 container lg:max-w-[1024px]">
            <EvaluacionSeccion seccion="Libro del curso" :idoneidad="80" :claridad="85" :composicion="80" :posicion="75" />
            <TextImage title="Libro del Curso"
                textClass="container"
                buttonLabel="Descargar Libro" href="/libros/curso-holistico-tseyor">
                <template v-slot:image>
                    <Libro3d :libro="libro" alt="Curso Holístico Tseyor"/>
                </template>
                <p class="text-lg">Cuentos de nuestros <Referencia>Guías Estelares</Referencia> con enseñanzas filosóficas que ayudan enormemente a comprender la filosofía de Tseyor.</p>
                <p class="text-sm text-gray-500 mt-2">Puedes leer el material antes o durante el curso.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary btn-sm mt-2">Inscribirme al curso</Link>
            </TextImage>
        </Section>

        <!-- 7. Próximos cursos y eventos -->
        <Section v-if="proximosCursos?.length" class="py-12 space-y-12">
            <EvaluacionSeccion seccion="Próximos cursos" :idoneidad="50" :claridad="60" :composicion="60" :posicion="65" />
            <FeaturedPosts v-if="proximosCursos.length" title="Próximos cursos" :posts="cursos" />
            <div class="container text-center">
                <p>Consulta regularmente los <Link :href="route('eventos')">Eventos</Link> para estar informado de los próximos eventos de la comunidad Tseyor.</p>
                <p class="mt-2">También puedes acercarte al centro Tseyor más cercano y solicitar un curso para ti. Busca en <Link href="/contactos">el mapa</Link>.</p>
                <div class="flex justify-center gap-3 mt-4">
                    <Link :href="route('eventos')" class="btn btn-primary">Ver eventos</Link>
                    <Link href="/contactos" class="btn btn-secondary">Ver mapa</Link>
                </div>
            </div>
        </Section>

        <!-- 8. Inscripción final -->
        <Section>
            <EvaluacionSeccion seccion="CTA final inscripción" :idoneidad="90" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Te invitamos a recorrer con nosotros el camino del autodescubrimiento"
                buttonLabel="Inscribirme gratuitamente"
                :href="route('cursos.inscripcion.nueva')"
                srcWidth="2432" srcHeight="1664" srcImage="/almacen/medios/paginas/inscribirse.jpg"
                class="py-20">
                <p>Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.</p>
                <Link :href="route('biblioteca')" class="btn btn-secondary btn-sm mt-2">Explorar biblioteca antes</Link>
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

const testimonios = [
    {
        name: "Laura",
        role: "Curso Holístico",
        text: "Llegué con escepticismo, pero el curso me permitió explorar a mi ritmo. La retroalimentación con el grupo fue lo más valioso.",
    },
    {
        name: "David",
        role: "Nombre simbólico recibido",
        text: "Me sorprendió lo bien organizado que está. Cada tema me ayudó a conectar puntos que antes no veía. Y todo gratuito.",
    },
    {
        name: "Elena",
        role: "Participante online",
        text: "El curso se siente libre, respetuoso y sin dogmas. Pude cuestionar todo y sacar mis propias conclusiones.",
    },
]
</script>
