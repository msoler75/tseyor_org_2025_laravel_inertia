<template>
    <!-- Hero -->
    <section id="hero" class="relative overflow-hidden py-16 md:pt-24"
    style="background: url(/almacen/medios/portada/portada_tseyor_2026_hero.jpg) center center; background-size: cover"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-2xl bg-base-100/90 backdrop-blur-xs rounded-4xl p-7 relative mt-12">

                <img src="/almacen/medios/portada/libelula.png?w=180" alt="" class="absolute right-10 -top-18"/>

                <div class="inline-block px-1 py-1.5 text-xs font-bold tracking-widest uppercase text-primary font-display">
                    ONG
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl font-display font-black text-secondary tracking-tight leading-[1.1] mb-8">
                    <span>MUNDO ARMÓNICO </span> <br class="hidden sm:inline" /><span class="text-primary">TSEYOR</span>
                </h1>

                <p class="text-lg md:text-xl text-base-content/90 leading-relaxed mb-8 max-w-2xl font-light">
                    Somos una ONG dedicada al despertar de la consciencia y la creación de las Sociedades Armónicas. Nuestros Guías Estelares nos tutelan en este proceso de transformación interior y reconexión cósmica.
                </p>

                <div class="flex items-center gap-3 mb-10 max-w-2xl">
                    <Info class="w-5 h-4 text-primary shrink-0" />
                    <p class="text-sm text-primary/80 font-semibold leading-relaxed p-0 m-0">
                        Experimentación libre para el autodescubrimiento, sin dogmas.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <a href="#sabiduria" @click.prevent="scrollSeccion('sabiduria')" class="flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-md transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer group">
                        Explorar TSEYOR
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                    <Link href="/biblioteca" class="flex items-center justify-center gap-2 px-6 py-4 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs transition-all uppercase tracking-widest shadow-sm">
                        <BookOpen class="w-4 h-4 text-primary" />
                        Biblioteca
                    </Link>
                    <Link v-if="hayProximosEventos" href="/eventos" class="flex items-center justify-center gap-2 px-6 py-4 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs transition-all uppercase tracking-widest shadow-sm">
                        <Calendar class="w-4 h-4 text-primary" />
                        Eventos
                    </Link>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="pt-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <Link href="/origenes-de-tseyor" class="group bg-base-100/80 backdrop-blur-lg p-4 sm:p-5 rounded-2xl border border-base-300/50 shadow-lg hover:shadow-2xl hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">+40 años</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">de trayectoria</span>
                    </div>
                </Link>
                <Link href="/libros" class="group bg-base-100/80 backdrop-blur-lg p-4 sm:p-5 rounded-2xl border border-base-300/50 shadow-lg hover:shadow-2xl hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><path d="M12 6v7"/><path d="M9 9h6"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">+{{ formatearNumero(stats.libros) }} libros</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">disponibles gratis</span>
                    </div>
                </Link>
                <Link href="/comunicados" class="group bg-base-100/80 backdrop-blur-lg p-4 sm:p-5 rounded-2xl border border-base-300/50 shadow-lg hover:shadow-2xl hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">+{{ formatearNumero(stats.comunicados) }}</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">comunicados de guías</span>
                    </div>
                </Link>
                <Link href="/ong" class="group bg-base-100/80 backdrop-blur-lg p-4 sm:p-5 rounded-2xl border border-base-300/50 shadow-lg hover:shadow-2xl hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">Nuestra ONG</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">Ayuda humanitaria</span>
                    </div>
                </Link>
            </div>
        </div>
    </section>

    <!-- ¿Por dónde empezar? -->
    <section class="hidden py-6 bg-base-200 border-b border-base-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-6">
            <span class="text-sm font-bold text-base-content/60 uppercase tracking-widest text-center sm:text-left">¿Por dónde empezar?</span>
            <div class="flex flex-wrap items-center justify-center gap-2">
                <Link href="/cursos" class="px-4 py-2 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider hover:bg-primary/20 transition-all border border-primary/20">
                    Curso Holístico
                </Link>
                <Link href="/biblioteca" class="px-4 py-2 rounded-full bg-base-100 text-base-content/70 text-xs font-bold uppercase tracking-wider hover:bg-base-300 transition-all border border-base-300">
                    Biblioteca
                </Link>
                <Link href="/meditaciones" class="px-4 py-2 rounded-full bg-base-100 text-base-content/70 text-xs font-bold uppercase tracking-wider hover:bg-base-300 transition-all border border-base-300">
                    Meditaciones
                </Link>
                <Link href="/eventos" class="px-4 py-2 rounded-full bg-base-100 text-base-content/70 text-xs font-bold uppercase tracking-wider hover:bg-base-300 transition-all border border-base-300">
                    Próximos encuentros
                </Link>
            </div>
        </div>
    </section>

    <!-- Wisdom: El mensaje desde las estrellas -->
    <section id="sabiduria" class="relative py-24 md:py-32 overflow-hidden bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
                <div class="lg:col-span-5 relative min-h-[400px] lg:h-[540px] rounded-3xl overflow-hidden shadow-md group">
                    <img
                        src="/almacen/medios/paginas/todos-los-guias.jpg"
                        alt="Guías Estelares"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-700"
                        loading="lazy"
                    />
                    <div class="absolute inset-0 bg-gradient-to-t from-neutral/70 via-transparent to-transparent"></div>
                </div>

                <div class="lg:col-span-7 flex flex-col justify-center">
                    <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display self-start">
                        CONFEDERACIÓN DE MUNDOS HABITADOS DE LA GALAXIA
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-black text-base-content tracking-tight leading-tight mb-6">
                        El mensaje desde las estrellas
                    </h2>

                    <p class="text-base-content/60 leading-relaxed mb-6 font-light text-base">
                        Nuestra filosofía proviene de los <b>Guías Estelares</b> de la <b>Confederación de Mundos Habitados de la Galaxia</b>, quienes nos tutelan para trascender dogmas caducos, explorar las infinitas capacidades de la consciencia humana, y así participar activamente en la creación de las futuras Sociedades Armónicas en este planeta.
                    </p>

                    <div class="border-l-4 border-primary bg-primary/10 p-5 rounded-r-3xl mb-8">
                        <p class="text-primary/80 italic font-semibold text-sm">
                            "No se trata de creer, sino de comprobar a través de la propia experimentación personal."
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                        <Link href="/guias" class="flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs uppercase tracking-widest transition-all text-center shadow-sm">
                            <Compass class="w-4 h-4 text-primary" />
                             Conocer a los Guías Estelares
                             <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </Link>
                        <Link href="/comunicados" class="flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs uppercase tracking-widest transition-all text-center shadow-sm">
                            <FileText class="w-4 h-4 text-primary" />
                            Leer sus comunicados
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute w-full my-24 mx-auto text-base-content/80 flex justify-center text-xs items-center uppercase tracking-wider gap-3">Sigue leyendo <ArrowDown class="w-4 h-4 text-primary"/>.</div>
    </section>

    <!-- Comunidad de trabajo interior -->
    <section id="comunidad" class="py-32 bg-base-200 border-t border-b border-base-300 relative">
        <div class="absolute inset-0 bg-[radial-gradient(#4f46e504_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-3xl mx-auto mb-16 flex flex-col items-center">
                <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                    COMUNIDAD DE VOLUNTARIOS
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-primary tracking-tight mb-4">
                    Autoconocimiento y crecimiento compartido
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
                <div class="md:col-span-8 bg-gradient-to-br from-primary to-primary/80 text-white p-8 sm:p-10 rounded-3xl flex flex-col justify-between shadow-xl relative overflow-hidden group border border-base-300">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl group-hover:scale-110 transition-transform pointer-events-none"></div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-white mb-8 backdrop-blur-sm">
                            <HeartHandshake class="w-6 h-6" />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-display font-bold tracking-tight mb-4 text-white">
                            Un camino que se recorre acompañado
                        </h3>
                        <p class="leading-relaxed mb-6 text-md font-light text-white/80">
                            En TSEYOR no caminas solo. Nuestra comunidad es el espacio donde compartir tus experiencias, aprender de los demás y avanzar juntos en el autoconocimiento, guiados por las referencias que nos entregan los Guías Estelares.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start gap-3 text-sm text-white/70">
                                <CheckCircle2 class="w-4 h-4 shrink-0 mt-0.5 text-white/60" />
                                <span><b class="text-white">Reflexión y meditación</b> — herramientas para el autoconocimiento y el crecimiento personal</span>
                            </li>
                            <li class="flex items-start gap-3 text-sm text-white/70">
                                <CheckCircle2 class="w-4 h-4 shrink-0 mt-0.5 text-white/60" />
                                <span><b class="text-white">Diálogo y aprendizaje mutuo</b> — compartir experiencias desde el respeto y la escucha sincera</span>
                            </li>
                            <li class="flex items-start gap-3 text-sm text-white/70">
                                <CheckCircle2 class="w-4 h-4 shrink-0 mt-0.5 text-white/60" />
                                <span><b class="text-white">Amistad y hermanamiento</b> — como una familia que crece unida superando obstáculos y apoyándose</span>
                            </li>
                        </ul>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex -space-x-2.5 overflow-hidden">
                                <img v-for="img in usuariosImagenes.slice(0, 6)" :key="img" :src="img" alt=""
                                    class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" loading="lazy" />
                            </div>
                            <span class="text-xs font-bold bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-full text-white uppercase tracking-widest border border-white/10">{{stats.usuarios}}+ miembros activos</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link href="/quienes-somos" class="w-full text-center py-3.5 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest transition-all shadow-md hover:bg-base-200">
                            Conoce quiénes somos
                        </Link>
                        <Link href="/ong" class="w-full text-center py-3.5 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest transition-all shadow-md hover:bg-base-200">
                            Conoce la ONG y sus proyectos
                        </Link>
                    </div>
                </div>

                <div class="md:col-span-4 bg-base-100 p-8 sm:p-10 rounded-3xl border border-base-300 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <Link href="/biblioteca">
                        <div class="h-48 sm:h-56 w-full rounded-2xl overflow-hidden mb-6 relative bg-base-200">
                            <img src="/almacen/medios/portada/biblioteca_tseyor_2026.jpg" alt="Biblioteca Tseyor"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-primary-content text-xs font-bold tracking-widest uppercase bg-primary px-3 py-1.5 rounded-full">
                                BIBLIOTECA TSEYOR
                            </div>
                        </div>
                        <h3 class="text-2xl font-display font-bold text-base-content tracking-tight mb-3">
                            Contenido libre y gratuito
                        </h3>
                        <p class="text-base-content/80 leading-relaxed mb-6 text-md font-light">
                            Cientos de libros, audios de meditaciones y reflexiones, vídeos, psicografías y mucho más. Todo basado en las referencias de nuestros Guías Estelares.
                        </p>
                    </Link>
                    <div class="border-t border-base-300 pt-4 flex items-center justify-between">
                        <Link href="/biblioteca" class="flex items-center gap-2 font-bold text-primary text-xs uppercase tracking-widest hover:gap-3 transition-all">
                            Acceder a la biblioteca ahora
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Medita ahora -->
    <section class="relative min-h-[500px] sm:min-h-[740px] max-h-[90vh] bg-cover bg-center bg-no-repeat flex flex-col border-t border-b border-base-300"
        style="background-image: url('/almacen/medios/portada/mujer_meditando.jpg'); background-position: 25% center">

        <div class="absolute inset-0 bg-gradient-to-l from-black/20 via-black/10 to-black/40"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col grow">

            <div class="flex justify-end items-start pt-8 sm:pt-16">
                <div class="w-full sm:max-w-sm">
                    <div class="relative bg-base-100/80 backdrop-blur-xl rounded-3xl p-7 sm:p-8 shadow-2xl border border-white/10 overflow-hidden">
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
                        <div class="absolute -bottom-16 -left-16 w-32 h-32 bg-secondary/10 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="flex items-center gap-2 mb-4">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-primary">Meditación diaria</span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-display font-black text-base-content tracking-tight leading-tight">
                            Siéntate y conecta con tu universo interior
                        </h2>
                        <p class="text-base-content/50 text-sm mt-2 leading-relaxed font-light">
                            Meditaciones y reflexiones de nuestros Guías Estelares para el autoconocimiento y el despertar de nuestra consciencia.
                        </p>

                        <!-- Play button -->
                        <button @click="toggleMeditacion" class="mt-6 w-full flex items-center justify-center gap-3 px-5 py-3.5 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest shadow-md hover:bg-primary/90 transition-all cursor-pointer">
                            <svg v-if="esMeditacionActiva && player.state === 'playing'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>
                            <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            <template v-if="esMeditacionActiva && player.state === 'playing'">Escuchando</template>
                            <template v-else>Escuchar meditación diaria</template>
                        </button>

                        <!-- Waveform visualization -->
                        <Transition name="waveform">
                            <div v-if="esMeditacionActiva" class="mt-3 bg-base-200/50 rounded-2xl p-5 border border-base-300/30">
                            <div class="flex items-end gap-[3px] h-8 mb-3">
                                <span v-for="i in 24" :key="i"
                                    class="w-[3px] rounded-full transition-all duration-150"
                                    :class="player.state === 'playing'
                                        ? 'bg-primary/60 animate-waveform'
                                        : 'bg-base-content/10'"
                                    :style="player.state === 'playing' ? { animationDelay: `${i * 0.08}s`, height: `${Math.random() * 100}%` } : { height: '30%' }">
                                </span>
                            </div>

                            <!-- Title marquee -->
                            <div class="overflow-hidden relative" style="mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);">
                                <p class="text-sm font-semibold text-base-content whitespace-nowrap animate-marquee">
                                    <span>{{ meditacionActual?.titulo }}</span>
                                    <span class="inline-block mx-8 opacity-0">|</span>
                                    <span>{{ meditacionActual?.titulo }}</span>
                                </p>
                            </div>

                            <!-- Time + Progress -->
                            <div class="mt-1.5 flex items-center gap-3">
                                <div @click="seekBar" class="flex-1 h-2 -my-0.5 flex items-center cursor-pointer group">
                                    <div class="w-full h-1 rounded-full bg-base-300/60 overflow-hidden group-hover:h-1.5 transition-all">
                                        <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary/60"
                                            :style="{ width: player.duration > 0 ? `${(player.currentTime / player.duration) * 100}%` : '0%' }">
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[11px] font-mono text-base-content/40 tabular-nums shrink-0">
                                    {{ tiempoActual }} / {{ duracionTotal }}
                                </span>
                            </div>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>

            <div class="grow"></div>

            <div class="pb-8 sm:pb-12 flex flex-wrap items-center gap-3">
                <Link href="/audios" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/20 rounded-full text-white text-xs font-bold uppercase tracking-widest transition-all shadow-lg group">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                    <span>Biblioteca de audios</span>
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </Link>
                <Link href="/radio" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/10 backdrop-blur-sm hover:bg-white/20 border border-white/20 rounded-full text-white text-xs font-bold uppercase tracking-widest transition-all shadow-lg group">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                    <span>Radio TSEYOR 24h</span>
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </Link>
            </div>
        </div>
    </section>

    <!-- Filosofía -->
    <section id="filosofia" class="py-24 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 flex flex-col items-center">
                <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                    FILOSOFÍA TSEYOR
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-primary tracking-tight">
                    Un primer vistazo a la filosofía TSEYOR
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link v-for="pagina in paginasFilosofia" :key="pagina.ruta" :href="pagina.ruta"
                    class="bg-base-100 rounded-3xl border border-base-300 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/40 transition-all duration-300 flex flex-col justify-between h-full group">
                    <div>
                        <div class="h-56 relative overflow-hidden bg-base-200">
                            <img :src="pagina.imagen+'?w=440'" :alt="pagina.titulo" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                        </div>
                        <div class="p-5">
                            <h4 class="font-display font-bold text-base-content text-lg mb-2">{{ pagina.titulo }}</h4>
                            <p class="text-base-content/60 text-sm leading-relaxed font-light">{{ pagina.descripcion }}</p>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        <span class="text-xs font-bold text-primary flex items-center gap-1 hover:text-base-content transition-colors uppercase tracking-wider">
                            Saber más <ChevronRight class="w-3 h-3" />
                        </span>
                    </div>
                </Link>
            </div>

            <div class="text-center mt-10">
                <Link href="/filosofia" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary text-neutral-content font-bold text-xs uppercase tracking-widest hover:bg-primary/80 transition-all shadow-sm">
                    Explorar todos los temas
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- Rostros de la comunidad -->
    <section class="py-24 bg-base-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
            <div class="text-center max-w-2xl mx-auto flex flex-col items-center">
                <span class="inline-block text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 px-3 py-1 mb-4 font-display">
                    GENTE REAL
                </span>
                <h2 class="text-3xl sm:text-4xl font-display font-black tracking-tight">
                    Así es nuestra comunidad
                </h2>
                <p class="text-base-content/60 mt-3 text-sm leading-relaxed max-w-lg">
                    Instantáneas de nuestra vida compartida: encuentros, talleres, convivencias.
                </p>
            </div>
        </div>

        <div v-if="entradasRecientes?.length" class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-8 gap-2 px-4 sm:px-8 max-w-7xl mx-auto">
            <Link v-for="entrada in entradasRecientes.slice(0, 24)" :key="entrada.slug" :href="entrada.ruta"
                class="aspect-square rounded-xl overflow-hidden relative group bg-base-300">
                <img :src="entrada.imagen" :alt="entrada.titulo"
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-2">
                    <span class="text-white text-[10px] font-bold leading-tight truncate">{{ entrada.titulo }}</span>
                </div>
            </Link>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 text-center">
            <Link href="/quienes-somos" class="btn btn-primary rounded-full px-8 shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                Conoce a las personas
                <ArrowRight class="w-4 h-4 ml-1.5 -mt-0.5" />
            </Link>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-24 bg-base-200 border-t border-b border-base-300">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12 flex flex-col items-center">
                <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                    ACLARA TUS INQUIETUDES
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight">
                    Preguntas frecuentes
                </h2>
                <p class="text-base-content/60 mt-4 text-sm font-light leading-relaxed max-w-xl">
                    Respuestas directas a las dudas más habituales antes de dar el paso.
                </p>
            </div>

            <div class="space-y-4">
                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                        ¿Cómo se comunican los Guías Estelares con vosotros?
                        <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                        Mayormente a través de nuestro <b>canalizador Chac-Mool Puente</b> mediante la <b>telepatía externa</b>. Este proceso lleva más de 40 años documentándose y está disponible públicamente en nuestra web.
                    </p>
                </details>
                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                        ¿Hay que tener alguna creencia para participar?
                        <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                        Ninguna. No hay dogmas, no hay credo que suscribir. Puedes ser ateo, agnóstico, espiritual o religioso. Lo único que se pide es apertura para explorar y disposición para compartir desde el respeto. Cada persona vive su proceso a su ritmo.
                    </p>
                </details>
                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                        ¿Hay que pagar algo? ¿Hay compromiso?
                        <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                        No y no. Todo es gratuito: libros, audios, curso, eventos. Y no hay compromiso: puedes explorar, leer, escuchar y participar sin ninguna obligación. Tampoco hay plazos ni permanencia. Si en cualquier momento decides que esto no es para ti, no pasa nada.
                    </p>
                </details>
                <details class="group rounded-2xl bg-base-100 border border-base-200/60 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base">
                        ¿Cómo puedo empezar?
                        <ChevronDown class="w-5 h-5 text-base-content/40 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-200/60 pt-4">
                        La puerta de entrada recomendada es el <Link href="/cursos" class="text-primary hover:underline">curso holístico</Link>: es gratuito, tutelado y te da el contexto completo. También puedes explorar la <Link href="/biblioteca" class="text-primary hover:underline">biblioteca</Link> con cientos de libros gratis, o escuchar las <Link href="/audios" class="text-primary hover:underline">meditaciones</Link> para empezar a familiarizarte.
                    </p>
                </details>
            </div>

            <div class="text-center mt-10">
                <Link href="/preguntas-frecuentes" class="text-xs font-bold text-primary hover:text-base-content transition-colors uppercase tracking-widest">
                    Ver todas las preguntas frecuentes →
                </Link>
            </div>
        </div>
    </section>

    <section class="py-24 bg-base-300">
        <Card class="max-w-3xl mx-auto">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center gap-3 mb-4">
                    <Mail class="w-5 text-primary" />
                    <h2 class="text-xl font-display font-bold text-base-content mb-0">Boletín TSEYOR</h2>
                </div>
                <p class="text-base-content/60 text-sm mb-6 max-w-xl">Recibe puntualmente en tu correo electrónico las últimas actualizaciones de comunicados, nuevos libros en PDF y la programación de nuestros próximos encuentros virtuales gratuitos.</p>
                <div class="mx-auto">
                    <Suscribe />
                </div>
            </div>
        </Card>
    </section>

    <!-- CTA final -->
    <section class="py-24 relative overflow-hidden bg-cover bg-center"
        style="background-image: url('/almacen/medios/portada/final.jpg'); background-size: cover">
        <div class="absolute inset-0 bg-gradient-to-r from-primary/90 via-primary/80 to-primary/85"></div>
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl p-8 sm:p-14 lg:p-20 text-center text-white relative overflow-hidden">
                <div class="max-w-2xl mx-auto">
                    <span class="inline-block text-xs font-bold tracking-widest bg-white/15 text-white/90 border border-white/20 uppercase px-3 py-1.5 rounded-full mb-6 font-display backdrop-blur-sm">
                        TU PUERTA DE ENTRADA
                    </span>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-black tracking-tight leading-tight mb-6 text-white">
                        Inscríbete al curso gratuito
                    </h2>
                    <p class="text-white/70 leading-relaxed mb-10 text-sm sm:text-base font-light max-w-xl mx-auto">
                        El curso holístico es la puerta de entrada a la comunidad. En él conocerás la filosofía, las herramientas de trabajo interior y el camino para integrarte como voluntario en la ONG. Es gratuito, vivo y abierto para ti.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link href="/cursos" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest shadow-lg transition-all duration-300 hover:bg-base-200 hover:shadow-xl transform hover:-translate-y-0.5 hover:scale-105 active:translate-y-0">
                            Conocer el curso
                            <ArrowRight class="w-4 h-4 inline-block ml-1.5 -mt-0.5 group-hover:translate-x-1 transition-transform" />
                        </Link>
                        <Link href="/biblioteca" class="w-full sm:w-auto px-6 py-4 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 font-bold text-xs uppercase tracking-widest transition-all text-center backdrop-blur-sm hover:scale-105">
                            Ver contenidos libres primero
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Suscribe from "@/Components/Suscribe.vue"
import usePlayer from '@/Stores/player.js'
import { getSrcUrl } from '@/composables/srcutils.js'

