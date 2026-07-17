<template>
    <div class="container flex justify-between mx-auto px-4 py-8 md:py-12">
        <Back href="/quienes-somos" :floatAtY="-100">Quiénes Somos</Back>
        <Share />
    </div>

    <Sections white-first>

        <!-- 1. Hero: Presentación -->
        <Section>
            <div class="container mx-auto px-4 py-24 md:py-32">
                <div class="text-center max-w-5xl mx-auto">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        ONG Mundo Armónico TSEYOR
                    </span>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-black leading-[1.08] tracking-tight text-balance">
                        Una comunidad de voluntarios
                        <span class="text-primary">al servicio de las personas</span>
                    </h1>

                    <p class="text-base sm:text-lg text-base-content/75 max-w-3xl mx-auto mt-8 leading-relaxed">
                        Reunimos a personas implicadas en el proyecto TSEYOR que colaboran, cada una desde sus capacidades,
                        para impulsar ayuda humanitaria, formación, acompañamiento y cooperación.
                    </p>

                    <p class="text-base text-base-content/60 max-w-2xl mx-auto mt-4 leading-relaxed">
                        Todos aportamos y todos seguimos aprendiendo. La filosofía se hace visible en la forma de servir,
                        relacionarnos y trabajar juntos por el bien común.
                    </p>

                    <div class="flex flex-wrap justify-center gap-4 mt-10">
                        <Link href="/donar" class="btn btn-primary rounded-full px-8 shadow-lg">
                            Donar ahora
                            <Icon icon="ph:heart-duotone" class="text-lg" />
                        </Link>
                        <Link href="/centros" class="btn btn-secondary rounded-full px-8">
                            Buscar un centro
                            <Icon icon="ph:map-pin-duotone" class="text-lg" />
                        </Link>
                    </div>

                    <a
                        href="#mision"
                        @click.prevent="scrollASeccion('mision')"
                        class="inline-flex items-center gap-2 mt-8 text-xs font-bold uppercase tracking-[0.2em] text-primary hover:text-base-content transition-colors"
                    >
                        Conocer nuestra misión
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
                            aria-label="Secciones de la ONG"
                        >
                            <option v-for="clave in claves" :key="clave.ancla" :value="clave.ancla">
                                {{ clave.titulo }}
                            </option>
                        </select>
                    </div>

                    <nav class="hidden lg:block lg:sticky lg:top-28 lg:self-start space-y-1" aria-label="Secciones de la ONG">
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

                    <!-- Misión -->
                    <article id="mision" class="scroll-mt-24">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                            <Icon icon="ph:target-duotone" class="text-2xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            Nuestra misión
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-8 text-balance">
                            Contribuir a una sociedad más armónica, justa y solidaria
                        </h2>

                        <p class="text-base-content/75 leading-relaxed max-w-3xl mb-10">
                            Entendemos la ayuda de una manera integral: atender necesidades concretas y, al mismo tiempo,
                            favorecer el desarrollo humano, la autonomía, el equilibrio y el hermanamiento.
                        </p>

                        <div class="grid sm:grid-cols-2 gap-5 max-w-3xl">
                            <article v-for="ambito in ambitos" :key="ambito.titulo"
                                class="rounded-2xl bg-base-100 border border-base-200/60 p-6 shadow-sm hover:shadow-md transition-shadow">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
                                    <Icon :icon="ambito.icono" class="text-xl text-primary" />
                                </div>
                                <h3 class="font-display font-bold text-base mb-2">{{ ambito.titulo }}</h3>
                                <p class="text-sm text-base-content/60 leading-relaxed">{{ ambito.texto }}</p>
                            </article>
                        </div>

                        <div class="mt-10 max-w-3xl rounded-2xl bg-primary/5 border border-primary/10 p-6 sm:p-8">
                            <p class="text-base-content/75 leading-relaxed">
                                Nuestros Estatutos recogen estos fines, dan forma legal a la organización y permiten que las
                                iniciativas se desarrollen con responsabilidad, continuidad y transparencia.
                            </p>
                            <Link
                                v-if="estatutosUrl"
                                :href="estatutosUrl"
                                class="inline-flex items-center gap-2 text-primary font-bold mt-4 text-sm hover:text-base-content transition-colors"
                            >
                                Consultar los Estatutos
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>

                        <!-- Transición -->
                        <div class="flex gap-3 mt-16 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Y en concreto, ¿qué proyectos están activos ahora?"
                            </p>
                        </div>
                    </article>

                    <!-- Proyectos -->
                    <article id="proyectos" class="scroll-mt-24">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                            <Icon icon="ph:clipboard-text-duotone" class="text-2xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            Proyectos activos
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                            Distintas formas de una misma labor
                        </h2>

                        <p class="text-base-content/75 leading-relaxed max-w-3xl mb-10">
                            Las iniciativas cambian y crecen según las necesidades y las personas que participan, pero todas
                            comparten un mismo sentido de servicio.
                        </p>

                        <div class="grid sm:grid-cols-2 gap-5 max-w-3xl">
                            <article v-for="proyecto in proyectos" :key="proyecto.titulo"
                                class="rounded-2xl bg-base-100 border border-base-200/60 p-6 shadow-sm hover:shadow-md transition-shadow">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center mb-4">
                                    <Icon :icon="proyecto.icono" class="text-xl text-primary" />
                                </div>
                                <h3 class="font-display font-bold text-base mb-2">{{ proyecto.titulo }}</h3>
                                <p class="text-sm text-base-content/60 leading-relaxed">{{ proyecto.texto }}</p>
                            </article>
                        </div>

                        <!-- Transición -->
                        <div class="flex gap-3 mt-16 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Todo esto suena muy bonito, pero ¿quién lo hace posible?"
                            </p>
                        </div>
                    </article>

                    <!-- Voluntariado -->
                    <article id="voluntariado" class="scroll-mt-24">
                        <div class="grid md:grid-cols-5 gap-10 items-center">
                            <div class="md:col-span-3">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                                    <Icon icon="ph:handshake-duotone" class="text-2xl text-primary" />
                                </div>
                                <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                                    Voluntariado
                                </span>
                                <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                                    La ONG somos las personas que la hacemos posible
                                </h2>

                                <p class="text-base-content/75 leading-relaxed mb-4">
                                    Unas personas colaboran en tareas administrativas; otras, en organización, divulgación,
                                    acompañamiento, formación, documentación, comunicación o logística. Cada función es necesaria y cada aportación suma.
                                </p>

                                <p class="text-base-content/75 leading-relaxed mb-6">
                                    No actuamos desde una posición de superioridad ni como poseedores de respuestas definitivas.
                                    Somos aprendices de la filosofía TSEYOR que procuramos llevar sus principios a la práctica mediante
                                    el servicio, la cooperación y el trabajo compartido.
                                </p>

                                <p class="text-xl sm:text-2xl font-display font-bold text-primary leading-snug mb-8">
                                    Todos aportamos. Todos aprendemos. Todos formamos parte de la acción.
                                </p>

                                <Link
                                    :href="voluntariadoUrl || route('contactos')"
                                    class="btn btn-primary rounded-full px-8 shadow-lg"
                                >
                                    Quiero colaborar
                                    <ArrowRight class="w-4 h-4" />
                                </Link>
                            </div>
                            <div class="md:col-span-2 hidden md:flex justify-center">
                                <div class="w-full max-w-xs aspect-square rounded-2xl bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/10 flex items-center justify-center p-8">
                                    <Icon icon="ph:hand-heart-duotone" class="text-8xl text-primary/30" />
                                </div>
                            </div>
                        </div>

                        <!-- Transición -->
                        <div class="flex gap-3 mt-16 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Me gustaría involucrarme… ¿qué tengo que hacer?"
                            </p>
                        </div>
                    </article>

                    <!-- Únete -->
                    <article id="unete" class="scroll-mt-24">
                        <div class="text-center max-w-3xl mx-auto">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6 mx-auto">
                                <Icon icon="ph:user-plus-duotone" class="text-2xl text-primary" />
                            </div>
                            <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                                Únete al equipo
                            </span>
                            <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                                ¿Quieres formar parte de la ONG?
                            </h2>

                            <p class="text-base-content/75 leading-relaxed max-w-2xl mx-auto mb-8">
                                El primer paso es realizar el <strong>Curso Holístico TSEYOR</strong>, donde conocerás las bases
                                de la filosofía y te prepararás para colaborar activamente en los proyectos de la ONG.
                            </p>

                            <div class="flex flex-wrap justify-center gap-4">
                                <Link
                                    :href="route('cursos.inscripcion.nueva')"
                                    class="btn btn-primary rounded-full px-8 shadow-lg"
                                >
                                    Hazte voluntario
                                    <Icon icon="ph:hand-heart-duotone" class="text-lg" />
                                </Link>
                                <Link
                                    :href="route('libro', 'la-ong-mundo-armonico-tseyor')"
                                    class="btn rounded-full bg-base-100 border border-base-300 hover:border-primary/40 px-8"
                                >
                                    Documentación de la ONG
                                    <ArrowRight class="w-4 h-4" />
                                </Link>
                            </div>
                        </div>
                    </article>

                </div>
            </div>
        </div>

        <!-- 7. CTA final -->
        <Section>
            <div class="relative py-24 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/95 via-primary/85 to-secondary/80"></div>
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-black tracking-tight leading-tight mb-6 text-white">
                        El servicio no es una obligación.<br>
                        <span class="text-white/70">Es una forma de despertar.</span>
                    </h2>
                    <p class="text-white/60 text-sm sm:text-base leading-relaxed max-w-xl mx-auto mb-10">
                        En TSEYOR no buscamos seguidores. Buscamos personas que, desde su propia experiencia,
                        decidan poner su tiempo y talento al servicio de un mundo más armónico.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            :href="route('cursos.inscripcion.nueva')"
                            class="btn rounded-full bg-white text-base-content border-0 hover:bg-base-200 px-8 shadow-lg font-bold text-xs uppercase tracking-widest transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105"
                        >
                            Quiero colaborar
                            <ArrowRight class="w-4 h-4 inline ml-1.5 -mt-0.5" />
                        </Link>
                        <Link
                            :href="route('cursos')"
                            class="btn rounded-full bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs uppercase tracking-widest backdrop-blur-sm"
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

