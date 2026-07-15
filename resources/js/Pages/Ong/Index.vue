<template>
    <div class="container flex justify-between mx-auto px-4 py-8 md:py-12">
        <Back href="/quienes-somos" :floatAtY="-100">Quiénes Somos</Back>
        <Share />
    </div>

    <Sections white-first>

        <!-- Presentación -->
        <Section>
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <img
                    src="/archivos/logos/logo-ong-mundo-armonico.JPG"
                    class="w-full max-w-md h-auto mx-auto mb-10"
                    alt="ONG Mundo Armónico TSEYOR"
                >

                <span class="inline-block text-xs font-bold uppercase tracking-[0.28em] text-primary/60 mb-5 font-display">
                    ONG Mundo Armónico TSEYOR
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-display font-black leading-tight tracking-tight">
                    Una comunidad de voluntarios
                    <span class="text-primary">al servicio de las personas</span>
                </h1>

                <p class="text-lg text-base-content/70 max-w-3xl mx-auto mt-7 leading-relaxed">
                    Reunimos a personas implicadas en el proyecto TSEYOR que colaboran, cada una desde sus capacidades,
                    para impulsar ayuda humanitaria, formación, acompañamiento y cooperación.
                </p>

                <p class="text-base text-base-content/60 max-w-2xl mx-auto mt-4 leading-relaxed">
                    Todos aportamos y todos seguimos aprendiendo. La filosofía se hace visible en la forma de servir,
                    relacionarnos y trabajar juntos por el bien común.
                </p>

                <a href="#proyectos" class="btn btn-primary rounded-full px-8 shadow-lg mt-9">
                    Conocer nuestra labor
                    <ArrowRight class="w-4 h-4" />
                </a>
            </div>
        </Section>

        <!-- Misión -->
        <Section>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <span class="inline-block text-xs font-bold uppercase tracking-[0.28em] text-primary/60 mb-5 font-display">
                    Nuestra misión
                </span>

                <h2 class="text-3xl sm:text-4xl font-display font-black leading-tight tracking-tight">
                    Contribuir a una sociedad más armónica, justa y solidaria
                </h2>

                <p class="text-lg text-base-content/70 mt-7 leading-relaxed">
                    Entendemos la ayuda de una manera integral: atender necesidades concretas y, al mismo tiempo,
                    favorecer el desarrollo humano, la autonomía, el equilibrio y el hermanamiento.
                </p>

                <div class="mt-10 divide-y divide-base-300/60 border-y border-base-300/60">
                    <div v-for="ambito in ambitos" :key="ambito.titulo" class="py-6">
                        <h3 class="text-xl font-display font-bold">{{ ambito.titulo }}</h3>
                        <p class="text-base text-base-content/65 mt-2 leading-relaxed">{{ ambito.texto }}</p>
                    </div>
                </div>

                <div class="mt-9 border-l-4 border-primary pl-5">
                    <p class="text-base text-base-content/65 leading-relaxed">
                        Nuestros Estatutos recogen estos fines, dan forma legal a la organización y permiten que las
                        iniciativas se desarrollen con responsabilidad, continuidad y transparencia.
                    </p>
                    <Link
                        v-if="estatutosUrl"
                        :href="estatutosUrl"
                        class="inline-flex items-center gap-2 text-primary font-bold mt-4 hover:underline"
                    >
                        Consultar los Estatutos
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </Section>

        <!-- Proyectos -->
        <Section>
            <div id="proyectos" class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 scroll-mt-24">
                <span class="inline-block text-xs font-bold uppercase tracking-[0.28em] text-primary/60 mb-5 font-display">
                    Proyectos activos
                </span>

                <h2 class="text-3xl sm:text-4xl font-display font-black leading-tight tracking-tight">
                    Distintas formas de una misma labor
                </h2>

                <p class="text-lg text-base-content/70 mt-7 leading-relaxed">
                    Las iniciativas cambian y crecen según las necesidades y las personas que participan, pero todas
                    comparten un mismo sentido de servicio.
                </p>

                <div class="mt-10 divide-y divide-base-300/60 border-y border-base-300/60">
                    <article v-for="proyecto in proyectos" :key="proyecto.titulo" class="py-6">
                        <h3 class="text-xl font-display font-bold">{{ proyecto.titulo }}</h3>
                        <p class="text-base text-base-content/65 mt-2 leading-relaxed">{{ proyecto.texto }}</p>
                    </article>
                </div>
            </div>
        </Section>

        <!-- Voluntariado -->
        <Section>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <span class="inline-block text-xs font-bold uppercase tracking-[0.28em] text-primary/60 mb-5 font-display">
                    Voluntariado
                </span>

                <h2 class="text-3xl sm:text-4xl font-display font-black leading-tight tracking-tight">
                    La ONG somos las personas que la hacemos posible
                </h2>

                <p class="text-lg text-base-content/70 mt-7 leading-relaxed">
                    Unas personas colaboran en tareas administrativas; otras, en organización, divulgación,
                    acompañamiento, formación, comunicación o logística. Cada función es necesaria y cada aportación suma.
                </p>

                <p class="text-lg text-base-content/70 mt-5 leading-relaxed">
                    No actuamos desde una posición de superioridad ni como poseedores de respuestas definitivas.
                    Somos aprendices de la filosofía TSEYOR que procuramos llevar sus principios a la práctica mediante
                    el servicio, la cooperación y el trabajo compartido.
                </p>

                <p class="text-2xl sm:text-3xl font-display font-bold text-primary mt-10 leading-snug">
                    Todos aportamos. Todos aprendemos. Todos formamos parte de la acción.
                </p>

                <Link
                    :href="voluntariadoUrl || route('contactos')"
                    class="btn btn-primary rounded-full px-8 shadow-lg mt-9"
                >
                    Quiero colaborar
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </Section>

        <!-- Documentación -->
        <Section>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 text-center">
                <span class="inline-block text-xs font-bold uppercase tracking-[0.28em] text-primary/60 mb-5 font-display">
                    Conocer más
                </span>

                <h2 class="text-3xl sm:text-4xl font-display font-black leading-tight tracking-tight">
                    Origen, fundamentos y desarrollo de la ONG
                </h2>

                <p class="text-lg text-base-content/65 max-w-2xl mx-auto mt-6 leading-relaxed">
                    La biblioteca reúne la historia de Mundo Armónico TSEYOR, sus Estatutos y los documentos que explican
                    su evolución y sus ámbitos de actuación.
                </p>

                <Link
                    href="/libros/la-ong-mundo-armonico-tseyor"
                    class="btn rounded-full bg-base-100 border border-base-300 hover:border-primary/40 px-8 mt-9"
                >
                    Ver documentación en la Biblioteca
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </Section>

    </Sections>
