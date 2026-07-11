<template>
    <Sections white-first>

        <div>
            <div class="container w-full mx-auto mt-4 lg:mt-12 flex justify-end mb-10">
                <span />
                <Share />
            </div>
        </div>

        <!-- Hero -->
        <Section>
            <EvaluacionSeccion seccion="Hero ONG" :idoneidad="90" :claridad="85" :composicion="85" :posicion="90" />
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                    <div class="lg:col-span-6">
                        <div class="inline-block px-3 py-1 mb-5 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                            ONG MUNDO ARMÓNICO TSEYOR
                        </div>
                        <img src="/archivos/logos/logo-ong-mundo-armonico.JPG"
                            class="w-full h-auto max-w-[500px] mb-6" alt="ONG Mundo Armónico Tseyor"/>
                        <p class="text-base-content/60 text-base leading-relaxed font-light">
                            Un espacio de cooperación, desarrollo humano y evolución consciente, reconocido oficialmente y al servicio de la humanidad.
                        </p>
                    </div>
                    <div class="lg:col-span-6 space-y-5">
                        <div class="bg-base-100 rounded-3xl border border-base-300 p-6 shadow-sm">
                            <p class="text-sm text-base-content/80 leading-relaxed italic font-light">
                                "Se está preparando un gran cambio y ello comportará también grandes dificultades. Nuestro grupo, el grupo Tseyor, pondrá su granito de arena para paliarlas."
                            </p>
                        </div>
                        <div class="bg-base-100 rounded-3xl border border-base-300 p-6 shadow-sm border-l-4 border-l-primary">
                            <p class="text-sm text-base-content/80 leading-relaxed italic font-light">
                                "Habéis recibido cantidad de información; muchos estómagos necesitan de dicho alimento espiritual. Ahí también está una parte importante de la ayuda humanitaria."
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- Objetivos -->
        <Section>
            <EvaluacionSeccion seccion="Objetivos" :idoneidad="85" :claridad="80" :composicion="85" :posicion="85" />
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center max-w-3xl mx-auto mb-14">
                    <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                        NUESTRO PROPÓSITO
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight">
                        Objetivos de la ONG
                    </h2>
                    <p class="text-base-content/60 text-sm font-light mt-3 leading-relaxed">
                        El objeto general de esta Asociación es la cooperación en la consecución de una sociedad armónica, justa y solidaria, partiendo de una verdadera conciencia de la solidaridad, que contribuya a la participación social, a la reflexión sobre las causas de la injusticia y a la creación de sociedades armónicas.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 max-w-6xl mx-auto">
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Heart class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Ayuda humanitaria</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Proporcionar ayuda humanitaria a personas, grupos y colectivos: alimentación, medios, equipos, semillas y recursos de salud.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Leaf class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Desarrollo sostenible</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Cooperar a favor de un desarrollo humano sostenible que respete el equilibrio con el entorno natural.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <GraduationCap class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Formación cultural</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Aportar formación cultural, científica y técnica a personas y grupos que la demanden y no tengan acceso a la misma.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Hand class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Acción solidaria</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Realizar actividades que redunden en el beneficio de personas y colectivos, sobre todo de los más necesitados, y en casos de catástrofes.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <ClipboardList class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Gestión de proyectos</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Apoyo para la gestión de proyectos en comunidades empobrecidas que persigan un desarrollo endógeno sostenible.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Megaphone class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Sensibilización</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Sensibilizar a la población en valores de armonía, cooperación, solidaridad, justicia social y respeto entre los seres humanos.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group sm:col-span-2 lg:col-span-3 max-w-2xl mx-auto">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Sprout class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Salud y alimentación</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Proporcionar medios y formación en el ámbito de la salud, la alimentación, la agricultura, la expresión de ideas y sentimientos que despierten los potenciales creativos en armonía con el entorno natural.</p>
                    </div>
                </div>

                <div class="text-center mt-10">
                    <Link :href="estatutosUrl" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-neutral text-neutral-content font-bold text-xs uppercase tracking-widest hover:bg-primary transition-all shadow-sm">
                        Ver Estatutos completos
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </Section>

        <!-- Proyectos -->
        <Section>
            <EvaluacionSeccion seccion="Proyectos" :idoneidad="90" :claridad="85" :composicion="85" :posicion="85" />
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center max-w-3xl mx-auto mb-14">
                    <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                        INICIATIVAS DE LA ONG
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight">
                        Proyectos activos
                    </h2>
                    <p class="text-base-content/60 text-sm font-light mt-3 leading-relaxed">
                        Estos son los proyectos que impulsamos desde la ONG Mundo Armónico Tseyor para construir las bases de una Sociedad Armónica planetaria.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Users class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Crear espacios de reunión</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Donde poder retroalimentar ideas, pensamientos y proyectos.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Palette class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Espacios creativos</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Crear espacios creativos de arte y ciencia para la expresión libre del ser.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Handshake class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Redes de intercambio</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Tejer redes de intercambio de bienes y servicios entre la comunidad.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Heart class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Hermandad y amistad</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Fomentar la amistad y el hermanamiento en espacios seguros y protegidos.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <Globe class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Puentes intercivilizaciones</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Crear puentes de unión con civilizaciones de otros mundos.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <BookOpen class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Filosofía Tseyor</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Interiorizar el pensamiento mediante las prácticas de la filosofía Tseyor.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <HeartPulse class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Sanación y autosanación</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Preparar espacios de sanación mediante los canales oportunos.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 flex flex-col items-start gap-4 group">
                        <div class="w-11 h-11 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <UtensilsCrossed class="w-5 h-5" />
                        </div>
                        <h4 class="font-display font-bold text-base-content text-sm leading-snug">Alimentos solidarios</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Programas de alimentos para personas necesitadas.</p>
                    </div>
                </div>
            </div>
        </Section>

        <SectionBook
            titulo="Libro: ONG Mundo Armónico Tseyor"
            slug="la-ong-mundo-armonico-tseyor"
            portada="/almacen/medios/libros/227/La%20ONG%20Mundo%20Armonico%20Tseyor.jpg"
            enlace="/libros/la-ong-mundo-armonico-tseyor"
            buttonLabel="Ver en Biblioteca">
            <p>Todo el material recopilado de la ONG de Tseyor, desde julio de 2010 que la Confederación nos propuso su creación.</p>
        </SectionBook>

        <Section>
            <EvaluacionSeccion seccion="Voluntarios" :idoneidad="95" :claridad="90" :composicion="85" :posicion="90" />
            <TextImage title="Voluntarios TSEYOR" srcImage="/almacen/medios/paginas/quienes-somos.jpg" image-right>
                <p>Ser voluntario en TSEYOR es <strong>formar parte activa de una comunidad viva</strong>. No hay cargos ni jerarquías — todos somos aprendices, todos compartimos y crecemos juntos.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div class="bg-base-100 rounded-3xl border border-base-300 p-5 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300">
                        <h4 class="font-bold text-base text-base-content mb-1">Divulgación del mensaje</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Compartimos la filosofía cósmico-crística a través de conversaciones, escritos y encuentros.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 p-5 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300">
                        <h4 class="font-bold text-base text-base-content mb-1">Talleres vivenciales</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Participamos en experiencias prácticas de autodescubrimiento y conexión grupal.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 p-5 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300">
                        <h4 class="font-bold text-base text-base-content mb-1">Convivencias y viajes</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Compartimos momentos de encuentro presencial, aprendizaje colectivo y naturaleza.</p>
                    </div>
                    <div class="bg-base-100 rounded-3xl border border-base-300 p-5 shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300">
                        <h4 class="font-bold text-base text-base-content mb-1">Aprendizaje continuo</h4>
                        <p class="text-sm text-base-content/60 leading-relaxed font-light">Todos enseñamos y todos aprendemos. El Curso Holístico es nuestra guía compartida.</p>
                    </div>
                </div>
                <p class="text-base text-base-content/60 mt-4 italic font-light">No se necesita experiencia previa ni conocimientos especiales. Solo las ganas de crecer y servir.</p>
                <Link :href="route('cursos.inscripcion.nueva')" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-sm mt-4">
                    Quiero ser voluntario
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </TextImage>
        </Section>

        <Section>
            <TextImage title="Muular" srcImage="/almacen/medios/paginas/muular.png" buttonLabel="Conoce el Muular"
                :href="route('muular')" image-right>
                <p class="text-base text-base-content/60 font-light">La moneda social y espiritual de Tseyor</p>
            </TextImage>
        </Section>

    </Sections>
</template>

<script setup>
import {
    ArrowRight,
    Heart,
    Leaf,
    GraduationCap,
    Hand,
    ClipboardList,
    Megaphone,
    Sprout,
    Users,
    Palette,
    Handshake,
    Globe,
    BookOpen,
    HeartPulse,
    UtensilsCrossed,
} from 'lucide-vue-next'

defineProps({
    estatutosUrl: {
        type: String,
        required: false
    }
})
</script>