const activeSection = ref('mision')

const claves = [
    { ancla: 'mision', titulo: 'Nuestra misión' },
    { ancla: 'proyectos', titulo: 'Proyectos activos' },
    { ancla: 'voluntariado', titulo: 'Voluntariado' },
    { ancla: 'unete', titulo: 'Únete al equipo' },
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

import { ArrowRight } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

defineProps({
    estatutosUrl: {
        type: String,
        default: '',
    },
    voluntariadoUrl: {
        type: String,
        default: '',
    },
})

const ambitos = [
    {
        titulo: 'Ayuda y acompañamiento',
        texto: 'Apoyamos a personas y comunidades mediante recursos, alimentos, presencia cercana y acciones solidarias.',
        icono: 'ph:hand-heart-duotone',
    },
    {
        titulo: 'Formación y divulgación',
        texto: 'Compartimos conocimientos, talleres y materiales que favorecen el autoconocimiento y el desarrollo humano.',
        icono: 'ph:book-open-duotone',
    },
    {
        titulo: 'Salud, alimentación y equilibrio',
        texto: 'Impulsamos iniciativas relacionadas con el bienestar integral, la alimentación y la sanación y autosanación.',
        icono: 'ph:heartbeat-duotone',
    },
    {
        titulo: 'Cooperación y comunidad',
        texto: 'Creamos espacios de encuentro y redes de colaboración basadas en el respeto, la hermandad y el bien común.',
        icono: 'ph:users-three-duotone',
    },
]

const proyectos = [
    {
        titulo: 'Encuentro y hermanamiento',
        texto: 'Reuniones, convivencias y espacios en los que compartir experiencias, necesidades e iniciativas.',
        icono: 'ph:users-duotone',
    },
    {
        titulo: 'Formación, divulgación y creatividad',
        texto: 'Cursos, talleres, publicaciones y actividades de arte, ciencia y expresión al servicio del desarrollo humano.',
        icono: 'ph:graduation-cap-duotone',
    },
    {
        titulo: 'Salud y acompañamiento',
        texto: 'Prácticas y espacios orientados al equilibrio, la sanación y autosanación y el apoyo cercano entre personas.',
        icono: 'ph:shield-check-duotone',
    },
    {
        titulo: 'Ayuda solidaria y cooperación',
        texto: 'Alimentos, recursos y redes de colaboración para responder a necesidades concretas y apoyar proyectos comunitarios.',
        icono: 'ph:globe-duotone',
    },
]
</script>
