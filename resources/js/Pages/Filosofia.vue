<template>
    <div class="container flex justify-between mx-auto px-4 py-8 md:py-12">
        <Back href="/quienes-somos" :floatAtY="-100">Quiénes Somos</Back>
        <Share />
    </div>

    <Sections height="550">

        <!-- 1. Introducción -->
        <Section>
            <div class="container mx-auto px-4 py-24 md:py-32">
                <div class="text-center max-w-5xl mx-auto">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Filosofía TSEYOR
                    </span>

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-black leading-[1.08] tracking-tight">
                        Una visión del despertar
                        <span class="text-primary">humano y cósmico</span>
                    </h1>

                    <p class="text-base sm:text-lg text-base-content/60 max-w-3xl mx-auto mt-8 leading-relaxed">
                        La filosofía TSEYOR reúne las referencias transmitidas por los
                        <strong>Guías Estelares de la Confederación de Mundos Habitados de la Galaxia</strong>
                        acerca del ser humano, su origen, el momento de transformación que vive nuestro planeta
                        y las posibilidades de evolución de nuestra consciencia.
                    </p>

                    <div class="max-w-3xl mx-auto mt-10 rounded-3xl bg-base-100/60 border border-base-300/30 px-7 py-6 sm:px-10 sm:py-8">
                        <p class="text-base-content/70 leading-relaxed">
                            Estas informaciones no se ofrecen para que sean aceptadas mediante la creencia.
                            Se presentan como referencias que cada persona puede conocer, considerar y,
                            mediante la interiorización y la experimentación, llegar a comprobar por sí misma.
                        </p>
                    </div>

                    <a
                        href="#vision-general"
                        class="inline-flex items-center gap-2 mt-10 text-xs font-bold uppercase tracking-[0.2em] text-primary hover:text-base-content transition-colors"
                    >
                        Descubrir la visión general
                        <Icon icon="ph:arrow-down-duotone" class="text-lg" />
                    </a>
                </div>
            </div>
        </Section>

        <!-- 2. Mapa general de la filosofía -->
        <Section>
            <div id="vision-general" class="container mx-auto px-4 py-24 md:py-32">
                <div class="text-center max-w-3xl mx-auto mb-16 md:mb-20">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Una síntesis
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                        Cuatro claves para comprender TSEYOR
                    </h2>
                    <p class="text-base-content/60 mt-6 leading-relaxed">
                        Un primer mapa para entender de dónde procede el mensaje, qué visión ofrece del ser humano,
                        cómo propone comprobarla y hacia qué transformación apunta.
                    </p>
                </div>

                <nav class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-6xl mx-auto" aria-label="Secciones de la filosofía TSEYOR">
                    <a
                        v-for="(clave, index) in claves"
                        :key="clave.ancla"
                        :href="'#' + clave.ancla"
                        class="group rounded-3xl bg-base-200/60 border border-base-300/30 p-6 hover:border-primary/30 hover:bg-base-200 hover:shadow-lg transition-all duration-300"
                    >
                        <div class="flex items-center justify-between mb-5">
                            <span class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-display font-black">
                                {{ index + 1 }}
                            </span>
                            <Icon :icon="clave.icono" class="text-2xl text-primary/70 group-hover:text-primary transition-colors" />
                        </div>
                        <h3 class="text-lg font-display font-bold mb-2 group-hover:text-primary transition-colors">
                            {{ clave.titulo }}
                        </h3>
                        <p class="text-sm text-base-content/55 leading-relaxed">
                            {{ clave.resumen }}
                        </p>
                    </a>
                </nav>
            </div>
        </Section>

        <!-- 3. Mensaje y momento cósmico -->
        <Section>
            <div id="mensaje-cosmico" class="container mx-auto px-4 py-24 md:py-32 scroll-mt-24">
                <div class="max-w-6xl mx-auto">
                    <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-12 lg:gap-20 items-start">
                        <div class="lg:sticky lg:top-28">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-8">
                                <Icon icon="ph:planet-duotone" class="text-3xl text-primary" />
                            </div>
                            <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-5 font-display">
                                El momento
                            </span>
                            <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                                Un mensaje desde las estrellas para un cambio de era
                            </h2>
                        </div>

                        <div class="space-y-6 text-base-content/65 leading-relaxed">
                            <p class="text-lg text-base-content/75">
                                Según los Guías Estelares, la humanidad se encuentra al final de un ciclo evolutivo
                                y ante una profunda transformación de la consciencia. Este proceso forma parte del
                                <strong>Plan Cósmico para la Tierra 1914-2054</strong> y conduce hacia una auténtica
                                <strong>revolución de los sentidos y de la consciencia</strong>.
                            </p>

                            <p>
                                Las comunicaciones anuncian la llegada del <strong>Rayo Sincronizador</strong>,
                                una energía cósmica que elevará la vibración del universo y favorecerá un salto evolutivo.
                                No se trataría únicamente de un cambio exterior, tecnológico o social, sino de una
                                transformación profunda de nuestra forma de pensar, sentir y comprender la realidad.
                            </p>

                            <p>
                                Los Guías de la Confederación acompañan a la humanidad aportando información,
                                herramientas y referencias. Sin embargo, respetan completamente nuestro libre albedrío:
                                no pueden realizar el trabajo por nosotros ni imponer el despertar.
                            </p>

                            <p>
                                La transformación ha de producirse desde el interior de cada persona y desde el corazón
                                mismo de la sociedad, pacíficamente, mediante el ejemplo, la amistad, el servicio y el amor.
                            </p>

                            <div class="rounded-3xl border border-base-300/30 bg-base-100 p-7 mt-10">
                                <p class="text-sm font-bold uppercase tracking-widest text-base-content/40 mb-4">
                                    Ideas esenciales
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="idea in ideasMomento" :key="idea" class="px-3 py-1.5 rounded-full bg-base-200 border border-base-300/50 text-sm text-base-content/65">
                                        <Referencia class="cursor-help">{{ idea }}</Referencia>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 4. Naturaleza del ser humano -->
        <Section>
            <div id="naturaleza-humana" class="container mx-auto px-4 py-24 md:py-32 scroll-mt-24">
                <div class="max-w-6xl mx-auto">
                    <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-12 lg:gap-20 items-start">
                        <div class="space-y-6 text-base-content/65 leading-relaxed order-2 lg:order-1">
                            <p class="text-lg text-base-content/75">
                                TSEYOR plantea que el ser humano no es únicamente su cuerpo, su personalidad o su
                                existencia cotidiana. Es un ser cósmico, espiritual y multidimensional, una expresión
                                de la consciencia del Absoluto que participa de una realidad universal y holográfica.
                            </p>

                            <p>
                                Los Guías utilizan el término <strong>atlante</strong> para referirse al ser humano que
                                "piensa que piensa": aquel que empieza a observar su propio pensamiento y a preguntarse
                                verdaderamente quién es, de dónde viene y hacia dónde va.
                            </p>

                            <p>
                                Según estas referencias, vivimos temporalmente en un escenario de realidad limitada,
                                un <strong>paréntesis</strong> en el que hemos olvidado nuestra auténtica naturaleza.
                                El mundo visible sería solamente una parte de la realidad. Junto a él existe una dimensión
                                adimensional o interdimensional, a la que también accedemos de manera todavía inconsciente
                                durante el sueño, la intuición, la inspiración o determinados estados de consciencia.
                            </p>

                            <p>
                                El despertar consiste en recuperar conscientemente esa conexión y reconocer nuestra
                                naturaleza profunda, nuestra chispa divina o <strong>Cristo Cósmico interior</strong>.
                            </p>

                            <div class="rounded-3xl bg-gradient-to-br from-primary/10 to-base-100 border border-primary/15 p-7 sm:p-9 mt-10">
                                <div class="flex gap-5 items-start">
                                    <Icon icon="ph:sparkle-duotone" class="text-3xl text-primary shrink-0 mt-1" />
                                    <p class="text-base-content/70">
                                        Las comunicaciones hablan también de capacidades hoy latentes: la extrapolación
                                        del pensamiento, la navegación consciente por otras realidades, la clarividencia,
                                        la regeneración del cuerpo y una creatividad capaz de actuar desde el mundo
                                        adimensional. Es el proceso que TSEYOR denomina <strong>especialización</strong>,
                                        relacionado igualmente con la alquimia y la magia blanca entendidas como
                                        transformación consciente y orientada al bien común.
                                    </p>
                                </div>
                            </div>

                            <div class="rounded-3xl bg-base-200/60 border border-base-300/30 p-7 mt-10">
                                <p class="text-sm font-bold uppercase tracking-widest text-base-content/40 mb-4">
                                    Ideas esenciales
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="idea in ideasNaturaleza" :key="idea" class="px-3 py-1.5 rounded-full bg-base-200 border border-base-300/50 text-sm text-base-content/65">
                                        <Referencia class="cursor-help">{{ idea }}</Referencia>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="lg:sticky lg:top-28 order-1 lg:order-2 lg:text-right">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-8 lg:ml-auto">
                                <Icon icon="ph:sparkle-duotone" class="text-3xl text-primary" />
                            </div>
                            <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-5 font-display">
                                Nuestra naturaleza
                            </span>
                            <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                                El ser humano es mucho más de lo que aparenta
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 5. Interiorización y comprobación -->
        <Section>
            <div id="comprobacion" class="container mx-auto px-4 py-24 md:py-32 scroll-mt-24">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center max-w-4xl mx-auto mb-16 md:mb-20">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-8">
                            <Icon icon="ph:eye-duotone" class="text-3xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-5 font-display">
                            El camino
                        </span>
                        <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                            Un camino de interiorización y comprobación
                        </h2>
                        <p class="text-base-content/60 mt-6 leading-relaxed">
                            El conocimiento intelectual de estas ideas no constituye por sí solo el despertar.
                            La propuesta es convertir la información recibida en experiencia consciente.
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-5 max-w-5xl mx-auto">
                        <article v-for="paso in camino" :key="paso.titulo" class="rounded-3xl bg-base-200/60 border border-base-300/30 p-7 sm:p-8">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6">
                                <Icon :icon="paso.icono" class="text-2xl text-primary" />
                            </div>
                            <h3 class="text-xl font-display font-bold mb-3">{{ paso.titulo }}</h3>
                            <p class="text-base-content/60 leading-relaxed">{{ paso.texto }}</p>
                        </article>
                    </div>

                    <div class="max-w-4xl mx-auto mt-12 rounded-3xl bg-base-100 border border-base-300 p-8 sm:p-10 text-center shadow-sm">
                        <p class="text-base-content/70 leading-relaxed">
                            La experimentación puede apoyarse en la meditación, los talleres, el trabajo con el símbolo,
                            el Juul, los sueños, la extrapolación mental y otras herramientas transmitidas en TSEYOR.
                            Sin embargo, ninguna técnica sustituye a la autoobservación cotidiana, la sinceridad y la
                            transformación del propio pensamiento.
                        </p>
                        <p class="text-lg font-display font-bold text-primary mt-7">
                            Conocer, experimentar, contrastar y obtener las propias conclusiones.
                        </p>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 6. Transformación individual y colectiva -->
        <Section>
            <div id="sociedades-armonicas" class="container mx-auto px-4 py-24 md:py-32 scroll-mt-24">
                <div class="max-w-6xl mx-auto">
                    <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-12 lg:gap-20 items-start">
                        <div class="lg:sticky lg:top-28">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-8">
                                <Icon icon="ph:users-three-duotone" class="text-3xl text-primary" />
                            </div>
                            <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-5 font-display">
                                La finalidad
                            </span>
                            <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                                Despertar juntos para crear otro mundo
                            </h2>
                        </div>

                        <div class="space-y-6 text-base-content/65 leading-relaxed">
                            <p class="text-lg text-base-content/75">
                                Aunque el trabajo interior es personal e intransferible, no se plantea como un camino
                                individualista. Los comunicados insisten en que el ser humano avanza mediante la
                                <strong>hermandad</strong>, la retroalimentación y el reconocimiento de los demás como
                                espejos de sí mismo.
                            </p>

                            <p>
                                La unidad de pensamiento no significa que todos deban pensar igual. Significa descubrir
                                una unidad profunda dentro de la diversidad, respetando las pequeñas diferencias de cada
                                persona y colaborando desde el amor, la igualdad y el bien común.
                            </p>

                            <p>
                                El autodescubrimiento no tiene como finalidad engrandecer al individuo ni adquirir
                                facultades para beneficio propio. Su sentido es contribuir al despertar de toda la humanidad
                                y participar conscientemente en una cadena universal de ayuda. Quien recibe ayuda se prepara
                                también para ayudar.
                            </p>

                            <p>
                                Esta transformación interior y colectiva conduce hacia las
                                <strong>Sociedades Armónicas</strong>: comunidades humanas basadas en la cooperación,
                                el equilibrio, la libertad, la ausencia de imposición, el compartir y la ayuda humanitaria
                                material y espiritual.
                            </p>

                            <div class="rounded-3xl bg-gradient-to-br from-primary/15 to-primary/5 border border-primary/20 p-8 sm:p-10 mt-10">
                                <p class="text-xl sm:text-2xl font-display font-bold leading-snug">
                                    Esta es la revolución que propone TSEYOR: una revolución pacífica de los sentidos y de
                                    la consciencia, nacida de la transformación del pensamiento y manifestada en el hermanamiento.
                                </p>
                            </div>

                            <div class="rounded-3xl bg-base-200/60 border border-base-300/30 p-7 mt-10">
                                <p class="text-sm font-bold uppercase tracking-widest text-base-content/40 mb-4">
                                    Ideas esenciales
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <span v-for="idea in ideasSociedad" :key="idea" class="px-3 py-1.5 rounded-full bg-base-100 border border-base-300/50 text-sm text-base-content/65">
                                        <Referencia class="cursor-help">{{ idea }}</Referencia>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 7. Temas para profundizar -->
        <Section v-if="temasDestacados.length">
            <div id="temas-clave" class="container mx-auto px-4 py-24 md:py-32">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Sigue explorando
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                        Profundiza en los temas clave
                    </h2>
                    <p class="text-base-content/60 mt-6 leading-relaxed">
                        Esta página ofrece solamente un atisbo. Cada uno de estos conceptos se desarrolla ampliamente
                        en la literatura y en los comunicados de TSEYOR.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 max-w-5xl mx-auto">
                    <Link
                        v-for="pagina in temasDestacados"
                        :key="pagina.ruta"
                        :href="'/' + pagina.ruta"
                        class="group bg-base-200/60 backdrop-blur-sm rounded-3xl border border-base-300/30 overflow-hidden hover:border-primary/30 hover:bg-base-200 hover:shadow-lg transition-all duration-300"
                    >
                        <div v-if="pagina.imagen" class="w-full h-36 overflow-hidden bg-base-200">
                            <img
                                :src="pagina.imagen"
                                :alt="pagina.titulo"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            >
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold font-display mb-2 group-hover:text-primary transition-colors">
                                {{ pagina.titulo }}
                            </h3>
                            <p class="text-sm text-base-content/60 leading-relaxed">
                                {{ pagina.descripcion }}
                            </p>
                            <span class="inline-flex items-center gap-2 mt-5 text-xs font-bold uppercase tracking-widest text-primary">
                                Leer más
                                <Icon icon="ph:arrow-right-duotone" class="text-base group-hover:translate-x-1 transition-transform" />
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
        </Section>

        <!-- 8. Invitación final -->
        <Section>
            <div class="container mx-auto px-4 py-24 md:py-32">
                <div class="max-w-5xl mx-auto text-center">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Una invitación abierta
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight">
                        No se trata de creer.<br>
                        <span class="text-primary">Se trata de descubrir.</span>
                    </h2>

                    <div class="bg-base-200/60 rounded-3xl p-8 sm:p-10 md:p-14 border border-base-300/30 text-left max-w-4xl mx-auto mt-12 space-y-6">
                        <p class="text-base-content/70 leading-relaxed text-lg">
                            TSEYOR ofrece una visión amplia del ser humano y del universo. Sus referencias pueden
                            resultar nuevas, sorprendentes o difíciles de comprender inicialmente. No se pide aceptarlas
                            de antemano ni renunciar al propio discernimiento.
                        </p>
                        <p class="text-base-content/70 leading-relaxed">
                            La invitación es a conocerlas, mantenerlas como una posibilidad y emprender un proceso personal
                            de interiorización y experimentación. Porque el verdadero conocimiento no nace de repetir lo que
                            otros afirman, sino de descubrirlo conscientemente en uno mismo.
                        </p>
                    </div>

                    <p class="text-xl sm:text-2xl font-display font-bold mt-12">
                        Conoce las referencias.
                        <span class="text-primary">Experimenta. Contrasta.</span>
                        Llega a tus propias conclusiones.
                    </p>

                    <div class="flex flex-wrap justify-center gap-4 mt-10">
                        <Link
                            :href="route('biblioteca')"
                            class="btn btn-primary px-9 py-4 text-sm font-bold uppercase tracking-wider rounded-full shadow-lg hover:shadow-xl transition-all"
                        >
                            Explorar la biblioteca
                            <Icon icon="ph:books-duotone" class="text-lg" />
                        </Link>
                        <Link
                            :href="route('cursos')"
                            class="btn btn-secondary px-8 py-4 rounded-full"
                        >
                            Conocer el Curso Holístico
                        </Link>
                    </div>
                </div>
            </div>
        </Section>

    </Sections>