</template>

<script setup>
import { ArrowRight } from 'lucide-vue-next'

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
    },
    {
        titulo: 'Formación y divulgación',
        texto: 'Compartimos conocimientos, talleres y materiales que favorecen el autoconocimiento y el desarrollo humano.',
    },
    {
        titulo: 'Salud, alimentación y equilibrio',
        texto: 'Impulsamos iniciativas relacionadas con el bienestar integral, la alimentación y la sanación y autosanación.',
    },
    {
        titulo: 'Cooperación y comunidad',
        texto: 'Creamos espacios de encuentro y redes de colaboración basadas en el respeto, la hermandad y el bien común.',
    },
]

const proyectos = [
    {
        titulo: 'Encuentro y hermanamiento',
        texto: 'Reuniones, convivencias y espacios en los que compartir experiencias, necesidades e iniciativas.',
    },
    {
        titulo: 'Formación, divulgación y creatividad',
        texto: 'Cursos, talleres, publicaciones y actividades de arte, ciencia y expresión al servicio del desarrollo humano.',
    },
    {
        titulo: 'Salud y acompañamiento',
        texto: 'Prácticas y espacios orientados al equilibrio, la sanación y autosanación y el apoyo cercano entre personas.',
    },
    {
        titulo: 'Ayuda solidaria y cooperación',
        texto: 'Alimentos, recursos y redes de colaboración para responder a necesidades concretas y apoyar proyectos comunitarios.',
    },
]
</script>
