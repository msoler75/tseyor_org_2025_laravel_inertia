<template>
    <Sections height="500">

        <!-- 1. Hero: conecta con la portada -->
        <Section class="py-12 md:py-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid gap-10 items-center [@media(min-width:870px)]:grid-cols-2">
                    <div>
                        <span class="inline-flex items-center gap-2 text-xs font-bold tracking-widest uppercase text-primary/70 font-display mb-3">
                            <Users class="w-3.5 h-3.5 text-primary" />
                            +1.200 personas han realizado este curso
                        </span>
                        <h1 class="text-4xl sm:text-5xl font-display font-black tracking-tight leading-tight mb-4">
                            Curso Holístico Tseyor
                        </h1>
                        <p class="text-lg text-base-content/80 leading-relaxed mb-4">
                            La puerta de entrada a una filosofía viva, unas herramientas de autoconocimiento y una comunidad que te acompaña. Un curso tutelado por los <Referencia>Guías Estelares</Referencia>, donde todos aprendemos de todos.
                        </p>
                        <div class="flex flex-wrap gap-x-6 gap-y-2 mb-6">
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/80">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                100% gratuito
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/80">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                Tutelado
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/80">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                En comunidad
                            </span>
                            <span class="inline-flex items-center gap-2 text-sm text-base-content/80">
                                <CheckCircle2 class="w-4 h-4 text-primary" />
                                7 capítulos
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <Link :href="route('cursos.inscripcion.nueva')" class="btn btn-primary rounded-full px-8 shadow-lg whitespace-nowrap transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                 Quiero inscribirme
                                 <ArrowRight class="w-4 h-4 ml-1" />
                             </Link>
                            <a href="#material-del-curso" @click.prevent="scrollToMaterial" class="w-full sm:w-auto text-center py-3.5 px-8 rounded-full btn btn-ghost border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 shadow-sm whitespace-nowrap">
                                Ver el material del curso
                            </a>
                        </div>
                    </div>
                    <div class="hidden justify-center [@media(min-width:870px)]:flex">
                        <Image src="/almacen/medios/paginas/curso.png" src-width="373" src-height="482" alt="Curso Holístico Tseyor" class="max-h-96 object-contain drop-shadow-2xl" />
                    </div>
                </div>
            </div>
        </Section>

        <!-- 2. Qué aprenderás -->
        <Section class="py-16 md:py-20 bg-base-200/50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-3">Qué aprenderás</h2>
                <p class="text-base-content/80 max-w-xl mx-auto mb-10 sm:mb-14">
                    El curso recorre las enseñanzas fundamentales que los <Referencia>Guías Estelares</Referencia> han transmitido a lo largo de más de 40 años.
                </p>

                <div class="grid sm:grid-cols-2 gap-4 lg:gap-5 max-w-4xl mx-auto text-left">
                    <div class="p-6 rounded-2xl bg-base-100 flex flex-col items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0">
                            <Icon icon="hugeicons:scroll-01" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">Los mensajes de los Guías</h3>
                            <p class="text-sm text-base-content/80 leading-relaxed">Conocerás a los <Referencia>Guías Estelares</Referencia>, su papel como tutores y el contenido de sus comunicaciones.</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-base-100 flex flex-col items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0">
                            <Icon icon="lucide:heart" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">La filosofía cósmico-crística</h3>
                            <p class="text-sm text-base-content/80 leading-relaxed">Los principios de <Referencia>hermandad galáctica</Referencia>, el despertar de la conciencia y las <Referencia>Sociedades Armónicas</Referencia>.</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-base-100 flex flex-col items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0">
                            <Icon icon="hugeicons:idea" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">Herramientas de autoconocimiento</h3>
                            <p class="text-sm text-base-content/80 leading-relaxed">Prácticas de meditación, reflexión interior y trabajo personal para tu propio autodescubrimiento.</p>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl bg-base-100 flex flex-col items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-linear-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0">
                            <Icon icon="hugeicons:hand-helping" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-display font-bold text-base mb-1.5">Organización y voluntariado</h3>
                            <p class="text-sm text-base-content/80 leading-relaxed">Cómo está estructurada TSEYOR como ONG, sus estamentos, su funcionamiento y cómo integrarte si así lo deseas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 3. Cómo funciona -->
        <Section class="py-16">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-10">Cómo se vive el curso</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-left">
                    <div class="flex flex-col items-start p-4 lg:p-6 rounded-2xl bg-base-100 border border-base-200/60 shadow-sm">
                        <div class="flex lg:flex-col items-center lg:items-start gap-3 lg:gap-4 w-full">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">1</div>
                            <h3 class="font-display font-bold text-base mb-0">Te inscribes</h3>
                        </div>
                        <p class="text-sm text-base-content/80 leading-relaxed mt-3 lg:mt-10">Rellena el <Link :href="route('cursos.inscripcion.nueva')" class="text-primary hover:underline font-medium">formulario</Link> y nos pondremos en contacto contigo para empezar.</p>
                    </div>
                    <div class="flex flex-col items-start p-4 lg:p-6 rounded-2xl bg-base-100 border border-base-200/60 shadow-sm">
                        <div class="flex lg:flex-col items-center lg:items-start gap-3 lg:gap-4 w-full">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">2</div>
                            <h3 class="font-display font-bold text-base mb-0">Eliges tu modalidad</h3>
                        </div>
                        <p class="text-sm text-base-content/80 leading-relaxed mt-3 lg:mt-10">
                            Presencial en un
                            <Link href="/donde-estamos" class="text-primary hover:underline font-medium">centro cerca</Link> de ti
                            u online por videoconferencia.
                        </p>
                        <Link href="/donde-estamos" class="text-xs text-primary/80 hover:text-primary transition-colors mt-3">Encuentra nuestros centros →</Link>
                    </div>
                    <div class="flex flex-col items-start p-4 lg:p-6 rounded-2xl bg-base-100 border border-base-200/60 shadow-sm">
                        <div class="flex lg:flex-col items-center lg:items-start gap-3 lg:gap-4 w-full">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">3</div>
                            <h3 class="font-display font-bold text-base mb-0">Lectura del libro</h3>
                        </div>
                        <p class="text-sm text-base-content/80 leading-relaxed mt-3 lg:mt-10">Lectura atenta y grupal del <a href="#material-del-curso" @click.prevent="scrollToMaterial" class="text-primary hover:underline font-medium cursor-pointer">libro del curso</a>, durante 7 capítulos, resolviendo dudas y compartiendo impresiones.</p>
                    </div>
                    <div class="flex flex-col items-start p-4 lg:p-6 rounded-2xl bg-base-100 border border-base-200/60 shadow-sm">
                        <div class="flex lg:flex-col items-center lg:items-start gap-3 lg:gap-4 w-full">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold font-display shrink-0">4</div>
                            <h3 class="font-display font-bold text-base mb-0">Nombre simbólico</h3>
                        </div>
                        <p class="text-sm text-base-content/80 leading-relaxed mt-3 lg:mt-10">Al completar el curso, los Guías Estelares te otorgan tu <Link href="/glosario/nombre-simbolico" class="text-primary hover:underline font-medium">nombre simbólico</Link> dentro de TSEYOR.</p>
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
                    <div class="flex flex-col items-center">
                        <div class="relative">
                            <span class="absolute -top-2 -right-2 z-10 inline-flex items-center gap-1 text-[10px] font-bold tracking-widest uppercase bg-warning text-warning-content px-2.5 py-1 rounded-full shadow-sm">
                                <Star class="w-3 h-3 fill-current" />
                                Libro principal
                            </span>
                            <Libro3d :libro="libro" alt="Curso Holístico Tseyor" />
                        </div>
                    </div>
                    <div class="md:text-left text-center">
                        <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-4">Curso Holístico Tseyor</h2>
                        <p class="text-base text-base-content/70 leading-relaxed mb-4">
                            El curso se basa en un libro de relatos y enseñanzas transmitidas por los Guías Estelares. Cada capítulo abre una puerta a la reflexión, el autoconocimiento y el diálogo compartido.
                        </p>
                        <p class="text-sm text-base-content/80 mb-6">
                            Puedes descargarlo gratuitamente.
                        </p>
                        <Link href="/libros/curso-holistico-tseyor" class="btn btn-primary rounded-full px-8 shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105">
                            Descargar el libro del curso
                        </Link>
                    </div>
                </div>

                <!-- Separador -->
                <div class="flex items-center gap-4 my-16">
                    <div class="flex-1 h-px bg-base-300"></div>
                    <span class="text-xs font-bold tracking-widest uppercase text-base-content/70">Material complementario</span>
                    <div class="flex-1 h-px bg-base-300"></div>
                </div>

                <!-- Material complementario -->
                <div class="grid md:grid-cols-2 gap-10 items-center">
                    <div class="flex justify-center md:order-2">
                        <Libro3d :libro="libroGuias" alt="Los Guías Estelares" />
                    </div>
                    <div class="md:order-1 md:text-left text-center">
                        <h3 class="text-2xl sm:text-3xl font-display font-bold tracking-tight mb-3">Lectura complementaria</h3>
                        <p class="text-sm text-base-content/80 leading-relaxed mb-4">
                            Conoce la historia del grupo TSEYOR y su relación con los Guías Estelares, con más de 40 años de experiencias de contactismo.
                        </p>
                        <p class="text-sm text-base-content/80 mb-6">
                            Lectura complementaria gratuita.
                        </p>
                        <Link :href="`/libros/${libroGuias.slug}`" class="btn btn-ghost rounded-full border border-base-300 hover:border-primary/40 hover:bg-base-200 transition-all">
                            Descargar Los Guías Estelares
                        </Link>
                    </div>
                </div>
            </div>
        </Section>

        <!-- 5. Preguntas frecuentes -->
        <Section class="py-16">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-10 text-center">Preguntas frecuentes</h2>

                <div class="space-y-4">
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Cuánto tiempo dura el curso?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/75 leading-relaxed border-t border-base-200/60 pt-4">
                            Son 7 capítulos que se leen en grupo al ritmo que marque la dinámica del grupo. No hay una duración fija: puede ser desde unas pocas semanas hasta varios meses, según la frecuencia de las reuniones.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Necesito conocimientos previos?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/75 leading-relaxed border-t border-base-200/60 pt-4">
                            Ninguno. El curso es la puerta de entrada. No se requiere experiencia previa en meditación, filosofía ni ningún conocimiento especial. Solo ganas de explorar y compartir.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Qué son los Guías Estelares?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/75 leading-relaxed border-t border-base-200/60 pt-4">
                            Seres de otros mundos que se comunican con el grupo TSEYOR desde hace más de 40 años. Su mensaje promueve el despertar de la conciencia, la <Referencia>hermandad galáctica</Referencia> y las <Referencia>Sociedades Armónicas</Referencia>. En el curso los conocerás a través del libro y las sesiones grupales.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Qué consigo al finalizar el curso?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/75 leading-relaxed border-t border-base-200/60 pt-4">
                            Al completar el curso, recibes tu <Referencia r="nombre simbolico">nombre simbólico</Referencia> dentro de TSEYOR, un nombre que los Guías Estelares te otorgan y que representa tu esencia. También pasas a formar parte de la comunidad TSEYOR si así lo deseas.
                        </p>
                    </details>
                    <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                        <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                            ¿Qué pasa después de inscribirme?
                            <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                        </summary>
                        <p class="mt-4 text-sm text-base-content/75 leading-relaxed border-t border-base-200/60 pt-4">
                            Te pondrás en contacto con el grupo más cercano a tu zona o con el grupo online. Ellos te indicarán cuándo comienza la próxima lectura grupal y cómo incorporarte. No hay fechas fijas: el curso arranca cuando hay un grupo listo.
                        </p>
                    </details>
                </div>
            </div>
        </Section>

        <!-- 6. Testimonios -->
        <Section v-if="testimonios.length" class="py-16 bg-base-200/50 overflow-hidden">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-10 lg:px-16">
                    <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight mb-3">Quiénes ya lo han vivido</h2>
                    <p class="text-base-content/70 leading-relaxed">
                        Personas de distintos países comparten qué les ha aportado el curso y la comunidad TSEYOR. Desliza o usa las flechas para ver más.
                    </p>
                </div>
            </div>

            <!-- Carousel a ancho completo de ventana -->
            <div class="relative">
                <button
                    type="button"
                    aria-label="Ver testimonios anteriores"
                    @click="scrollTestimonios(-1)"
                    :class="testimoniosAtStart ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                    class="absolute left-3 sm:left-4 -translate-y-2 top-1/2 z-20 inline-flex items-center justify-center w-11 h-11 rounded-full bg-base-100 border border-base-300 shadow-md hover:border-primary/40 hover:bg-base-200 text-base-content/70 hover:text-primary transition-opacity duration-700 ease-out"
                >
                    <ChevronLeft class="w-5 h-5" />
                </button>
                <button
                    type="button"
                    aria-label="Ver más testimonios"
                    @click="scrollTestimonios(1)"
                    :class="testimoniosAtEnd ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                    class="absolute right-3 sm:right-4 -translate-y-2 top-1/2 z-20 inline-flex items-center justify-center w-11 h-11 rounded-full bg-base-100 border border-base-300 shadow-md hover:border-primary/40 hover:bg-base-200 text-base-content/70 hover:text-primary transition-opacity duration-700 ease-out"
                >
                    <ChevronRight class="w-5 h-5" />
                </button>

                <!-- Degradado de disolución en el borde derecho (por encima de las tarjetas, debajo del botón) -->
                <div
                    aria-hidden="true"
                    :class="testimoniosAtEnd ? 'opacity-0' : 'opacity-100'"
                    class="pointer-events-none absolute inset-y-0 right-0 w-40 sm:w-56 z-10 bg-linear-to-l from-base-200 from-15% via-75% via-base-200/40 to-transparent transition-opacity duration-700 ease-out"
                ></div>

                <!-- Fila scrolleable a ancho completo -->
                <div
                    ref="testimoniosScroller"
                    role="region"
                    aria-roledescription="carrusel"
                    aria-label="Testimonios de alumnos"
                    tabindex="0"
                    @scroll="onTestimoniosScroll"
                    @keydown="onTestimoniosKeydown"
                    :style="{ paddingLeft: testimoniosStartPadding }"
                    class="flex gap-6 overflow-x-auto px-4 sm:px-6 pb-4 motion-safe:scroll-smooth [scrollbar-width:thin] [scrollbar-color:var(--color-base-300)_transparent] focus:outline-none focus-visible:ring-2 focus-visible:ring-primary/40 rounded-2xl"
                >
                    <figure
                        v-for="testimonio in testimonios"
                        :key="testimonio.nombre"
                        class="flex flex-col w-[280px] sm:w-[320px] lg:w-[340px] shrink-0 rounded-2xl bg-base-100 border border-base-200/60 shadow-sm p-6"
                    >
                        <div class="text-sm sm:text-base text-base-content/80 leading-relaxed flex-1">
                            «{{ testimonio.frase }}»
                        </div>
                        <figcaption class="mt-5 pt-4 border-t border-base-200/60 flex items-center gap-3">
                            <Image :src="testimonio.imagen" width="48" height="48" :alt="`Testimonio de ${testimonio.nombre}`" class="w-12 h-12 rounded-full object-cover shrink-0" />
                            <div class="min-w-0">
                                <div class="font-display font-bold text-base leading-tight">{{ testimonio.nombre }}</div>
                                <div class="text-sm text-base-content/80">{{ testimonio.pais }}</div>
                            </div>
                        </figcaption>
                    </figure>
                </div>

                <!-- Contador de posición del carrusel (visible + aria-live) -->
                <div class="flex items-center justify-center gap-3 mt-2 pb-1">
                    <span
                        aria-live="polite"
                        class="text-xs font-bold tracking-widest uppercase text-base-content/70 font-display tabular-nums"
                    >
                        {{ testimoniosPosition }} / {{ testimonios.length }}
                    </span>
                </div>
            </div>
        </Section>

        <!-- 7. CTA final -->
        <Section class="py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-linear-to-r from-primary/90 via-primary/80 to-primary/85"></div>
            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl p-8 sm:p-14 lg:p-20 text-center text-white relative overflow-hidden">
                    <div class="max-w-2xl mx-auto">
                        <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-black tracking-tight leading-tight mb-6 text-white">
                            Inscríbete al curso gratuito
                        </h2>
                        <p class="text-white/90 leading-relaxed mb-10 text-sm sm:text-base font-light max-w-xl mx-auto">
                            Empezamos cuando recibimos solicitudes de tu zona, online o presencial. Sin compromiso, sin dogmas, sin coste. Solo tú y tu experiencia.
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link :href="route('cursos.inscripcion.nueva')" class="whitespace-nowrap w-full sm:w-auto px-8 py-4 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest shadow-lg transition-all duration-300 hover:bg-base-200 hover:shadow-xl transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                                Inscribirme gratis
                                <ArrowRight class="w-4 h-4 inline ml-1.5 -mt-0.5" />
                            </Link>
                            <Link :href="route('biblioteca')" class="whitespace-nowrap w-full sm:w-auto px-6 py-4 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-widest transition-all text-center hover:scale-105">
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