</template>

<script setup>
const props = defineProps({
    paginasDescubre: {
        type: Array,
        default: () => [],
    },
})

const claves = [
    {
        ancla: 'mensaje-cosmico',
        titulo: 'El mensaje y el momento',
        resumen: 'El Plan Cósmico, el final de ciclo y la revolución de la consciencia.',
        icono: 'ph:planet-duotone',
    },
    {
        ancla: 'naturaleza-humana',
        titulo: 'Nuestra naturaleza',
        resumen: 'El ser humano atlante, multidimensional y conectado con el Absoluto.',
        icono: 'ph:sparkle-duotone',
    },
    {
        ancla: 'comprobacion',
        titulo: 'El camino interior',
        resumen: 'Autoobservación, humildad, equilibrio, experimentación y comprobación.',
        icono: 'ph:eye-duotone',
    },
    {
        ancla: 'sociedades-armonicas',
        titulo: 'La transformación colectiva',
        resumen: 'Hermandad, unidad en la diversidad, ayuda y Sociedades Armónicas.',
        icono: 'ph:users-three-duotone',
    },
]

const ideasMomento = [
    'Guías Estelares',
    'Confederación de Mundos',
    'Plan Cósmico',
    'Final de ciclo',
    'Rayo Sincronizador',
    'Salto cuántico',
]

