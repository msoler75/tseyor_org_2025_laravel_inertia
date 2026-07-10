<template>
    <div class="container flex justify-between mx-auto py-12">
        <Back href="/quienes-somos" :floatAtY="-100">Quiénes Somos</Back>
        <Share />
    </div>

    <Sections height="550" white-first>

        <h1 class="hidden">Filosofía Tseyor</h1>

        <!-- 1. Tiempos de transformación -->
        <Section>
            <EvaluacionSeccion seccion="Hero: Tiempos de transformación" :idoneidad="95" :claridad="90" :composicion="85" :posicion="95" />
            <Hero title="Tiempos de transformación" srcImage="/almacen/medios/paginas/hombre-universo.jpg">
                <p>Nos encontramos en un momento de transformación sin precedentes a nivel cósmico, que nos va a conducir a un nuevo nivel vibratorio, lo que significa mayor comprensión.</p>
                <p>La Confederación de Mundos Habitados de la Galaxia está tutelando al grupo Tseyor en un proceso de autodescubrimiento.</p>
                <p>Un proceso que llevará a la humanidad a conectarse con sus otras realidades interdimensionales.</p>
                <p>Este proceso se propone desde una filosofía viva, no dogmática, abierta a la propia comprobación.</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <Link :href="route('cursos')" class="btn btn-primary">Curso gratuito</Link>
                    <Link :href="route('biblioteca')" class="btn btn-secondary">Explorar biblioteca</Link>
                </div>
            </Hero>
        </Section>

        <!-- 2. Interesa la comprobación -->
        <Section>
            <EvaluacionSeccion seccion="Interesa la comprobación" :idoneidad="90" :claridad="95" :composicion="80" :posicion="90" />
            <TextImage title="Interesa la comprobación" srcImage="/almacen/medios/paginas/hombre-con-lupa.jpg"
                :href="route('libros', { slug: 'filosofia' })" buttonLabel="Leer filosofía" image-right>
                <p>No habremos de creer nada; en lugar de eso comprobaremos mediante la experimentación.</p>
                <p>Solo la propia comprobación nos dará la convicción y la certeza de que estamos en una transformación de nuestras impresiones, contemplando las realidades que están aquí y ahora.</p>
                <Link :href="route('cursos')" class="btn btn-secondary btn-sm mt-2">Empezar el curso</Link>
            </TextImage>
        </Section>

        <!-- 3. El proceso de autodescubrimiento -->
        <Section>
            <EvaluacionSeccion seccion="Proceso de autodescubrimiento" :idoneidad="85" :claridad="85" :composicion="80" :posicion="85" />
            <TextImage
                title="El proceso de autodescubrimiento"
                :href="route('cursos')"
                buttonLabel="Curso gratuito"
                srcImage="/almacen/medios/portada/meditando.jpg"
                src-width="1920" src-height="1080"
                class="py-20"
            >
                <p>A través de meditaciones, talleres y un gran sentimiento de hermanamiento conseguimos transmutar nuestra personalidad hasta lograr la Unidad.</p>
                <Link :href="route('meditaciones')" class="btn btn-secondary btn-sm mt-2">Ver meditaciones</Link>
            </TextImage>
        </Section>

        <!-- 4. Una puerta abierta al infinito -->
        <Section>
            <EvaluacionSeccion seccion="Puerta al infinito" :idoneidad="80" :claridad="75" :composicion="75" :posicion="80" />
            <Hero title="Una puerta abierta al infinito" srcImage="/almacen/medios/paginas/cristo-cosmico-replica.jpg"
                image-right src-width="600" src-height="400">
                <p>Aprendamos a conectar con nuestra chispa divina, con la inestimable ayuda del Cristo-Cósmico, a través de nuestro amigo el pequeño Christian.</p>
                <p>Y descubriremos en nosotros nuestro universo interior, hallando la verdadera felicidad y la gran Verdad que siempre estuvo ahí.</p>
                <Link :href="route('biblioteca')" class="btn btn-primary btn-sm mt-2">Leer más en biblioteca</Link>
                <Link href="/glosario" class="btn btn-secondary btn-sm mt-2">Ver glosario</Link>
            </Hero>
        </Section>

        <!-- 5. Filosofía cuántica -->
        <Section>
            <EvaluacionSeccion seccion="Filosofía cuántica" :idoneidad="80" :claridad="75" :composicion="75" :posicion="75" />
            <TextImage title="Filosofía cuántica" srcImage="/almacen/medios/paginas/hombre-comprension.jpg"
                :href="route('libros', { slug: 'filosofia' })" buttonLabel="Leer libro de filosofía" image-right
                class="py-10">
                <p>La realidad cuántica significa creación a través del pensamiento.</p>
                <p>Necesitamos claves, referencias, dispositivos, que nos permitan ese lanzamiento hacia las estrellas, que en el fondo no es más que el reencuentro con uno mismo, a través de uno mismo en su universo interior.</p>
                <Link href="/glosario" class="btn btn-secondary btn-sm mt-2">Consultar glosario</Link>
            </TextImage>
        </Section>

        <!-- 6. Las Sociedades Armónicas -->
        <Section>
            <EvaluacionSeccion seccion="Sociedades Armónicas" :idoneidad="85" :claridad="80" :composicion="80" :posicion="80" />
            <TextImage title="Las sociedades armónicas" srcImage="/almacen/medios/paginas/SociedadesArmonicas.jpg"
                buttonLabel="Ver en Biblioteca" :href="route('libros', { slug: 'las-sociedades-armonicas' })"
                src-width="1920" src-height="1282"
                class="py-20">
                <p>Son sociedades en las que predomina la hermandad, no hay líderes, no hay enfermedad, y sí la auténtica libertad.</p>
                <p>A nuestro planeta le ha llegado el momento de transformarse de forma pacífica y con mucho amor y paciencia materializar las Sociedades Armónicas.</p>
                <div class="flex flex-wrap gap-4 mt-4">
                    <div class="flex items-center gap-2">
                        <Icon icon="ph:heart-duotone" class="text-xl text-red-400" />
                        <span class="text-sm">Hermandad</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Icon icon="ph:link-duotone" class="text-xl text-blue-400" />
                        <span class="text-sm">Auténtica libertad</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Icon icon="ph:x-circle-duotone" class="text-xl text-gray-400" />
                        <span class="text-sm">Sin líderes</span>
                    </div>
                </div>
                <Link :href="route('quienes-somos')" class="btn btn-secondary btn-sm mt-2">Quiénes somos</Link>
            </TextImage>
        </Section>

        <!-- 7. Glosario -->
        <Section>
            <EvaluacionSeccion seccion="Glosario" :idoneidad="75" :claridad="80" :composicion="70" :posicion="75" />
            <TextImage title="Glosario" buttonLabel="Ir al Glosario" src-width="600" src-height="400" href="/glosario"
                srcImage="/almacen/medios/paginas/glosario.jpg" image-right
                class="py-20">
                <p>Consulta todos los términos de la filosofía Tseyor en nuestro glosario de términos. También podrás ver los Guías Estelares y las preguntas frecuentes.</p>
                <div class="flex flex-wrap gap-2 mt-4">
                    <Link href="/guias" class="badge badge-primary">Guías Estelares</Link>
                    <Link href="/preguntas-frecuentes" class="badge badge-secondary">Preguntas frecuentes</Link>
                </div>
            </TextImage>
        </Section>

        <!-- 8. Únete a Tseyor -->
        <Section>
            <EvaluacionSeccion seccion="CTA final" :idoneidad="85" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Únete a Tseyor" buttonLabel="Curso Holístico"
                :href="route('cursos')" textClass="space-y-8">
                <p>Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.</p>
                <p>Juntos, exploraremos nuestra esencia y buscaremos trascender las limitaciones de esta realidad ilusoria.</p>
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
