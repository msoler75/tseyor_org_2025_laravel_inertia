<template>
    <Sections>
    <h1 class="hidden">Hacia una nueva consciencia</h1>

        <!-- 1. Hero -->
        <Section>
            <EvaluacionSeccion seccion="Hero principal" :idoneidad="95" :claridad="85" :composicion="80" :posicion="95" />
            <Hero
                title="Hacia una nueva consciencia"
                textClass="md:max-w-(--breakpoint-md) justify-center"
                class="h-[95vh] w-[96vw]"
            >
                <div class="bg-white/20 hover:bg-white/50 transition duration-500 p-7 rounded-xl space-y-4">
                    <p>TSEYOR es una ONG dedicada al despertar de la consciencia y la creación de las Sociedades Armónicas.</p>
                    <p>Nuestros Guías Estelares nos tutelan en este proceso de transformación interior.</p>
                    <p class="text-sm text-gray-400 italic">Sin obligación de creer. Un camino de experimentación personal, retroalimentación y libertad interior.</p>
                </div>
                <div class="w-full flex flex-wrap justify-center mx-auto mt-4 gap-3">
                    <Link href="/cursos" class="btn btn-primary text-xs">
                        <Icon icon="ph:graduation-cap-duotone" class="transform scale-150" />Comenzar el Curso Holístico
                    </Link>
                    <Link href="/biblioteca" class="btn btn-secondary text-xs">
                        <Icon icon="ph:books-duotone" class="transform scale-150" />Explorar la biblioteca
                    </Link>
                    <Link v-if="hayProximosEventos" href="eventos" class="btn btn-accent text-xs">
                        <Icon icon="ph:calendar-duotone" class="transform scale-150" />Próximos eventos
                    </Link>
                </div>
            </Hero>
        </Section>

        <!-- 2. El mensaje desde las estrellas -->
        <Section>
            <EvaluacionSeccion seccion="El mensaje desde las estrellas" :idoneidad="90" :claridad="85" :composicion="75" :posicion="90" />
            <Hero
                title="El mensaje desde las estrellas"
                cover
                src-image="/almacen/medios/paginas/todos-los-guias.jpg"
                src-width="2004"
                src-height="1639"
                textClass="container"
                image-class="h-full"
            >
                <p>Nuestra filosofía proviene de los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia, integrando ciencia, espiritualidad y experimentación personal.</p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <Link href="/guias" class="btn btn-primary text-xs">
                        <Icon icon="ph:user-gear-duotone" class="transform scale-150" />Conocer a los Guías Estelares
                    </Link>
                    <Link href="/comunicados" class="btn btn-secondary text-xs">
                        <Icon icon="ph:flying-saucer-duotone" class="transform scale-150" />Leer comunicados
                    </Link>
                </div>
            </Hero>
        </Section>

        <!-- 3. ONG y comunidad -->
        <Section>
            <EvaluacionSeccion seccion="ONG y comunidad" :idoneidad="90" :claridad="85" :composicion="80" :posicion="85" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">Una ONG de ayuda humanitaria</h2>
                <p class="text-center text-gray-500 mb-4 max-w-2xl mx-auto">Mundo Armónico TSEYOR es una organización sin ánimo de lucro legalmente constituida, dedicada a la ayuda humanitaria, el desarrollo sostenible y el despertar de la consciencia.</p>
                <div class="flex flex-wrap justify-center gap-3 mb-10">
                    <span class="badge badge-primary">ONG sin ánimo de lucro</span>
                    <span class="badge badge-secondary">Ayuda humanitaria</span>
                    <span class="badge badge-accent">Formación cultural</span>
                    <span class="badge badge-ghost">Cooperación al desarrollo</span>
                    <span class="badge badge-neutral">Conocimiento libre</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <div class="card bg-base-200 p-6 rounded-xl text-center space-y-3 md:col-span-2 md:row-span-1">
                        <Icon icon="ph:hand-heart-duotone" class="text-5xl text-primary mx-auto" />
                        <h3 class="font-bold text-lg">Mundo Armónico TSEYOR</h3>
                        <p class="text-sm">ONG legalmente constituida que canaliza ayuda humanitaria: alimentación, salud, formación cultural y cooperación al desarrollo en comunidades necesitadas. Todo ello desde el servicio desinteresado y el conocimiento libre.</p>
                        <div class="flex flex-wrap justify-center gap-2 mt-2">
                            <span class="badge badge-outline badge-sm">Alimentación</span>
                            <span class="badge badge-outline badge-sm">Salud</span>
                            <span class="badge badge-outline badge-sm">Formación</span>
                            <span class="badge badge-outline badge-sm">Cooperación</span>
                            <span class="badge badge-outline badge-sm">Sostenibilidad</span>
                        </div>
                        <Link href="/ong" class="btn btn-primary btn-sm mt-2">Conoce la ONG y sus proyectos</Link>
                    </div>
                    <div class="card bg-base-200 p-6 rounded-xl text-center space-y-3">
                        <Icon icon="ph:users-three-duotone" class="text-4xl text-accent mx-auto" />
                        <h3 class="font-bold">Comunidad de miembros</h3>
                        <p class="text-sm">Personas comprometidas con el servicio, el autodescubrimiento y la creación de Sociedades Armónicas.</p>
                        <Link href="/quienes-somos" class="btn btn-accent btn-sm">Conócenos</Link>
                    </div>
                    <div class="card bg-base-200 p-6 rounded-xl text-center space-y-3">
                        <Icon icon="ph:books-duotone" class="text-4xl text-secondary mx-auto" />
                        <h3 class="font-bold">Contenido libre y gratuito</h3>
                        <p class="text-sm">Libros, comunicados, audios, meditaciones y vídeos de acceso gratuito para todos.</p>
                        <Link href="/biblioteca" class="btn btn-secondary btn-sm">Explorar biblioteca</Link>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 4. Descubre (tarjetas de las páginas "Descubre") -->
        <Section>
            <EvaluacionSeccion seccion="Descubre" :idoneidad="85" :claridad="80" :composicion="75" :posicion="80" />
            <div class="container mx-auto py-16 px-4 overflow-hidden">
                <h2 class="text-center mb-4">Descubre TSEYOR</h2>
                <p class="text-center text-gray-500 mb-10 max-w-2xl mx-auto">Explora los temas fundamentales que articulan el conocimiento y la práctica TSEYOR.</p>
                <GridFill col-width="16rem" class="gap-6">
                    <CardContent v-for="pagina in paginasDescubre" :key="pagina.ruta"
                        :href="pagina.ruta"
                        :title="pagina.titulo"
                        :image="pagina.imagen"
                        imageHeight="194" />
                </GridFill>
                <div class="text-center mt-8">
                    <Link href="/descubre" class="btn btn-primary">Ver todos los temas</Link>
                </div>
            </div>
        </Section>

        <!-- 5. Por dónde empezar (curso + biblioteca + blog + eventos) -->
        <Section>
            <EvaluacionSeccion seccion="Por dónde empezar" :idoneidad="85" :claridad="90" :composicion="85" :posicion="85" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">¿Por dónde empezar?</h2>
                <p class="text-center text-gray-500 mb-10">Elige tu puerta de entrada al mundo TSEYOR.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
                    <Link href="/cursos" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:graduation-cap-duotone" class="text-4xl text-primary" />
                        <h3 class="font-bold">Curso Holístico</h3>
                        <p class="text-sm">Guía inicial gratuita para ordenar el conocimiento TSEYOR.</p>
                        <span class="btn btn-primary btn-sm mt-auto">Comenzar</span>
                    </Link>
                    <Link href="/biblioteca" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:books-duotone" class="text-4xl text-secondary" />
                        <h3 class="font-bold">Biblioteca</h3>
                        <p class="text-sm">Accede libremente a todos los materiales.</p>
                        <span class="btn btn-secondary btn-sm mt-auto">Explorar</span>
                    </Link>
                    <Link href="/blog" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:pencil-line-duotone" class="text-4xl text-accent" />
                        <h3 class="font-bold">Blog</h3>
                        <p class="text-sm">Vivencias y experiencias de la comunidad.</p>
                        <span class="btn btn-accent btn-sm mt-auto">Leer</span>
                    </Link>
                    <Link v-if="hayProximosEventos" href="/eventos" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:calendar-duotone" class="text-4xl text-warning" />
                        <h3 class="font-bold">Eventos</h3>
                        <p class="text-sm">Encuentros, cursos y convivencias.</p>
                        <span class="btn btn-warning btn-sm mt-auto">Ver</span>
                    </Link>
                </div>
            </div>
        </Section>

        <!-- 6. Vida de la comunidad (blog + novedades + boletín) -->
        <Section>
            <EvaluacionSeccion seccion="Vida de la comunidad" :idoneidad="80" :claridad="85" :composicion="80" :posicion="75" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">Vida de la comunidad</h2>
                <p class="text-center text-gray-500 mb-10 max-w-2xl mx-auto">Novedades, artículos del blog y boletín informativo de TSEYOR.</p>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <div class="space-y-4 lg:col-span-2">
                        <h3 class="font-bold text-lg">Últimas novedades</h3>
                        <div v-if="novedadesRecientes?.length" class="space-y-2">
                            <Link v-for="novedad in novedadesRecientes.slice(0, 3)" :key="novedad.id" :href="novedad.ruta"
                                class="block p-3 bg-base-200 rounded-lg hover:bg-base-300 transition">
                                <p class="font-medium text-sm">{{ novedad.titulo }}</p>
                                <p class="text-xs text-gray-500">{{ novedad.fecha }}</p>
                            </Link>
                        </div>
                        <div v-if="entradasRecientes?.length" class="space-y-3 mt-6">
                            <h3 class="font-bold text-lg">Blog</h3>
                            <Link v-for="entrada in entradasRecientes.slice(0, 2)" :key="entrada.id" :href="entrada.ruta"
                                class="block p-3 bg-base-200 rounded-lg hover:bg-base-300 transition">
                                <p class="font-medium text-sm">{{ entrada.titulo }}</p>
                                <p class="text-xs text-gray-500">{{ entrada.fecha }}</p>
                            </Link>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-4">
                            <Link href="/novedades" class="btn btn-primary btn-sm">Ver novedades</Link>
                            <Link href="/blog" class="btn btn-secondary btn-sm">Leer blog</Link>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="font-bold text-lg">Boletín</h3>
                        <p class="text-sm text-gray-500">Recibe las últimas novedades de TSEYOR en tu correo.</p>
                        <Suscribe />
                    </div>
                </div>
            </div>
        </Section>

        <!-- 7. Preguntas frecuentes -->
        <Section>
            <EvaluacionSeccion seccion="Preguntas frecuentes" :idoneidad="85" :claridad="90" :composicion="85" :posicion="85" />
            <div class="container mx-auto py-16 px-4 max-w-4xl">
                <h2 class="text-center mb-4">Preguntas frecuentes</h2>
                <p class="text-center text-gray-500 mb-10 max-w-2xl mx-auto">Las dudas más comunes sobre TSEYOR, los Guías Estelares y el Salto Cuántico.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                        <h3 class="font-bold text-sm">Sobre TSEYOR</h3>
                        <ul class="text-sm space-y-2">
                            <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Qué es el grupo Tseyor?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Es esto una secta?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="link link-hover">¿Qué significa la palabra Tseyor?</Link></li>
                        </ul>
                        <Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="btn btn-ghost btn-xs">Ver más →</Link>
                    </div>
                    <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                        <h3 class="font-bold text-sm">Guías Estelares</h3>
                        <ul class="text-sm space-y-2">
                            <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Cómo contactan con nosotros?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Por qué están aquí?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="link link-hover">¿Por qué no se presentan?</Link></li>
                        </ul>
                        <Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="btn btn-ghost btn-xs">Ver más →</Link>
                    </div>
                    <div class="card bg-base-200 p-5 rounded-xl space-y-3">
                        <h3 class="font-bold text-sm">Salto Cuántico</h3>
                        <ul class="text-sm space-y-2">
                            <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿En qué consiste el cambio?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿Cómo nos afectará?</Link></li>
                            <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="link link-hover">¿Podemos prepararnos?</Link></li>
                        </ul>
                        <Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="btn btn-ghost btn-xs">Ver más →</Link>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 8. CTA final -->
        <Section>
            <EvaluacionSeccion seccion="CTA final" :idoneidad="85" :claridad="90" :composicion="80" :posicion="85" />
            <Hero title="Comienza tu viaje" textClass="space-y-6">
                <p>El camino del autodescubrimiento está abierto para ti. Sin dogmas, desde tu propia experiencia.</p>
                <div class="flex flex-wrap justify-center gap-3">
                    <Link href="/cursos/inscripcion/nueva" class="btn btn-primary">Inscríbete al curso gratuito</Link>
                    <Link href="/biblioteca" class="btn btn-secondary">Explorar contenidos libres</Link>
                </div>
            </Hero>
        </Section>

    </Sections>
</template>

<script setup>
import EvaluacionSeccion from "@/Components/EvaluacionSeccion.vue"
import CardContent from "@/Components/CardContent.vue"
import GridFill from "@/Components/GridFill.vue"

defineProps({
    hayProximosEventos: Boolean,
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
    novedadesRecientes: {
        type: Array,
        default: () => [],
    },
    paginasDescubre: {
        type: Array,
        default: () => [],
    },
})
</script>

<style scoped>
@reference "../../css/app.css";
</style>
