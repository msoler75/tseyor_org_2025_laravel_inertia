<template>
    <div class="container flex justify-between mx-auto px-4 py-8 md:py-12">
        <span></span>
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

                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-display font-black leading-[1.08] tracking-tight text-balance">
                        Una visión del despertar
                        <span class="text-primary">humano y cósmico</span>
                    </h1>

                    <p class="text-base sm:text-lg text-base-content/75 max-w-3xl mx-auto mt-8 leading-relaxed">
                        La filosofía TSEYOR reúne las referencias transmitidas por los
                        <strong>Guías Estelares de la Confederación de Mundos Habitados de la Galaxia</strong>
                        acerca del ser humano, su origen, el momento de transformación que vive nuestro planeta
                        y las posibilidades de evolución de nuestra consciencia.
                    </p>

                    <div class="max-w-3xl mx-auto mt-10 rounded-3xl bg-gradient-to-br from-primary/[0.07] to-base-100 border border-primary/10 px-7 py-6 sm:px-10 sm:py-8">
                        <p class="text-base-content/75 leading-relaxed">
                            Estas informaciones no se ofrecen para que sean aceptadas mediante la creencia.
                            Se presentan como referencias que cada persona puede conocer, considerar y,
                            mediante la interiorización y la experimentación, llegar a comprobar por sí misma.
                        </p>
                    </div>

                    <a
                        href="#vision-general"
                        @click.prevent="scrollAVision"
                        class="inline-flex items-center gap-2 mt-10 text-xs font-bold uppercase tracking-[0.2em] text-primary hover:text-base-content transition-colors"
                    >
                        Descubrir la visión general
                        <Icon icon="ph:arrow-down-duotone" class="text-lg" />
                    </a>
                </div>
            </div>
        </Section>

        <!-- 2-6. Síntesis general -->
        <div id="vision-general" class="container mx-auto px-4 py-24 md:py-32">
            <div class="grid lg:grid-cols-[220px_1fr] gap-12 lg:gap-20">

                <!-- Left: sticky index (desktop) + select (mobile) -->
                <div>
                    <!-- Mobile nav: select dropdown -->
                    <div class="lg:hidden mb-8">
                        <label class="text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-3 block font-display">
                            En esta página
                        </label>
                        <select
                            @change="saltarASeccion"
                            class="select select-bordered w-full bg-base-100 border-base-300 rounded-xl text-sm font-display font-bold"
                            aria-label="Secciones de la filosofía"
                        >
                            <option v-for="clave in claves" :key="clave.ancla" :value="clave.ancla">
                                {{ clave.titulo }}
                            </option>
                        </select>
                    </div>

                    <!-- Desktop nav -->
                    <nav class="hidden lg:block lg:sticky lg:top-28 lg:self-start space-y-1" aria-label="Secciones de la filosofía TSEYOR">
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

                <!-- Right: narrative blocks -->
                <div class="space-y-24">
                    <!-- Momento cósmico -->
                    <article id="mensaje-cosmico" class="scroll-mt-24">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                            <Icon icon="ph:planet-duotone" class="text-2xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            El momento
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                            Un mensaje desde las estrellas para un cambio de era
                        </h2>

                        <!-- Preview: conceptos clave -->
                        <div class="flex flex-wrap gap-2 mb-8">
                            <span v-for="idea in ideasMomento" :key="idea" class="px-4 py-2 rounded-full bg-primary/5 border border-primary/15 text-sm text-primary font-medium">
                                <Referencia class="cursor-help">{{ idea }}</Referencia>
                            </span>
                        </div>

                        <div class="space-y-6 text-base-content/75 leading-relaxed max-w-3xl">
                            <p class="text-lg text-base-content/80">
                                Según los Guías Estelares, la humanidad se encuentra al final de un ciclo evolutivo
                                y ante una profunda transformación de la consciencia. Este proceso forma parte del
                                <strong>Plan Cósmico para la Tierra 1914-2054</strong> y conduce hacia una auténtica
                                <strong>revolución de los sentidos y de la consciencia</strong>.
                            </p>

                            <p>
                                Los Guías Estelares anuncian la llegada del <strong>Rayo Sincronizador</strong>,
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

                            <!-- Transición: pregunta puente -->
                            <div class="flex gap-3 mt-8 pt-6 border-t border-base-200/60">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                                </div>
                                <p class="text-sm text-base-content/50 italic leading-relaxed">
                                    "Si existe un mensaje cósmico, ¿quién es el ser humano al que va dirigido?"
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- Naturaleza del ser humano -->
                    <article id="naturaleza-humana" class="scroll-mt-24">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                            <Icon icon="ph:sparkle-duotone" class="text-2xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            Nuestra naturaleza
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                            El ser humano es mucho más de lo que aparenta
                        </h2>

                        <!-- Preview: conceptos clave -->
                        <div class="flex flex-wrap gap-2 mb-8">
                            <span v-for="idea in ideasNaturaleza" :key="idea" class="px-4 py-2 rounded-full bg-primary/5 border border-primary/15 text-sm text-primary font-medium">
                                <Referencia class="cursor-help">{{ idea }}</Referencia>
                            </span>
                        </div>

                        <div class="space-y-6 text-base-content/75 leading-relaxed max-w-3xl">
                            <p class="text-lg text-base-content/80">
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

                            <div class="rounded-3xl bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/15 p-7 sm:p-9 mt-10">
                                <div class="flex gap-5 items-start">
                                    <Icon icon="ph:sparkle-duotone" class="text-3xl text-primary shrink-0 mt-1" />
                                    <p class="text-base-content/75">
                                        Los Guías Estelares hablan también de capacidades hoy latentes: la extrapolación
                                        del pensamiento, la navegación consciente por otras realidades, la clarividencia,
                                        la regeneración del cuerpo y una creatividad capaz de actuar desde el mundo
                                        adimensional. Es el proceso que TSEYOR denomina <strong>especialización</strong>:
                                        el desarrollo consciente de esas facultades superiores, orientado al bien común
                                        y a la transformación de uno mismo y del entorno.
                                    </p>
                                </div>
                            </div>

                            <!-- Transición: pregunta puente -->
                            <div class="flex gap-3 mt-8 pt-6 border-t border-base-200/60">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                                </div>
                                <p class="text-sm text-base-content/50 italic leading-relaxed">
                                    "Si somos más de lo que aparentamos, ¿cómo se explora y verifica todo esto?"
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- Interiorización y comprobación -->
                    <article id="comprobacion" class="scroll-mt-24">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mb-6">
                            <Icon icon="ph:eye-duotone" class="text-2xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            El camino
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                            Un camino de interiorización y comprobación
                        </h2>

                        <!-- Preview: conceptos clave -->
                        <div class="flex flex-wrap gap-2 mb-8">
                            <span v-for="idea in ideasCamino" :key="idea" class="px-4 py-2 rounded-full bg-primary/5 border border-primary/15 text-sm text-primary font-medium">
                                <Referencia class="cursor-help">{{ idea }}</Referencia>
                            </span>
                        </div>

                        <p class="text-base-content/75 mb-8 leading-relaxed max-w-3xl">
                            El conocimiento intelectual de estas ideas no constituye por sí solo el despertar.
                            La propuesta es convertir la información recibida en experiencia consciente.
                        </p>

                        <div class="grid md:grid-cols-2 gap-5 max-w-3xl">
                            <article v-for="paso in camino" :key="paso.titulo" class="rounded-3xl bg-base-100 border border-base-300/30 p-7 sm:p-8">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6">
                                    <Icon :icon="paso.icono" class="text-2xl text-primary" />
                                </div>
                                <h3 class="text-xl font-display font-bold mb-3">{{ paso.titulo }}</h3>
                                <p class="text-base-content/75 leading-relaxed">{{ paso.texto }}</p>
                            </article>
                        </div>

                        <div class="max-w-3xl mt-12 rounded-3xl bg-base-100 border border-base-300 p-8 sm:p-10 shadow-sm">
                            <p class="text-base-content/75 leading-relaxed">
                                La experimentación puede apoyarse en la meditación, los talleres, el trabajo con el símbolo,
                                el <strong>Juul</strong> (palabra maya que significa "rayo de luz", una energía vinculada al despertar de la consciencia transmitida por los Guías),
                                los sueños, la extrapolación mental y otras herramientas transmitidas en TSEYOR.
                                Sin embargo, ninguna técnica sustituye a la autoobservación cotidiana, la sinceridad y la
                                transformación del propio pensamiento.
                            </p>
                            <p class="text-lg font-display font-bold text-primary mt-7">
                                Conocer, experimentar, contrastar y obtener las propias conclusiones.
                            </p>
                        </div>

                        <!-- Transición: pregunta puente -->
                        <div class="flex gap-3 mt-8 pt-6 border-t border-base-200/60">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                <Icon icon="ph:chat-circle-duotone" class="text-primary text-sm" />
                            </div>
                            <p class="text-sm text-base-content/50 italic leading-relaxed">
                                "Y cuando una persona despierta, ¿qué ocurre con quienes le rodean?"
                            </p>
                        </div>
                    </article>

                    <!-- Transformación individual y colectiva (última sección, tratamiento visual diferenciado) -->
                    <article id="sociedades-armonicas" class="scroll-mt-24">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/30 to-secondary/10 flex items-center justify-center mb-6 shadow-sm">
                            <Icon icon="ph:users-three-duotone" class="text-3xl text-primary" />
                        </div>
                        <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-4 font-display">
                            La finalidad
                        </span>
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-black leading-[1.1] tracking-tight mb-6 text-balance">
                            Despertar juntos para crear otro mundo
                        </h2>

                        <!-- Preview: conceptos clave -->
                        <div class="flex flex-wrap gap-2 mb-8">
                            <span v-for="idea in ideasSociedad" :key="idea" class="px-4 py-2 rounded-full bg-primary/5 border border-primary/15 text-sm text-primary font-medium">
                                <Referencia class="cursor-help">{{ idea }}</Referencia>
                            </span>
                        </div>

                        <div class="space-y-6 text-base-content/75 leading-relaxed max-w-3xl">
                            <p class="text-lg text-base-content/80">
                                Aunque el trabajo interior es personal e intransferible, no se plantea como un camino
                                individualista. Los Guías Estelares insisten en que el ser humano avanza mediante la
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

                            <div class="rounded-3xl bg-gradient-to-br from-primary/15 via-primary/10 to-secondary/5 border border-primary/20 p-8 sm:p-10 mt-10 shadow-sm">
                                <p class="text-xl sm:text-2xl font-display font-bold leading-snug">
                                    Esta es la revolución que propone TSEYOR: una revolución pacífica de los sentidos y de
                                    la consciencia, nacida de la transformación del pensamiento y manifestada en el hermanamiento.
                                </p>
                            </div>

                        </div>
                    </article>
                </div>
            </div>
        </div>

        <!-- 7. Invitación final -->
        <Section>
            <div class="container mx-auto px-4 py-24 md:py-32">
                <div class="max-w-5xl mx-auto text-center">
                    <span class="inline-block text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display">
                        Una invitación abierta
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-display font-black leading-[1.1] tracking-tight text-balance">
                        No se trata de creer.<br>
                        <span class="text-primary">Se trata de descubrir.</span>
                    </h2>

                    <div class="bg-gradient-to-br from-base-200/80 to-base-100 rounded-3xl p-8 sm:p-10 md:p-14 border border-base-300/30 text-left max-w-4xl mx-auto mt-12 space-y-6">
                        <p class="text-base-content/75 leading-relaxed text-lg">
                            TSEYOR ofrece una visión amplia del ser humano y del universo. Sus referencias pueden
                            resultar nuevas, sorprendentes o difíciles de comprender inicialmente. No se pide aceptarlas
                            de antemano ni renunciar al propio discernimiento.
                        </p>
                        <p class="text-base-content/75 leading-relaxed">
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
                            :href="route('filosofia.temas')"
                            class="btn btn-primary px-9 py-4 text-sm font-bold uppercase tracking-wider rounded-full shadow-lg hover:shadow-xl transition-all"
                        >
                            Explorar todos los temas
                            <Icon icon="ph:list-dashes-duotone" class="text-lg" />
                        </Link>
                        <Link
                            :href="route('biblioteca')"
                            class="btn btn-secondary px-8 py-4 rounded-full"
                        >
                            Explorar la biblioteca
                        </Link>
                    </div>
                </div>
            </div>
        </Section>

    </Sections>