import { ref } from 'vue'
import { ArrowRight, CheckCircle2, Users, Star, ChevronDown, ChevronLeft, ChevronRight } from 'lucide-vue-next'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    libro: {
        type: Object,
        required: true,
    },
    libroGuias: {
        type: Object,
        required: true,
    },
    testimonios: {
        type: Array,
        default: () => [],
    },
})

// Mezcla los testimonios en una única pasada (al montar) para que el carrusel
// no cambie de orden en cada re-render y las keys permanezcan estables.
const testimonios = ref([])

function barajarTestimonios() {
    const mezclados = [...props.testimonios]
    for (let i = mezclados.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [mezclados[i], mezclados[j]] = [mezclados[j], mezclados[i]]
    }
    testimonios.value = mezclados
}

// --- Lógica del carousel de testimonios ---
const testimoniosScroller = ref(null)
const testimoniosAtStart = ref(true)
const testimoniosAtEnd = ref(false)
// Posición visible del carrusel (1-based) para el contador y aria-live
const testimoniosPosition = ref(1)

// Centra la primera tarjeta respecto a un contenedor virtual de 1280px
// en pantallas mayores: padding-left = (anchoVentana - 1280) / 2
const testimoniosStartPadding = ref('')

function actualizarPaddingInicio() {
    const ancho = window.innerWidth
    if (ancho > 1280) {
        testimoniosStartPadding.value = `${Math.round((ancho - 1280) / 2)}px`
    } else {
        testimoniosStartPadding.value = ''
    }
    // Recalcula los botones tras cambiar el padding
    requestAnimationFrame(() => updateTestimoniosButtons())
}