import {
  ArrowRight,
  ArrowDown,
  BookOpen,
  Calendar,
  Info,
  ChevronDown,
  ChevronRight,
  Compass,
  FileText,
  CheckCircle2,
  HeartHandshake,
  Mail,
} from 'lucide-vue-next'

const player = usePlayer()

const scrollSeccion = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

let lenisInstance

onMounted(async () => {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (prefersReduced) return

    try {
        const Lenis = (await import('lenis')).default
        lenisInstance = new Lenis({
            lerp: 0.08,
            smoothWheel: true,
            orientation: 'vertical',
            autoRaf: true,
            wheelMultiplier: 1,
        })
    } catch (e) {
        console.warn('Lenis no pudo cargarse, scroll nativo intacto')
    }
})

onUnmounted(() => {
    if (lenisInstance) lenisInstance.destroy()
})

const props = defineProps({
    hayProximosEventos: Boolean,
    stats: {
        type: Object,
        default: () => ({}),
    },
    paginasFilosofia: {
        type: Array,
        default: () => [],
    },
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
    usuariosImagenes: {
        type: Array,
        default: () => [],
    },
    meditaciones: {
        type: Array,
        default: () => [],
    },
})

function formatoDuracion(segundos) {
    if (!segundos || !isFinite(segundos)) return '0:00'
    const m = Math.floor(segundos / 60)
    const s = Math.floor(segundos % 60)
    return `${m}:${s.toString().padStart(2, '0')}`
}

