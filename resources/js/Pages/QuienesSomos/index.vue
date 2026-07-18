<template>
    <Sections height="550">

        <!-- 1. Hero -->
        <Section>
            <div class="container mx-auto px-4 py-24 md:py-32">
                <div class="text-center max-w-5xl mx-auto">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Quiénes Somos
                    </span>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-black leading-[1.08] tracking-tight text-balance">
                        Personas como tú,
                        <span class="text-primary">en busca de respuestas</span>
                    </h1>

                    <p class="text-base sm:text-lg text-base-content/75 max-w-3xl mx-auto mt-8 leading-relaxed">
                        Gente corriente de todas las edades y procedencias, unida por una misma inquietud:
                        explorar quiénes somos realmente y hacia dónde vamos como humanidad.
                    </p>

                    <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 mt-8">
                        <span class="inline-flex items-center gap-2 text-sm text-base-content/60">
                            <Icon icon="ph:check-circle-duotone" class="w-4 h-4 text-primary" />
                            Personas diversas
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm text-base-content/60">
                            <Icon icon="ph:check-circle-duotone" class="w-4 h-4 text-primary" />
                            Sin dogmas
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm text-base-content/60">
                            <Icon icon="ph:check-circle-duotone" class="w-4 h-4 text-primary" />
                            En búsqueda
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm text-base-content/60">
                            <Icon icon="ph:check-circle-duotone" class="w-4 h-4 text-primary" />
                            Comunidad abierta
                        </span>
                        <span class="inline-flex items-center gap-2 text-sm text-base-content/60">
                            <Icon icon="ph:check-circle-duotone" class="w-4 h-4 text-primary" />
                            Ayuda humanitaria
                        </span>
                    </div>

                    <div class="max-w-3xl mx-auto mt-10 rounded-3xl bg-gradient-to-br from-primary/[0.07] to-base-100 border border-primary/10 px-7 py-6 sm:px-10 sm:py-8">
                        <p class="text-base-content/75 leading-relaxed">
                            Sin líderes ni jerarquías. Sin dogmas ni creencias impuestas. Personas que creen en
                            la <Referencia>unidad en la diversidad</Referencia> y trabajan activamente por
                            la creación de las <Referencia>Sociedades Armónicas</Referencia>.
                        </p>
                    </div>

                    <a
                        href="#chac-mool"
                        @click.prevent="scrollASeccion('chac-mool')"
                        class="inline-flex items-center gap-2 mt-10 text-xs font-bold uppercase tracking-[0.2em] text-primary hover:text-base-content transition-colors"
                    >
                        Conocer al canalizador
                        <Icon icon="ph:arrow-down-duotone" class="text-lg" />
                    </a>
                </div>
            </div>
        </Section>

        <!-- 2-6. Contenido principal -->
        <div id="contenido-principal" class="container mx-auto px-4 py-24 md:py-32">
            <div class="grid lg:grid-cols-[220px_1fr] gap-12 lg:gap-20">

                <!-- Left: sticky nav -->
                <div>
                    <div class="lg:hidden mb-8">
                        <label class="text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-3 block font-display">
                            En esta página
                        </label>
                        <select
                            @change="saltarASeccion"
                            class="select select-bordered w-full bg-base-100 border-base-300 rounded-xl text-sm font-display font-bold"
                            aria-label="Secciones de quienes somos"
                        >
                            <option v-for="clave in claves" :key="clave.ancla" :value="clave.ancla">
                                {{ clave.titulo }}
                            </option>
                        </select>
                    </div>

                    <nav class="hidden lg:block lg:sticky lg:top-28 lg:self-start space-y-1" aria-label="Secciones de Quiénes Somos">
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                            En esta página
                        </span>
                        <a
                            v-for="(clave, index) in claves"
                            :key="clave.ancla"
                            :href="'#' + clave.ancla"
                            :aria-current="activeSection === clave.ancla ? 'section' : undefined"
                            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors group"
                            :class="activeSection === clave.ancla ? 'bg-primary/10 text-primary' : 'hover:bg-base-200/60'"
                        >
                            <span
                                class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-sm font-display font-black transition-colors"
                                :class="activeSection === clave.ancla ? 'bg-primary text-primary-content' : 'bg-primary/10 text-primary group-hover:bg-primary/20'"
                            >
                                {{ index + 1 }}
                            </span>
                            <span class="text-sm font-display font-bold transition-colors">
                                {{ clave.titulo }}
                            </span>
                        </a>
                    </nav>
                </div>

                <!-- Right: content blocks -->
                <div class="space-y-24">

                    <!-- Chac-Mool Puente -->
                    <article id="chac-mool" class="scroll-mt-24">
                        <div class="grid md:grid-cols-5 gap-10 items-center">
                            <div class="md:col-span-2">
                                <div class="rounded-2xl overflow-hidden shadow-lg max-w-xs mx-auto md:mx-0 bg-base-200">
                                    <img
                                        src="/almacen/medios/paginas/Puente.jpg"
                                        alt="Chac-Mool Puente, canalizador de TSEYOR"
                                        class="w-full h-auto"
                                        loading="lazy"
                                    />
                                </div>
                            </div>
                            <div class="md:col-span-3">
                                <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-primary/70 font-display mb-3">
                                    <Icon icon="ph:users-three-duotone" class="w-3.5 h-3.5 text-primary" />
                                    El canalizador
                                </span>
                                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-4 text-balance">
                                    Chac-Mool Puente
                                </h2>
                                <p class="text-base-content/75 leading-relaxed mb-4">
                                    Los comunicados con los Guías Estelares son conversaciones interdimensionales realizadas
                                    telepáticamente por <strong>Chac-Mool Puente</strong>, canalizador de TSEYOR e
                                    intermediario entre los dos mundos.
                                </p>
                                <p class="text-base-content/75 leading-relaxed mb-6">
                                    Conversaciones con la Confederación de Mundos Habitados de la Galaxia para impulsar
                                    sociedades sin líderes y la auténtica libertad del individuo.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <Link href="/comunicados" class="btn btn-primary rounded-full px-8 shadow-lg whitespace-nowrap transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                        Ver Comunicados
                                        <ArrowRight class="w-4 h-4 ml-1.5 -mt-0.5" />
                                    </Link>
                                    <Link :href="route('origenes-de-tseyor')" class="btn rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 font-bold text-xs uppercase tracking-widest transition-all shadow-sm whitespace-nowrap">
                                        Orígenes de TSEYOR
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Transición -->
                        <div class="flex gap-3 mt-16 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Y esta comunidad, ¿cómo se organiza? ¿qué vida tiene?"
                            </p>
                        </div>
                    </article>

                    <!-- Comunidad -->
                    <article id="comunidad" class="scroll-mt-24">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                            <Icon icon="ph:heartbeat-duotone" class="text-2xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            Vida comunitaria
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                            Una comunidad que aprende y crece unida
                        </h2>

                        <div class="space-y-6 text-base-content/75 leading-relaxed max-w-3xl mb-12">
                            <p class="text-lg text-base-content/80">
                                TSEYOR no es una organización vertical. Es una comunidad horizontal donde cada persona
                                aporta desde su talento y su experiencia, sin jerarquías ni estructuras de poder.
                            </p>
                            <p>
                                Nos organizamos en <strong>comisiones de trabajo</strong> voluntarias: difusión, ayuda
                                humanitaria, traducciones, centros locales, tecnología y más. Cada comisión se autogestiona
                                y coordina con las demás desde la confianza y la retroalimentación.
                            </p>
                            <p>
                                La comunidad se reúne periódicamente en <strong>encuentros presenciales y virtuales</strong>,
                                donde se comparten experiencias, se leen comunicados y se profundiza en el camino de
                                autoconocimiento. Todo abierto a quien quiera participar.
                            </p>
                        </div>

                        <!-- Blog preview -->
                        <div class="border-t border-base-200/60 pt-10">
                            <div class="flex flex-col sm:flex-row items-baseline justify-between mb-8">
                                <h3 class="text-xl font-display font-bold tracking-tight">Vida de la comunidad</h3>
                                <Link href="/entradas" class="text-xs font-bold text-primary flex items-center gap-1 hover:text-base-content transition-colors uppercase tracking-wider">
                                    Ver todo el blog
                                    <ArrowRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>

                            <div v-if="entradasRecientes?.length" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div v-for="entrada in entradasRecientes.slice(0, 4)" :key="entrada.slug"
                                    class="flex flex-col sm:flex-row gap-5 bg-base-100 p-4 rounded-2xl border border-base-200/80 hover:border-primary/30 shadow-sm hover:shadow-md transition-all duration-300">
                                    <div class="sm:w-2/5 aspect-[4/3] rounded-xl overflow-hidden shrink-0 relative bg-base-200">
                                        <img :src="entrada.imagen" :alt="entrada.titulo"
                                            class="absolute inset-0 w-full h-full object-cover"
                                            loading="lazy" />
                                    </div>
                                    <div class="grow flex flex-col justify-between py-0.5 min-w-0">
                                        <div>
                                            <span class="text-xs font-bold text-base-content/40 tracking-widest uppercase block mb-1">{{ entrada.fecha }}</span>
                                            <h4 class="font-display font-bold text-base-content text-base mb-1.5 leading-snug">{{ entrada.titulo }}</h4>
                                            <p class="text-base-content/60 text-sm leading-relaxed">{{ entrada.descripcion?.slice(0, 128) ?? '' }}{{ entrada.descripcion?.length > 128 ? '…' : '' }}</p>
                                        </div>
                                        <Link :href="'/blog/' + entrada.slug" class="text-xs font-bold text-primary mt-3 block uppercase tracking-widest hover:text-base-content transition-colors">
                                            Leer artículo →
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <div v-else class="text-center py-12 rounded-2xl bg-base-200/30 border border-dashed border-base-300">
                                <Icon icon="ph:article-duotone" class="text-3xl text-base-content/20 mx-auto mb-3" />
                                <p class="text-sm text-base-content/40">Próximamente compartiremos aquí las novedades de la comunidad.</p>
                            </div>
                        </div>

                        <!-- Transición -->
                        <div class="flex gap-3 mt-16 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Todo esto suena bien… ¿dónde puedo encontraros?"
                            </p>
                        </div>
                    </article>

                    <!-- Dónde estamos -->
                    <article id="donde-estamos" class="scroll-mt-24">
                        <div class="grid md:grid-cols-2 gap-12 items-center">
                            <div>
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                                    <Icon icon="ph:map-pin-duotone" class="text-2xl text-primary" />
                                </div>
                                <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                                    Presencia
                                </span>
                                <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                                    ¿Dónde estamos?
                                </h2>
                                <p class="text-base-content/75 leading-relaxed mb-4">
                                    Encuentra un centro o representante de Mundo Armónico TSEYOR cerca de ti.
                                </p>
                                <p class="text-base-content/75 leading-relaxed mb-6">
                                    Estamos creciendo. Si aún no hay uno en tu zona, ¡anímate a crear uno!
                                    Únete a nuestro proyecto y te acompañaremos en el camino.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <Link :href="route('contactos')" class="btn btn-primary rounded-full px-8 shadow-lg whitespace-nowrap transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                        Ver Mapa
                                        <ArrowRight class="w-4 h-4 ml-1.5 -mt-0.5" />
                                    </Link>
                                    <Link :href="route('cursos')" class="btn rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 font-bold text-xs uppercase tracking-widest transition-all shadow-sm whitespace-nowrap">
                                        Quiero unirme
                                    </Link>
                                </div>
                            </div>
                            <div class="relative aspect-[4/3] rounded-2xl overflow-hidden shadow-md bg-base-200">
                                <img
                                    src="/almacen/medios/paginas/mapa.jpg"
                                    alt="Mapa de centros TSEYOR"
                                    class="absolute inset-0 w-full h-full object-cover"
                                    loading="lazy"
                                />
                            </div>
                        </div>

                        <!-- Transición -->
                        <div class="flex gap-3 mt-16 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Tengo algunas dudas todavía…"
                            </p>
                        </div>
                    </article>

                    <!-- Preguntas frecuentes -->
                    <article id="preguntas" class="scroll-mt-24">
                        <div class="max-w-3xl">
                            <div class="text-center max-w-2xl mb-12">
                                <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                                    FAQ
                                </span>
                                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight text-balance">
                                    Respuestas a tus dudas
                                </h2>
                            </div>

                            <div class="space-y-4">
                                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                                        ¿Qué es el grupo Tseyor?
                                        <ChevronDown class="w-5 h-5 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                                    </summary>
                                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                                        Somos una comunidad de personas que compartimos un camino de autoconocimiento basado en la filosofía que nos transmiten nuestros Guías Estelares. Sin líderes, sin jerarquías, sin dogmas.
                                    </p>
                                </details>
                                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                                        ¿Es esto una secta?
                                        <ChevronDown class="w-5 h-5 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                                    </summary>
                                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                                        No. No tenemos líderes, no hay dependencia jerárquica, no hay aportaciones económicas obligatorias, no hay aislamiento ni control. Cada persona es libre de explorar a su ritmo. Puedes informarte con total libertad.
                                    </p>
                                </details>
                                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                                        ¿Qué tipo de personas forman parte?
                                        <ChevronDown class="w-5 h-5 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                                    </summary>
                                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                                        De todo tipo. Familias, profesionales, estudiantes, jubilados. Lo que compartimos es la curiosidad por el autodescubrimiento y las ganas de construir un mundo más armónico. No necesitas ningún perfil especial.
                                    </p>
                                </details>
                                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                                        ¿Hay que pagar algo?
                                        <ChevronDown class="w-5 h-5 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                                    </summary>
                                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                                        No. Todo el conocimiento es gratuito: libros, comunicados, meditaciones, cursos. La ONG se sostiene con colaboraciones voluntarias. No hay cuotas ni tarifas obligatorias para participar.
                                    </p>
                                </details>
                                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                                        ¿Cómo puedo empezar?
                                        <ChevronDown class="w-5 h-5 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                                    </summary>
                                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                                        La puerta de entrada es el <Link :href="route('cursos')" class="text-primary hover:underline font-medium">curso holístico</Link>, gratuito y tutelado. También puedes explorar la <Link :href="route('biblioteca')" class="text-primary hover:underline font-medium">biblioteca</Link> con cientos de libros gratis, o visitar un centro cercano para conocer el grupo en persona.
                                    </p>
                                </details>
                            </div>

                            <div class="text-center mt-10">
                                <Link href="/preguntas-frecuentes" class="text-xs font-bold text-primary hover:text-base-content transition-colors uppercase tracking-widest">
                                    Ver todas las preguntas frecuentes →
                                </Link>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        </div>

        <!-- 7. CTA final -->
        <Section>
            <div class="container mx-auto px-4 py-24 md:py-32">
                <div class="max-w-5xl mx-auto text-center">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Una comunidad viva
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight text-balance">
                        Este no es un grupo al que te unes.<br>
                        <span class="text-primary">Es un camino que eliges recorrer.</span>
                    </h2>

                    <div class="bg-gradient-to-br from-base-200/80 to-base-100 rounded-3xl p-8 sm:p-10 md:p-14 border border-base-300/30 text-left max-w-4xl mx-auto mt-12 space-y-6">
                        <p class="text-base-content/75 leading-relaxed text-lg">
                            No pedimos que creas en nada. Solo que tengas la curiosidad de explorar,
            la honestidad de cuestionarte y las ganas de compartir con otros que, como tú,
            buscan respuestas más allá de lo establecido.
                        </p>
                        <p class="text-base-content/75 leading-relaxed">
                            No hay un "primer paso" obligatorio. Puedes empezar por el curso, por la
                            biblioteca, por un centro, por un comunicado. Cada cual encuentra su puerta.
                            Lo importante es decidir mirar.
                        </p>
                    </div>

                    <div class="flex flex-wrap justify-center gap-4 mt-12">
                        <Link
                            :href="route('cursos.inscripcion.nueva')"
                            class="btn btn-primary px-9 py-4 text-sm font-bold uppercase tracking-wider rounded-full shadow-lg hover:shadow-xl transition-all"
                        >
                            Inscribirme al curso
                            <Icon icon="ph:arrow-right-duotone" class="text-lg" />
                        </Link>
                        <Link
                            :href="route('cursos')"
                            class="btn btn-secondary px-8 py-4 rounded-full"
                        >
                            Conocer el curso
                        </Link>
                    </div>
                </div>
            </div>
        </Section>

    </Sections>