</template>

<script setup>

const activeSection = ref('mensaje-cosmico')

function saltarASeccion(e) {
    const id = e.target.value
    const el = document.getElementById(id)
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
        history.replaceState(null, '', `#${id}`)
    }
}

function scrollAVision() {
    const el = document.getElementById('vision-general')
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }
}

onMounted(() => {
    const articles = document.querySelectorAll('#vision-general article[id]')
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

    // Leer hash inicial y hacer scroll
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

const claves = [
    {
        ancla: 'mensaje-cosmico',
        titulo: 'El cambio de era',
        resumen: 'El mensaje de los Guías Estelares, el Plan Cósmico y la revolución de la consciencia.',
    },
    {
        ancla: 'naturaleza-humana',
        titulo: 'Quiénes somos realmente',
        resumen: 'El ser humano como ser cósmico, multidimensional y conectado con el Absoluto.',
    },
    {
        ancla: 'comprobacion',
        titulo: 'El camino de autodescubrimiento',
        resumen: 'Autoobservación, experimentación y verificación personal de las referencias.',
    },
    {
        ancla: 'sociedades-armonicas',
        titulo: 'Hacia las Sociedades Armónicas',
        resumen: 'Hermandad, unidad en la diversidad y el camino hacia las Sociedades Armónicas.',
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

const ideasCamino = [
    'Autoobservación',
    'Meditación',
    'Extrapolación',
    'Juul',
    'Interiorización',
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

</script>