const ideasNaturaleza = [
    'Filosofía cuántica',
    'Universo holográfico',
    'Ser humano atlante',
    'Paréntesis',
    'Interdimensionalidad',
    'Especialización',
    'Facultades superiores',
]

const ideasSociedad = [
    'Amor',
    'Hermandad',
    'Unidad en la diversidad',
    'Retroalimentación',
    'Ayuda humanitaria',
    'Sociedades Armónicas',
]

const camino = [
    {
        titulo: 'Autoobservación',
        texto: 'Contemplar pensamientos, emociones y reacciones sin justificarlos ni rechazarlos, reconociendo la influencia del ego, del miedo, de los apegos y del medio.',
        icono: 'ph:eye-duotone',
    },
    {
        titulo: 'Humildad y equilibrio',
        texto: 'Aceptar que sabemos muy poco, abrirnos a nuevas posibilidades sin dogmatismo y armonizar mente, cuerpo y espíritu.',
        icono: 'ph:scales-duotone',
    },
    {
        titulo: 'Interiorización',
        texto: 'Aquietar el pensamiento recurrente mediante la contemplación y abrir una puerta hacia nuestra consciencia profunda.',
        icono: 'ph:spiral-duotone',
    },
    {
        titulo: 'Autodescubrimiento',
        texto: 'No tanto descubrir algo nuevo como recordar aquello que esencialmente somos y expresarlo en nuestra vida cotidiana.',
        icono: 'ph:compass-duotone',
    },
]

const ordenTemas = [
    'confederacion-de-mundos-habitados-de-la-galaxia',
    'el-rayo-sincronizador',
    'salto-cuantico',
    'especializacion',
    'la-autoobservacion',
    'retroalimentacion',
    'espejos',
    'las-sociedades-armonicas',
]

const temasDestacados = computed(() =>
    ordenTemas
        .map(ruta => props.paginasDescubre.find(pagina => pagina.ruta === ruta))
        .filter(Boolean)
)
</script>


