<template>
    <Sections height="500">

        <!-- 1. Hero: ¿Quiénes son las personas de Tseyor? -->
        <Section class="py-12 md:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 items-center [@media(min-width:870px)]:grid-cols-2">
                    <div>
                        <h1 class="text-4xl sm:text-5xl font-display font-black tracking-tight leading-tight mb-6">
                            ¿Quiénes Somos?
                        </h1>
                        <p class="text-lg text-base-content/70 leading-relaxed mb-4">
                            Somos personas como tú, de todas las edades y procedencias, unidas por una misma inquietud: explorar quiénes somos realmente y hacia dónde vamos como humanidad.
                        </p>
                        <p class="text-lg text-base-content/70 leading-relaxed mb-4">
                            Gente corriente que comparte un camino de autodescubrimiento, sin líderes ni dogmas. Personas que creen en la <Referencia>unidad en la diversidad</Referencia> y trabajan activamente por la creación de las <Referencia>Sociedades Armónicas</Referencia>.
                        </p>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 mb-6">
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                Personas diversas
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                Sin dogmas
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                En búsqueda
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                Comunidad abierta
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                Ayuda humanitaria
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link :href="route('filosofia')" class="btn btn-primary rounded-full px-8 shadow-lg whitespace-nowrap transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                Conoce nuestra filosofía
                                <ArrowRight class="w-4 h-4 ml-1.5 -mt-0.5" />
                            </Link>
                        </div>
                    </div>
                    <div class="hidden justify-center [@media(min-width:870px)]:flex">
                        <img src="/almacen/medios/paginas/quienes-somos.jpg" alt="Quiénes Somos" class="max-h-96 object-contain rounded-3xl shadow-xl" />
                    </div>
                </div>
            </div>
        </Section>

        <!-- 2. Chac-Mool Puente -->
        <Section class="py-16 md:py-20 bg-base-200/50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="rounded-3xl overflow-hidden shadow-md max-w-xs mx-auto">
                        <img src="/almacen/medios/paginas/Puente.jpg" alt="Chac-Mool Puente"
                            class="w-full h-auto" />
                    </div>
                    <div>
                        <span class="inline-block text-xs font-bold tracking-widest uppercase text-primary/70 font-display mb-3">
                            <Users class="w-3.5 h-3.5 text-primary inline mr-1" />
                            EL CANALIZADOR
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-4">Chac-Mool Puente</h2>
                        <p class="text-base-content/70 leading-relaxed mb-4">
                            Los comunicados son conversaciones interdimensionales realizadas telepáticamente por Chac-Mool Puente, canalizador de TSEYOR e intermediario entre los dos mundos.
                        </p>
                        <p class="text-base-content/70 leading-relaxed mb-6">
                            Conversaciones con la Confederación de Mundos Habitados de la Galaxia para impulsar sociedades sin líderes y la auténtica libertad del individuo.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link href="/comunicados" class="btn btn-primary rounded-full px-8 shadow-lg whitespace-nowrap transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                Ver Comunicados
                                <ArrowRight class="w-4 h-4 ml-1.5 -mt-0.5" />
                            </Link>
                            <Link :href="route('biblioteca')" class="btn rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 font-bold text-xs uppercase tracking-widest transition-all shadow-sm whitespace-nowrap">
                                Explorar biblioteca
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 3. Blog: Vida de la comunidad -->
        <Section class="py-16 md:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row items-baseline justify-between mb-12 border-b border-base-300 pb-4">
                    <h2 class="text-3xl font-display font-black tracking-tight">Vida de la comunidad</h2>
                    <Link href="/entradas" class="text-xs font-bold text-primary flex items-center gap-1 hover:text-base-content transition-colors uppercase tracking-wider">
                        Ver todo el blog
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div v-if="entradasRecientes?.length" v-for="entrada in entradasRecientes.slice(0, 4)" :key="entrada.slug"
                        class="flex flex-col sm:flex-row gap-6 bg-base-100 p-5 rounded-3xl border border-base-300 hover:border-primary/30 shadow-sm hover:shadow-md transition-all duration-300">
                        <div class="sm:w-1/3 h-44 rounded-2xl overflow-hidden shrink-0 relative bg-base-200">
                            <img :src="entrada.imagen" :alt="entrada.titulo" class="absolute inset-0 w-full h-full object-cover" />
                        </div>
                        <div class="grow flex flex-col justify-between py-1">
                            <div>
                                <span class="text-xs font-bold text-base-content/50 tracking-widest uppercase block mb-1.5">{{ entrada.fecha }}</span>
                                <h4 class="font-display font-bold text-base-content text-lg mb-2">{{ entrada.titulo }}</h4>
                                <p class="text-base-content/60 text-sm leading-relaxed">{{ entrada.descripcion?.slice(0, 128) + "..." }}</p>
                            </div>
                            <Link :href="'/blog/' + entrada.slug" class="text-xs font-bold text-primary mt-4 block uppercase tracking-widest hover:text-base-content transition-colors">Leer artículo →</Link>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 4. ¿Dónde estamos? -->
        <Section class="py-16 md:py-20 bg-base-200/50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-4">¿Dónde estamos?</h2>
                        <p class="text-base-content/70 leading-relaxed mb-4">Encuentra un centro o representante de Mundo Armónico TSEYOR cerca de ti.</p>
                        <p class="text-base-content/70 leading-relaxed mb-6">Estamos creciendo. Si aún no hay uno en tu zona, ¡anímate a crear uno! Únete a nuestro proyecto y te acompañaremos en el camino.</p>
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
                    <div class="relative min-h-[300px] rounded-3xl overflow-hidden shadow-md">
                        <img src="/almacen/medios/paginas/mapa.jpg" alt="Mapa"
                            class="absolute inset-0 w-full h-full object-cover" />
                    </div>
                </div>
            </div>
        </Section>

        <!-- 5. Preguntas frecuentes (acordeón) -->
        <Section class="py-16 md:py-20">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                    <span class="inline-block text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 px-3 py-1 mb-4 font-display">
                        ACLARA TUS INQUIETUDES
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight">Preguntas frecuentes</h2>
                </div>

                <div class="space-y-4">
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Qué es el grupo Tseyor?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                            Somos una comunidad de personas que compartimos un camino de autoconocimiento basado en la filosofía que nos transmiten nuestros Guías Estelares. Sin líderes, sin jerarquías, sin dogmas.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Es esto una secta?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                            No. No tenemos líderes, no hay dependencia jerárquica, no hay aportaciones económicas obligatorias, no hay aislamiento ni control. Cada persona es libre de explorar a su ritmo. Puedes informarte con total libertad.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Qué tipo de personas forman parte?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                            De todo tipo. Familias, profesionales, estudiantes, jubilados. Lo que compartimos es la curiosidad por el autodescubrimiento y las ganas de construir un mundo más armónico. No necesitas ningún perfil especial.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Hay que pagar algo?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                            No. Todo el conocimiento es gratuito: libros, comunicados, meditaciones, cursos. La ONG se sostiene con colaboraciones voluntarias. No hay cuotas ni tarifas obligatorias para participar.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Cómo puedo empezar?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
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
        </Section>

        <!-- 6. CTA final -->
        <Section class="py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/80 to-primary/85"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl p-8 sm:p-14 lg:p-20 text-center text-white relative overflow-hidden">
                    <div class="max-w-2xl mx-auto">
                        <span class="inline-block text-xs font-bold tracking-widest uppercase text-white/50 font-display mb-4">
                            TU PUERTA DE ENTRADA
                        </span>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-black tracking-tight leading-tight mb-6 text-white">
                            Inscríbete al curso gratuito
                        </h2>
                        <p class="text-white/70 leading-relaxed mb-10 text-sm sm:text-base font-light max-w-xl mx-auto">
                            El primer paso para formar parte de la comunidad es realizar nuestro Curso Holístico, totalmente gratuito. Sin compromiso, sin dogmas, sin coste.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link :href="route('cursos.inscripcion.nueva')" class="btn rounded-full bg-white text-base-content border-0 hover:bg-base-200 px-8 shadow-lg font-bold text-xs uppercase tracking-widest transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                Inscribirme gratis
                                <ArrowRight class="w-4 h-4 inline ml-1.5 -mt-0.5" />
                            </Link>
                            <Link :href="route('cursos')" class="btn rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs uppercase tracking-widest transition-all text-center backdrop-blur-sm hover:scale-105">
                                Saber más del curso
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

    </Sections>
</template>

<script setup>
import { ArrowRight, CheckCircle2, Users, ChevronDown } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

defineProps({
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
})
</script>
