<template>
    <div class="container flex justify-between mx-auto py-12">
        <Back href="/quienes-somos" :floatAtY="-100">Quiénes Somos</Back>
        <Share />
    </div>

    <Sections height="550" white-first>

        <h1 class="hidden">Filosofía Tseyor</h1>

        <!-- 1. Hero: Filosofía de las estrellas -->
        <Section>
            <EvaluacionSeccion seccion="Hero: Filosofía de las estrellas" :idoneidad="95" :claridad="90" :composicion="85" :posicion="95" />
            <Hero title="Filosofía de las estrellas" srcImage="/almacen/medios/paginas/hombre-universo.jpg">
                <p>Nuestra filosofía no es una invención humana. Proviene de la <strong>Confederación de Mundos Habitados de la Galaxia</strong>, transmitida a través de comunicaciones telepáticas con nuestros Guías Estelares.</p>
                <p>Es una filosofía viva, no dogmática: se comprueba mediante la experimentación personal, no mediante la creencia. Aborda la conciencia, el autodescubrimiento y la creación de Sociedades Armónicas.</p>
                <p>En un momento de transformación cósmica sin precedentes, recibimos estas enseñanzas para guiarnos hacia un nuevo nivel vibratorio y de comprensión.</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <Link :href="route('guias')" class="btn btn-primary">Conocer a los Guías Estelares</Link>
                    <Link :href="route('biblioteca')" class="btn btn-secondary">Explorar biblioteca</Link>
                </div>
            </Hero>
        </Section>

        <!-- 2. Sociedades Armónicas -->
        <Section>
            <EvaluacionSeccion seccion="Sociedades Armónicas" :idoneidad="95" :claridad="90" :composicion="85" :posicion="95" />
            <Hero title="Sociedades Armónicas" srcImage="/almacen/medios/paginas/sociedades-armonicas.jpg"
                cover textClass="container">
                <p>El horizonte hacia el que caminamos. Sociedades donde predomina la <strong>hermandad real</strong>, sin líderes ni jerarquías, sin enfermedad, con auténtica libertad.</p>
                <p>No es una utopía: es un modelo de convivencia basado en la cooperación, el respeto a la naturaleza y la evolución consciente de cada individuo.</p>
                <p>Las Sociedades Armónicas son el destino natural del autodescubrimiento colectivo. Pequeños núcleos que, desde el servicio y la fraternidad, inspirarán el cambio global.</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <Link :href="route('descubre')" class="btn btn-primary">Explorar temas clave</Link>
                    <Link :href="route('cursos')" class="btn btn-secondary">Comenzar el curso</Link>
                </div>
            </Hero>
        </Section>

        <!-- 3. Conciencia y autodescubrimiento -->
        <Section>
            <EvaluacionSeccion seccion="Conciencia y autodescubrimiento" :idoneidad="85" :claridad="85" :composicion="80" :posicion="85" />
            <TextImage title="Conciencia y autodescubrimiento"
                srcImage="/almacen/medios/portada/meditando.jpg"
                src-width="1920" src-height="1080"
                buttonLabel="Curso gratuito" :href="route('cursos')"
                class="py-20">
                <p>La conciencia no está en el cerebro — el cerebro es un receptor. Nuestra verdadera identidad es interdimensional y participa activamente en la creación de la realidad.</p>
                <p>A través de la meditación, los talleres vivenciales y el sentimiento de hermanamiento, transmutamos nuestra personalidad hasta lograr la Unidad. Este es el camino del autodescubrimiento.</p>
                <Link :href="route('meditaciones')" class="btn btn-secondary btn-sm mt-2">Ver meditaciones</Link>
            </TextImage>
        </Section>

        <!-- 4. La comprobación como método -->
        <Section>
            <EvaluacionSeccion seccion="La comprobación" :idoneidad="90" :claridad="95" :composicion="80" :posicion="90" />
            <TextImage title="La comprobación como método" srcImage="/almacen/medios/paginas/hombre-con-lupa.jpg"
                :href="route('libros', { slug: 'filosofia' })" buttonLabel="Leer libro de filosofía" image-right>
                <p><strong>No creer, comprobar.</strong> Este es el pilar fundamental. No pedimos fe ni adhesión dogmática — invitamos a experimentar por uno mismo.</p>
                <p>Solo la propia comprobación dará la convicción y la certeza de que estamos en una transformación de nuestras impresiones, contemplando las realidades que están aquí y ahora.</p>
                <p>Cada persona avanza a su ritmo, desde su propia experiencia, en retroalimentación con la comunidad.</p>
                <Link :href="route('cursos')" class="btn btn-secondary btn-sm mt-2">Empezar el curso gratuito</Link>
            </TextImage>
        </Section>

        <!-- 5. CTA final -->
        <Section>
            <EvaluacionSeccion seccion="CTA final" :idoneidad="85" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Únete a Tseyor" buttonLabel="Curso Holístico"
                :href="route('cursos')" textClass="space-y-8">
                <p>Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.</p>
                <p>Puedes empezar sin dogmas, desde tu propia experiencia y en retroalimentación con la comunidad.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary">Inscríbete</Link>
                    <Link :href="route('biblioteca')" class="btn btn-secondary">Explorar biblioteca</Link>
                </div>
            </Hero>
        </Section>

    </Sections>
</template>

<script setup>
import EvaluacionSeccion from "@/Components/EvaluacionSeccion.vue"
import { usePage } from "@inertiajs/vue3";
const page = usePage()
</script>