function updateTestimoniosButtons() {
    const el = testimoniosScroller.value
    if (!el) return
    // El "inicio" real no es scrollLeft 0: con snap-mandatory la primera
    // tarjeta queda alineada al borde, dejando scrollLeft = padding inicial.
    const umbralInicio = (testimoniosStartPadding.value ? parseFloat(testimoniosStartPadding.value) : 0) + 4
    testimoniosAtStart.value = el.scrollLeft <= umbralInicio
    // Se considera "final" cuando queda poco por recorrer, no solo en el último píxel:
    // margen = 20% del ancho visible (o 64px como máximo).
    const restante = el.scrollWidth - (el.scrollLeft + el.clientWidth)
    const umbralFinal = Math.min(64, el.clientWidth * 0.2)
    testimoniosAtEnd.value = restante <= umbralFinal
}

function scrollTestimonios(direction) {
    const el = testimoniosScroller.value
    if (!el) return
    // Desplaza una "ventana" hacia la izquierda o la derecha
    const desplazamiento = direction * el.clientWidth
    el.scrollBy({
        left: desplazamiento,
        behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth',
    })
}

function onTestimoniosScroll() {
    updateTestimoniosButtons()
    actualizarPosicionTestimonios()
}

function actualizarPosicionTestimonios() {
    const el = testimoniosScroller.value
    if (!el || !testimonios.length) return

    const maxScroll = el.scrollWidth - el.clientWidth
    if (maxScroll <= 0) return

    // Proporción del scroll (0 = inicio, 1 = final) → índice estimado
    const progress = el.scrollLeft / maxScroll
    const indice = Math.round(progress * (testimonios.length - 1)) + 1
    testimoniosPosition.value = Math.max(1, Math.min(testimonios.length, indice))
}

// Navegación por teclado del carrusel (H7: flexibility & efficiency)
function onTestimoniosKeydown(event) {
    if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault()
        scrollTestimonios(event.key === 'ArrowLeft' ? -1 : 1)
    }
}

let resizeHandler = null

onMounted(() => {
    barajarTestimonios()
    actualizarPaddingInicio()
    updateTestimoniosButtons()
    actualizarPosicionTestimonios()
    resizeHandler = () => actualizarPaddingInicio()
    window.addEventListener('resize', resizeHandler)
})

onBeforeUnmount(() => {
    if (resizeHandler) {
        window.removeEventListener('resize', resizeHandler)
    }
})

function scrollToMaterial() {
    document.getElementById('material-del-curso')?.scrollIntoView({ behavior: 'smooth' })
}
</script>