const tiempoActual = computed(() => formatoDuracion(player.currentTime))
const duracionTotal = computed(() => formatoDuracion(player.duration))

function formatearNumero(n) {
    if (n == null) return '0'
    return n.toLocaleString('es-ES')
}

const meditacionActual = computed(() => props.meditaciones[0] || null)
const meditacionUrl = computed(() => meditacionActual.value ? getSrcUrl(meditacionActual.value.audio) : null)

const esMeditacionActiva = computed(() => {
    if (!player.music || !meditacionUrl.value) return false
    return player.music.src === meditacionUrl.value
})

function toggleMeditacion() {
    if (!meditacionActual.value || !meditacionUrl.value) return
    if (esMeditacionActiva.value) {
        player.playPause()
    } else {
        player.play(meditacionUrl.value, meditacionActual.value.titulo)
    }
}

function seekBar(e) {
    if (!player.duration || player.duration <= 0) return
    const rect = e.currentTarget.getBoundingClientRect()
    const ratio = (e.clientX - rect.left) / rect.width
    player.seek(ratio * player.duration)
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes scaleUp {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

@keyframes ring-pulse {
  0% { opacity: 1; transform: scale(0.95); }
  50% { opacity: 0.5; }
  100% { opacity: 1; transform: scale(1.05); }
}

@keyframes waveform {
  0%, 100% { transform: scaleY(0.4); opacity: 0.4; }
  50% { transform: scaleY(1); opacity: 1; }
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-ring-pulse {
  animation: ring-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-waveform {
  animation: waveform 0.8s ease-in-out infinite alternate;
}

.animate-marquee {
  animation: marquee 15s linear infinite;
}

.waveform-enter-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.waveform-leave-active {
  transition: all 0.3s ease-in;
}
.waveform-enter-from {
  opacity: 0;
  transform: translateY(-16px);
}
.waveform-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}

.animate-scaleUp {
  animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
