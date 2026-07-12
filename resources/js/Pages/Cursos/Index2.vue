<template>
    <Sections height="500">

        <!-- 1. Hero: conecta con la portada -->
        <Section class="py-12 md:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-2 gap-10 items-center">
                    <div>
                        <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-primary/70 font-display mb-3">
                            <Users class="w-3.5 h-3.5 text-primary" />
                            +1.200 personas han realizado este curso
                        </span>
                        <h1 class="text-4xl sm:text-5xl font-display font-black tracking-tight leading-tight mb-4">
                            Curso Holístico Tseyor
                        </h1>
                        <p class="text-lg text-base-content/70 leading-relaxed mb-4">
                            La puerta de entrada a una filosofía viva, unas herramientas de autoconocimiento y una comunidad que te acompaña. Un curso tutelado por los Guías Estelares, donde todos aprendemos de todos.
                        </p>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 mb-6">
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                100% gratuito
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                Tutelado
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                En comunidad
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/70">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                7 capítulos
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary rounded-full px-8 shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                 Quiero inscribirme
                                 <ArrowRight class="w-4 h-4 ml-1" />
                             </Link>
                            <a href="#material-del-curso" @click.prevent="scrollToMaterial" class="w-full sm:w-auto text-center py-3.5 px-8 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs uppercase tracking-widest transition-all shadow-sm">
                                Ver el material del curso
                            </a>
                        </div>
                    </div>
                    <div class="hidden md:flex justify-center">
                        <img src="/almacen/medios/paginas/curso.png" alt="Curso Holístico Tseyor" class="max-h-96 object-contain drop-shadow-2xl" />
                    </div>
                </div>
            </div>
        </Section>

        <!-- 2. Qué aprenderás -->
        <Section class="py-16 md:py-20 bg-base-200/50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-block text-xs font-bold tracking-widest uppercase text-primary/60 font-display mb-2">Contenido</span>
                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-3">Qué aprenderás</h2>
                <p class="text-base-content/60 max-w-xl mx-auto mb-10 sm:mb-14">
                    El curso recorre las enseñanzas fundamentales que los Guías Estelares han transmitido a lo largo de más de 40 años.
                </p>

                <div class="grid sm:grid-cols-2 gap-5 max-w-4xl mx-auto text-left">
                    <div class="p-6 rounded-2xl bg-base-100 border border-base-200/50 shadow-sm flex items-start gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 mt-1">
                            <Icon icon="hugeicons:scroll-01" class="w-7 h-7" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">Los mensajes de los Guías</h3>
                            <p class="text-sm text-base-content/60 leading-relaxed">Conocerás qué son los Guías Estelares, su papel como tutores y el contenido de sus comunicaciones.</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-base-100 border border-base-200/50 shadow-sm flex items-start gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 mt-1">
                            <Icon icon="lucide:heart" class="w-7 h-7" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">La filosofía cósmico-crística</h3>
                            <p class="text-sm text-base-content/60 leading-relaxed">Los principios de hermandad galáctica, el despertar de la conciencia y las Sociedades Armónicas.</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-base-100 border border-base-200/50 shadow-sm flex items-start gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 mt-1">
                            <Icon icon="hugeicons:idea" class="w-7 h-7" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">Herramientas de autoconocimiento</h3>
                            <p class="text-sm text-base-content/60 leading-relaxed">Prácticas de meditación, reflexión interior y trabajo personal para tu propio descubrimiento.</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-base-100 border border-base-200/50 shadow-sm flex items-start gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 mt-1">
                            <Icon icon="hugeicons:hand-helping" class="w-7 h-7" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">Organización y voluntariado</h3>
                            <p class="text-sm text-base-content/60 leading-relaxed">Cómo está estructurada TSEYOR como ONG, sus estamentos, su funcionamiento y cómo integrarte si así lo deseas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 3. Cómo funciona -->
        <Section class="py-16">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-block text-xs font-bold tracking-widest uppercase text-primary/60 font-display mb-2">Experiencia tutelada</span>
                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-10">Cómo se vive el curso</h2>

                <div class="grid sm:grid-cols-3 gap-8 text-left">
                    <div class="flex flex-col items-start gap-3 p-6 rounded-2xl bg-base-100 border border-base-200/60">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">1</div>
                        <h3 class="font-display font-bold text-lg">Te inscribes</h3>
                        <p class="text-sm text-base-content/60 leading-relaxed">Rellena el formulario y nos pondremos en contacto contigo para empezar el curso.</p>
                    </div>
                    <div class="flex flex-col items-start gap-3 p-6 rounded-2xl bg-base-100 border border-base-200/60">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">2</div>
                        <h3 class="font-display font-bold text-lg">Lectura atenta del libro</h3>
                        <p class="text-sm text-base-content/60 leading-relaxed">El material principal del curso es el libro de los Guías Estelares. Su lectura atenta, en compañía, es el corazón de la experiencia.</p>
                    </div>
                    <div class="flex flex-col items-start gap-3 p-6 rounded-2xl bg-base-100 border border-base-200/60">
                        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">3</div>
                        <h3 class="font-display font-bold text-lg">Recibes tu nombre simbólico</h3>
                        <p class="text-sm text-base-content/60 leading-relaxed">Al completar el curso, los Guías Estelares te otorgan tu nombre simbólico dentro de TSEYOR.</p>
                    </div>
                </div>

                <div class="mt-10 max-w-2xl mx-auto px-6 py-5 rounded-2xl bg-primary/5 border border-primary/10 text-center">
                    <p class="text-base sm:text-lg font-display font-semibold text-primary/80 leading-relaxed">
                        «Un curso vivo, donde la lectura se vive en un ambiente de confraternidad y descubrimiento compartido. Aquí todos somos alumnos.»
                    </p>
                </div>
            </div>
        </Section>

        <!-- 4. El material del curso -->
        <Section id="material-del-curso" class="py-16 bg-base-200/50 scroll-mt-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Material principal -->
                <div class="grid md:grid-cols-2 gap-10 items-center">
                    <div class="flex justify-center">
                        <Libro3d :libro="libro" alt="Curso Holístico Tseyor" />
                    </div>
                    <div>
                        <span class="inline-block text-xs font-bold tracking-widest uppercase text-primary font-display mb-2">El libro del curso</span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-4">Curso Holístico Tseyor</h2>
                        <p class="text-base text-base-content/70 leading-relaxed mb-4">
                            El curso se basa en un libro de relatos y enseñanzas transmitidas por los Guías Estelares. Cada capítulo abre una puerta a la reflexión, el autoconocimiento y el diálogo compartido.
                        </p>
                        <p class="text-sm text-base-content/50 mb-6">
                            Puedes descargarlo gratuitamente.
                        </p>
                        <Link href="/libros/curso-holistico-tseyor" class="btn btn-primary rounded-full px-8 shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105">
                            Descargar el libro
                        </Link>
                    </div>
                </div>

                <!-- Separador -->
                <div class="flex items-center gap-4 my-16">
                    <div class="flex-1 h-px bg-base-300"></div>
                    <span class="text-xs font-bold tracking-widest uppercase text-base-content/30">Material complementario</span>
                    <div class="flex-1 h-px bg-base-300"></div>
                </div>

                <!-- Material complementario -->
                <div class="grid md:grid-cols-2 gap-10 items-center">
                    <div class="flex justify-center md:order-2">
                        <Libro3d :libro="libroGuias" alt="Los Guías Estelares" />
                    </div>
                    <div class="md:order-1">
                        <h3 class="text-2xl sm:text-3xl font-display font-bold tracking-tight mb-3">Libro de acompañamiento</h3>
                        <p class="text-sm text-base-content/60 leading-relaxed mb-4">
                            Una monografía que presenta a los Guías Estelares como seres de otros mundos que se comunican con el grupo Tseyor, con más de 40 años de experiencias de contactismo, ilustraciones obtenidas por extrapolación mental y una galería fotográfica del grupo.
                        </p>
                        <p class="text-sm text-base-content/40 mb-6">
                            Lectura complementaria gratuita.
                        </p>
                        <Link :href="`/libros/${libroGuias.slug}`" class="btn btn-ghost rounded-full border border-base-300 hover:border-primary/40 hover:bg-base-200 transition-all">
                            Descargar
                        </Link>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 5. CTA final -->
        <Section class="py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/80 to-primary/85"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl p-8 sm:p-14 lg:p-20 text-center text-white relative overflow-hidden">
                    <div class="max-w-2xl mx-auto">
                        <span class="inline-block text-xs font-bold tracking-widest uppercase text-white/50 font-display mb-4">
                            EMPIEZA CUANDO QUIERAS
                        </span>
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-black tracking-tight leading-tight mb-6 text-white">
                            Inscríbete al curso gratuito
                        </h2>
                        <p class="text-white/70 leading-relaxed mb-10 text-sm sm:text-base font-light max-w-xl mx-auto">
                            Sin compromiso, sin dogmas, sin coste. Solo tú y tu experiencia.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link :href="route('cursos.inscripcion.nueva')" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest shadow-lg transition-all duration-300 hover:bg-base-200 hover:shadow-xl transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                Inscribirme gratis
                                <ArrowRight class="w-4 h-4 inline ml-1.5 -mt-0.5" />
                            </Link>
                            <Link :href="route('biblioteca')" class="w-full sm:w-auto px-6 py-4 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-widest transition-all text-center backdrop-blur-sm hover:scale-105">
                                Explorar contenidos libres
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

    </Sections>
</template>

<script setup>

import { ArrowRight, CheckCircle2, Users } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

defineProps({
    libro: {
        type: Object,
        required: true,
    },
    libroGuias: {
        type: Object,
        required: true,
    },
    proximosCursos: {
        type: Array,
        default: () => [],
    },
})

function scrollToMaterial() {
    document.getElementById('material-del-curso')?.scrollIntoView({ behavior: 'smooth' })
}
</script>
