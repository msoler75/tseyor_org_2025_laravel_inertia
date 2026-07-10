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

        <!-- 3. Conocimiento libre, ONG y comunidad -->
        <Section>
            <EvaluacionSeccion seccion="Conocimiento libre, ONG y comunidad" :idoneidad="85" :claridad="80" :composicion="70" :posicion="85" />
            <div class="container mx-auto py-16 px-4">
                <div class="flex flex-wrap justify-center gap-3 mb-8">
                    <span class="badge badge-primary text-xs">ONG sin ánimo de lucro</span>
                    <span class="badge badge-secondary text-xs">Contenidos de libre descarga</span>
                    <span class="badge badge-accent text-xs">Comunidad de miembros</span>
                    <span class="badge badge-ghost text-xs">Sin dogmas</span>
                </div>
                <div class="max-w-3xl mx-auto text-center space-y-4">
                    <p class="text-lg">Los contenidos de TSEYOR, incluyendo libros, comunicados, audios y meditaciones, son de libre acceso y descarga.</p>
                    <p class="text-lg">Este conocimiento se comparte desde el servicio y la ayuda humanitaria, en sus vertientes material y espiritual.</p>
                    <p class="text-lg">La comunidad está conformada por miembros de la ONG Mundo Armónico TSEYOR.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3 mt-8">
                    <Link href="/biblioteca" class="btn btn-primary">
                        <Icon icon="ph:books-duotone" />Explorar contenidos libres
                    </Link>
                    <Link href="/ong" class="btn btn-secondary">
                        <Icon icon="ph:handshake-duotone" />Conoce la ONG
                    </Link>
                </div>
            </div>
        </Section>

        <!-- 4. Por dónde empezar -->
        <Section>
            <EvaluacionSeccion seccion="Por dónde empezar" :idoneidad="80" :claridad="90" :composicion="85" :posicion="80" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-12">Por dónde empezar</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
                    <Link href="/cursos" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:graduation-cap-duotone" class="text-4xl text-primary" />
                        <h3 class="font-bold">Curso Holístico Tseyor</h3>
                        <p class="text-sm">Una primera guía para comprender el conocimiento TSEYOR y empezar tu proceso.</p>
                        <span class="btn btn-primary btn-sm mt-auto">Ver curso</span>
                    </Link>
                    <Link href="/biblioteca" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:books-duotone" class="text-4xl text-secondary" />
                        <h3 class="font-bold">Biblioteca Tseyor</h3>
                        <p class="text-sm">Accede libremente a libros, comunicados, audios, meditaciones y materiales.</p>
                        <span class="btn btn-secondary btn-sm mt-auto">Entrar en biblioteca</span>
                    </Link>
                    <Link href="/blog" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:pencil-line-duotone" class="text-4xl text-accent" />
                        <h3 class="font-bold">Blog</h3>
                        <p class="text-sm">Conócenos un poco más a través de nuestras vivencias.</p>
                        <span class="btn btn-accent btn-sm mt-auto">Leer blog</span>
                    </Link>
                    <Link v-if="hayProximosEventos" href="/eventos" class="card bg-base-200 hover:bg-base-300 transition p-6 rounded-xl flex flex-col items-center text-center gap-3">
                        <Icon icon="ph:calendar-duotone" class="text-4xl text-warning" />
                        <h3 class="font-bold">Próximos eventos</h3>
                        <p class="text-sm">Consulta los próximos encuentros, cursos y convivencias.</p>
                        <span class="btn btn-warning btn-sm mt-auto">Ver eventos</span>
                    </Link>
                </div>
            </div>
        </Section>

        <!-- 5. Curso Holístico y Biblioteca TSEYOR -->
        <Section>
            <EvaluacionSeccion seccion="Curso y Biblioteca (detalle)" :idoneidad="85" :claridad="80" :composicion="75" :posicion="70" />
            <div class="container mx-auto py-16 px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-5xl mx-auto">
                    <div class="space-y-4">
                        <h2 class="text-3xl font-bold">Curso Holístico Tseyor</h2>
                        <p>El Curso Holístico ayuda a ordenar los fundamentos del conocimiento TSEYOR para quienes se acercan por primera vez.</p>
                        <p>Puedes realizarlo online, de forma gratuita y con acompañamiento.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="badge badge-primary">Gratuito</span>
                            <span class="badge badge-secondary">Sin dogmas</span>
                            <span class="badge badge-accent">Con retroalimentación</span>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-4">
                            <Link href="/inscripcion" class="btn btn-primary">
                                <Icon icon="ph:note-pencil-duotone" />Inscríbete gratis
                            </Link>
                            <Link href="/cursos" class="btn btn-ghost">
                                Ver detalles del curso
                            </Link>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-3xl font-bold">Biblioteca TSEYOR</h2>
                        <p>La Biblioteca TSEYOR reúne libros, comunicados, audios, meditaciones y materiales de libre acceso y descarga.</p>
                        <div class="flex flex-wrap gap-2">
                            <span class="badge badge-primary">Libre descarga</span>
                            <span class="badge badge-secondary">Libros PDF</span>
                            <span class="badge badge-accent">Audios</span>
                        </div>
                        <div class="flex flex-wrap gap-3 mt-4">
                            <Link href="/biblioteca" class="btn btn-secondary">
                                <Icon icon="ph:books-duotone" />Explorar biblioteca
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 6. Temas clave de TSEYOR -->
        <Section>
            <EvaluacionSeccion seccion="Temas clave de TSEYOR" :idoneidad="80" :claridad="75" :composicion="70" :posicion="75" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">Temas clave de TSEYOR</h2>
                <p class="text-center text-gray-500 mb-12 max-w-2xl mx-auto">Presentación de algunos temas clave de TSEYOR para una mayor comprensión y claridad.</p>
                <div v-if="temasClave?.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <Link v-for="tema in temasClave.slice(0, 6)" :key="tema.id" :href="tema.ruta"
                        class="card bg-base-200 hover:bg-base-300 transition rounded-xl overflow-hidden">
                        <figure v-if="tema.imagen" class="h-40 overflow-hidden">
                            <img :src="tema.imagen" :alt="tema.titulo" class="w-full h-full object-cover" />
                        </figure>
                        <div class="p-4 space-y-2">
                            <h3 class="font-bold">{{ tema.titulo }}</h3>
                            <p class="text-sm line-clamp-2">{{ tema.descripcion }}</p>
                        </div>
                    </Link>
                </div>
                <div v-else class="text-center text-gray-500">
                    <p>Próximamente más temas.</p>
                </div>
                <div class="flex justify-center gap-3 mt-8">
                    <Link href="/descubre" class="btn btn-primary">Ver todos los temas</Link>
                    <Link href="/biblioteca" class="btn btn-secondary">Explorar biblioteca</Link>
                </div>
            </div>
        </Section>

        <!-- 7. Comentarios de nuestros miembros -->
        <Section>
            <EvaluacionSeccion seccion="Testimonios" :idoneidad="60" :claridad="65" :composicion="65" :posicion="70" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">Voces de la comunidad</h2>
                <p class="text-center text-gray-500 mb-12 max-w-2xl mx-auto">Personas que participan en TSEYOR comparten su experiencia.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <div v-for="testimonio in testimonios" :key="testimonio.name"
                        class="card bg-base-200 p-6 rounded-xl space-y-3">
                        <p class="italic text-sm">"{{ testimonio.text }}"</p>
                        <div>
                            <p class="font-bold text-sm">{{ testimonio.name }}</p>
                            <p class="text-xs text-gray-500">{{ testimonio.role }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-center mt-8">
                    <Link href="/blog" class="btn btn-primary">Conoce más experiencias en el blog</Link>
                </div>
            </div>
        </Section>

        <!-- 8. Blog: vida de la comunidad -->
        <Section>
            <EvaluacionSeccion seccion="Blog en home" :idoneidad="75" :claridad="75" :composicion="70" :posicion="65" />
            <div class="container mx-auto py-16 px-4">
                <h2 class="text-center mb-4">Blog: vida de la comunidad</h2>
                <p class="text-center text-gray-500 mb-12">Aquí puedes conocer sobre la vida de la comunidad Tseyor.</p>
                <div v-if="entradasRecientes?.length" class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                    <Link v-for="entrada in entradasRecientes.slice(0, 3)" :key="entrada.id" :href="entrada.ruta"
                        class="card bg-base-200 hover:bg-base-300 transition rounded-xl overflow-hidden">
                        <figure v-if="entrada.imagen" class="h-48 overflow-hidden">
                            <img :src="entrada.imagen" :alt="entrada.titulo" class="w-full h-full object-cover" />
                        </figure>
                        <div class="p-4">
                            <p class="text-xs text-gray-500">{{ entrada.fecha }}</p>
                            <h3 class="font-bold mt-1">{{ entrada.titulo }}</h3>
                        </div>
                    </Link>
                </div>
                <div v-else class="text-center text-gray-500 mb-8">
                    <p>Conoce algunas de nuestras aventuras en este camino del autorreconocimiento; podrás vernos en muchas fotografías de eventos que hemos realizado.</p>
                </div>
                <div class="flex justify-center mt-8">
                    <Link href="/blog" class="btn btn-primary">Leer blog</Link>
                </div>
            </div>
        </Section>

        <!-- 9. Novedades y boletín -->
        <Section>
            <EvaluacionSeccion seccion="Novedades y boletín" :idoneidad="70" :claridad="80" :composicion="75" :posicion="60" />
            <div class="container mx-auto py-16 px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-5xl mx-auto">
                    <div class="space-y-4">
                        <h2 class="text-3xl font-bold">Novedades</h2>
                        <p>Todas las novedades de los contenidos de Tseyor.</p>
                        <div v-if="novedadesRecientes?.length" class="space-y-3">
                            <Link v-for="novedad in novedadesRecientes.slice(0, 4)" :key="novedad.id" :href="novedad.ruta"
                                class="block p-3 bg-base-200 rounded-lg hover:bg-base-300 transition">
                                <p class="font-medium">{{ novedad.titulo }}</p>
                                <p class="text-xs text-gray-500">{{ novedad.fecha }}</p>
                            </Link>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <Link href="/novedades" class="btn btn-primary btn-sm">Ver novedades</Link>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h2 class="text-3xl font-bold">Boletín</h2>
                        <p>Recibe novedades, contenidos y publicaciones recientes de TSEYOR.</p>
                        <Suscribe />
                    </div>
                </div>
            </div>
        </Section>
    </Sections>
</template>

<script setup>
import EvaluacionSeccion from "@/Components/EvaluacionSeccion.vue"
import { ref } from "vue"

defineProps({
    hayProximosEventos: Boolean,
    temasClave: {
        type: Array,
        default: () => [],
    },
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
    novedadesRecientes: {
        type: Array,
        default: () => [],
    },
    testimonios: {
        type: Array,
        default: () => [
            {
                name: "Ana",
                role: "Alumna del curso",
                text: "El curso me ayudó a ordenar la filosofía y a encontrar respuestas que buscaba desde hace años. Me sentí acompañada en todo momento.",
            },
            {
                name: "Carlos",
                role: "Miembro de la comunidad",
                text: "Encontré un espacio donde puedo explorar mi espiritualidad sin dogmas, a mi ritmo, y con personas afines.",
            },
            {
                name: "Marta",
                role: "Participante de encuentros",
                text: "La biblioteca me permitió investigar a mi ritmo antes de decidirme a hacer el curso. Todo es gratuito y de libre acceso.",
            },
        ],
    },
})
</script>

<style scoped>
@reference "../../css/app.css";
</style>