</template>

<script setup>

const activeSection = ref('chac-mool')

const claves = [
    { ancla: 'chac-mool', titulo: 'Chac-Mool Puente' },
    { ancla: 'comunidad', titulo: 'Vida comunitaria' },
    { ancla: 'donde-estamos', titulo: 'Dónde estamos' },
    { ancla: 'preguntas', titulo: 'Preguntas frecuentes' },
]

function saltarASeccion(e) {
    const id = e.target.value
    const el = document.getElementById(id)
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
        history.replaceState(null, '', `#${id}`)
    }
}

function scrollASeccion(id) {
    const el = document.getElementById(id)
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}

onMounted(() => {
    const articles = document.querySelectorAll('#contenido-principal article[id]')
    if (!articles.length) return

    const observer = new IntersectionObserver((entries) => {
        for (const entry of entries) {
            if (entry.isIntersecting) {
                activeSection.value = entry.target.id
                history.replaceState(null, '', `#${entry.target.id}`)
            }
        }
    }, {
        rootMargin: '-80px 0px -60% 0px',
    })

    articles.forEach(el => observer.observe(el))

    const hash = window.location.hash.replace('#', '')
    if (hash) {
        const target = document.getElementById(hash)
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' })
            }, 200)
            activeSection.value = hash
        }
    } else {
        activeSection.value = articles[0].id
    }
})

import { ArrowRight, ChevronDown } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

defineProps({
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
})
</script>
