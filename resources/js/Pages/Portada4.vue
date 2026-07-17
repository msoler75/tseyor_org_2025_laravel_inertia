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
                    Somos una ONG que, guiada por la comunicación directa con los Guías Estelares de la Confederación de Mundos Habitados, explora el despertar de la consciencia y la creación de Sociedades Armónicas. Sin dogmas, sin líderes, sin atajos.
                </p>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <a href="#guias-estelares" @click.prevent="scrollSeccion('guias-estelares')" class="flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-md transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer group">
                        Explorar TSEYOR
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </a>
                    <Link href="/biblioteca" class="flex items-center justify-center gap-2 px-6 py-4 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs transition-all uppercase tracking-widest shadow-sm">
                        <BookOpen class="w-4 h-4 text-primary" />
                        Biblioteca
                    </Link>
                    <Link v-if="hayProximosEventos" href="/eventos" class="flex items-center justify-center gap-2 px-6 py-4 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs transition-all uppercase tracking-widest shadow-sm">
                        Eventos
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Social proof: stats -->
    <div class="bg-primary/5 border-y border-base-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm text-base-content/50">
                <span class="font-bold uppercase tracking-wider text-[11px] text-base-content/30">Comunidad</span>
                <span class="inline-flex items-center gap-1.5"><span class="font-black text-base-content font-display text-lg">+40</span> años de comunicación</span>
                <span class="w-1 h-1 rounded-full bg-base-300"></span>
                <span class="inline-flex items-center gap-1.5"><span class="font-black text-base-content font-display text-lg">{{ formatearNumero(stats.usuarios) }}</span> miembros</span>
                <span class="w-1 h-1 rounded-full bg-base-300"></span>
                <span class="inline-flex items-center gap-1.5"><span class="font-black text-base-content font-display text-lg">{{ formatearNumero(stats.libros) }}</span> libros</span>
                <span class="w-1 h-1 rounded-full bg-base-300"></span>
                <span class="inline-flex items-center gap-1.5"><span class="font-black text-base-content font-display text-lg">{{ formatearNumero(stats.comunicados) }}</span> comunicados</span>
                <span class="w-1 h-1 rounded-full bg-base-300"></span>
                <span class="inline-flex items-center gap-1.5"><span class="font-black text-base-content font-display text-lg">{{ formatearNumero(stats.audios) }}</span> audios</span>
            </div>
        </div>
    </div>

    <!-- Guías Estelares -->
    <section id="guias-estelares" class="py-24 lg:py-32 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative min-h-[300px] lg:min-h-[450px] rounded-2xl overflow-hidden bg-base-200 order-2 lg:order-1">
                    <img src="/almacen/medios/paginas/todos-los-guias.jpg" alt="Guías Estelares"
                        class="absolute inset-0 w-full h-full object-cover"
                        loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                </div>

                <div class="order-1 lg:order-2">
                    <div class="inline-flex items-center gap-2 bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full mb-6">
                        <Stars class="w-3.5 h-3.5" />
                        El mensaje desde las estrellas
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight leading-tight mb-6">
                        ¿Quiénes son los Guías Estelares?
                    </h2>
                    <p class="text-base-content/60 leading-relaxed mb-6">
                        Son seres de la <strong>Confederación de Mundos Habitados de la Galaxia</strong> que, desde hace más de 40 años, se comunican con nuestro canalizador <strong>Chac-Mool Puente</strong> a través de la <strong>telepatía externa</strong>. Su mensaje es claro: estamos en un momento de cambio y la humanidad está llamada a despertar.
                    </p>
                    <p class="text-base-content/60 leading-relaxed mb-8">
                        No piden fe ni devoción. Solo ofrecen referencias para que cada persona <strong>compruebe por sí misma</strong>. Sus comunicados están documentados públicamente desde 1988.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <Link href="/guias" class="btn btn-primary rounded-xl px-6 shadow-md text-xs font-bold uppercase tracking-wider">
                            Conocer a los Guías Estelares
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link href="/comunicados" class="btn rounded-xl bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 text-xs font-bold uppercase tracking-wider px-6">
                            Leer sus comunicados
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filosofía -->
    <section id="filosofia" class="py-24 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14 flex flex-col items-center">
                <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                    FILOSOFÍA
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight">
                    Las enseñanzas que nos transmiten
                </h2>
                <p class="text-base-content/60 mt-3 text-sm leading-relaxed">
                    Las referencias de los Guías Estelares nos invitan a una transformación profunda: del individuo a la comunidad, del miedo al amor, de la separación a la unidad.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link v-for="pagina in paginasFilosofia" :key="pagina.ruta" :href="pagina.ruta"
                    class="bg-base-100 rounded-3xl border border-base-300 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/40 transition-all duration-300 flex flex-col justify-between h-full group">
                    <div>
                        <div class="h-48 relative overflow-hidden bg-base-200">
                            <img :src="pagina.imagen" :alt="pagina.titulo" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
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

    <!-- ONG -->
    <section class="py-24 lg:py-32 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1">
                    <div class="inline-flex items-center gap-2 bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full mb-6">
                        <HeartHandshake class="w-3.5 h-3.5" />
                        ONG
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight leading-tight mb-6">
                        La filosofía en acción
                    </h2>
                    <p class="text-base-content/60 leading-relaxed mb-8">
                        Mundo Armónico TSEYOR es una ONG registrada que traduce las enseñanzas de los Guías Estelares en proyectos concretos: ayuda humanitaria, centros de meditación, difusión cultural y acompañamiento a personas en su camino de autoconocimiento. Todo con transparencia y sin ánimo de lucro.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <Link href="/ong" class="flex items-center gap-3 p-4 rounded-xl bg-base-200/50 hover:bg-primary/5 border border-base-200 hover:border-primary/20 transition-all group">
                            <span class="text-2xl font-display font-black text-primary">{{ formatearNumero(stats.centros) }}</span>
                            <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider leading-tight">Centros<br/>activos</span>
                        </Link>
                        <Link href="/ong" class="flex items-center gap-3 p-4 rounded-xl bg-base-200/50 hover:bg-primary/5 border border-base-200 hover:border-primary/20 transition-all group">
                            <span class="text-2xl font-display font-black text-primary">{{ formatearNumero(stats.usuarios) }}</span>
                            <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider leading-tight">Voluntarios<br/>y miembros</span>
                        </Link>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <Link href="/ong" class="btn btn-primary rounded-xl px-6 shadow-md text-xs font-bold uppercase tracking-wider">
                            Conoce la ONG
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link href="/centros" class="btn rounded-xl bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 text-xs font-bold uppercase tracking-wider px-6">
                            Nuestros centros
                        </Link>
                    </div>
                </div>

                <div class="order-1 lg:order-2 relative min-h-[300px] lg:min-h-[400px] rounded-2xl overflow-hidden bg-base-200">
                    <img src="/almacen/medios/portada/portada_tseyor_2026_hero.jpg" alt="ONG Tseyor"
                        class="absolute inset-0 w-full h-full object-cover"
                        loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Comunidad -->
    <section class="py-24 lg:py-32 bg-primary/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-2">
                    <div class="inline-flex items-center gap-2 bg-secondary/10 text-secondary text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full mb-6">
                        <Users class="w-3.5 h-3.5" />
                        Comunidad
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight leading-tight mb-6">
                        Un camino que se recorre acompañado
                    </h2>
                    <p class="text-base-content/60 leading-relaxed mb-8">
                        En TSEYOR no caminas solo. Somos personas que, tuteladas por los Guías Estelares, compartimos, aprendemos y crecemos juntas en busca del despertar de la consciencia. Cada cual desde su singularidad, todas hacia un mismo horizonte.
                    </p>

                    <div class="flex items-center gap-2 mb-8">
                        <div class="flex -space-x-2.5 overflow-hidden">
                            <img v-for="img in usuariosImagenes.slice(0, 6)" :key="img" :src="img" alt=""
                                class="inline-block h-9 w-9 rounded-full ring-2 ring-white object-cover" loading="lazy" />
                        </div>
                        <span class="text-sm font-bold text-primary">{{stats.usuarios}}+ miembros</span>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <Link href="/quienes-somos" class="btn btn-primary rounded-xl px-6 shadow-md text-xs font-bold uppercase tracking-wider">
                            Conoce quiénes somos
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link href="/ong" class="btn rounded-xl bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 text-xs font-bold uppercase tracking-wider px-6">
                            Proyectos de la ONG
                        </Link>
                    </div>
                </div>

                <div class="lg:col-span-3">
                    <div v-if="entradasRecientes?.length" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <Link v-for="(entrada, i) in entradasRecientes.slice(0, 6)" :key="entrada.slug" :href="entrada.ruta"
                            class="relative aspect-square rounded-xl overflow-hidden group bg-base-200"
                            :class="i === 0 ? 'row-span-2 col-span-2' : ''">
                            <img :src="entrada.imagen" :alt="entrada.titulo"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                                <span class="text-white text-xs font-bold leading-tight">{{ entrada.titulo }}</span>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Medita ahora -->
    <section class="relative bg-base-900 overflow-hidden min-h-[660px] lg:min-h-[740px] flex flex-col">
        <div class="absolute inset-0">
            <img src="/almacen/medios/portada/mujer_meditando.jpg" alt=""
                class="w-full h-full object-cover"
                style="object-position: 25% center" />
            <div class="absolute inset-0 bg-gradient-to-r from-base-content/80 via-base-content/40 to-base-content/20"></div>
        </div>

        <div class="relative flex-1 flex flex-col justify-center px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full">
            <div class="max-w-lg ml-auto">
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-xs font-bold tracking-wider uppercase text-white/70">Meditación diaria</span>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-display font-black text-white tracking-tight leading-tight mb-4">
                        Conecta con tu universo interior
                    </h2>
                    <p class="text-white/60 text-sm leading-relaxed mb-6">
                        Meditaciones y reflexiones de nuestros Guías Estelares para el autoconocimiento y el despertar de la consciencia.
                    </p>

                    <button @click="toggleMeditacion" class="w-full flex items-center justify-center gap-3 px-5 py-3.5 rounded-xl bg-white text-base-content font-bold text-xs uppercase tracking-wider shadow-md hover:bg-base-200 transition-all cursor-pointer">
                        <svg v-if="esMeditacionActiva && player.state === 'playing'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><rect x="6" y="4" width="4" height="16" rx="1"/><rect x="14" y="4" width="4" height="16" rx="1"/></svg>
                        <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        <template v-if="esMeditacionActiva && player.state === 'playing'">Escuchando</template>
                        <template v-else>Escuchar meditación diaria</template>
                    </button>

                    <Transition name="waveform">
                        <div v-if="esMeditacionActiva" class="mt-4 bg-base-200/20 rounded-xl p-5 border border-white/10">
                            <div class="flex items-end gap-[3px] h-8 mb-3">
                                <span v-for="i in 24" :key="i"
                                    class="w-[3px] rounded-full transition-all duration-150"
                                    :class="player.state === 'playing' ? 'bg-white/60 animate-waveform' : 'bg-white/10'"
                                    :style="player.state === 'playing' ? { animationDelay: `${i * 0.08}s`, height: `${Math.random() * 100}%` } : { height: '30%' }">
                                </span>
                            </div>

                            <div class="overflow-hidden relative" style="mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);">
                                <p class="text-sm font-semibold text-white whitespace-nowrap animate-marquee">
                                    <span>{{ meditacionActual?.titulo }}</span>
                                    <span class="inline-block mx-8 opacity-0">|</span>
                                    <span>{{ meditacionActual?.titulo }}</span>
                                </p>
                            </div>

                            <div class="mt-1.5 flex items-center gap-3">
                                <div @click="seekBar" class="flex-1 h-2 -my-0.5 flex items-center cursor-pointer group">
                                    <div class="w-full h-1 rounded-full bg-white/20 overflow-hidden group-hover:h-1.5 transition-all">
                                        <div class="h-full rounded-full bg-white" :style="{ width: player.duration > 0 ? `${(player.currentTime / player.duration) * 100}%` : '0%' }"></div>
                                    </div>
                                </div>
                                <span class="text-xs font-mono text-white/40 tabular-nums shrink-0">{{ tiempoActual }} / {{ duracionTotal }}</span>
                            </div>
                        </div>
                    </Transition>
                </div>
            </div>
        </div>

        <div class="relative px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto w-full pb-8 lg:pb-10 flex flex-wrap gap-2">
            <Link href="/audios" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-white text-xs font-bold uppercase tracking-wider transition-all">
                Biblioteca de audios
            </Link>
            <Link href="/meditaciones" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-white text-xs font-bold uppercase tracking-wider transition-all">
                Más meditaciones
            </Link>
            <Link href="/radio" class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/10 rounded-xl text-white text-xs font-bold uppercase tracking-wider transition-all">
                Radio TSEYOR 24h
            </Link>
        </div>
    </section>

    <!-- Biblioteca -->
    <section class="py-24 lg:py-32 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="relative min-h-[300px] lg:min-h-[400px] rounded-2xl overflow-hidden bg-base-200 order-2 lg:order-1">
                    <img src="/almacen/medios/portada/biblioteca_tseyor_2026.jpg" alt="Biblioteca Tseyor"
                        class="absolute inset-0 w-full h-full object-cover"
                        loading="lazy" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                </div>

                <div class="order-1 lg:order-2">
                    <div class="inline-flex items-center gap-2 bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full mb-6">
                        <BookOpen class="w-3.5 h-3.5" />
                        Biblioteca
                    </div>
                    <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight leading-tight mb-6">
                        Contenido libre y gratuito para tu camino
                    </h2>
                    <p class="text-base-content/60 leading-relaxed mb-8">
                        Cientos de libros, audios de meditaciones, vídeos, psicografías, comunicados de los Guías Estelares y mucho más. Todo basado en las referencias transmitidas a TSEYOR. Todo gratis, sin registro, sin condiciones.
                    </p>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <Link href="/libros" class="flex items-center gap-3 p-4 rounded-xl bg-base-200/50 hover:bg-primary/5 border border-base-200 hover:border-primary/20 transition-all group">
                            <span class="text-2xl font-display font-black text-primary">{{ formatearNumero(stats.libros) }}</span>
                            <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider leading-tight">Libros<br/>gratis</span>
                        </Link>
                        <Link href="/audios" class="flex items-center gap-3 p-4 rounded-xl bg-base-200/50 hover:bg-primary/5 border border-base-200 hover:border-primary/20 transition-all group">
                            <span class="text-2xl font-display font-black text-primary">{{ formatearNumero(stats.audios) }}</span>
                            <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider leading-tight">Audios<br/>disponibles</span>
                        </Link>
                        <Link href="/comunicados" class="flex items-center gap-3 p-4 rounded-xl bg-base-200/50 hover:bg-primary/5 border border-base-200 hover:border-primary/20 transition-all group">
                            <span class="text-2xl font-display font-black text-primary">{{ formatearNumero(stats.comunicados) }}</span>
                            <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider leading-tight">Comunicados<br/>de guías</span>
                        </Link>
                        <Link href="/videos" class="flex items-center gap-3 p-4 rounded-xl bg-base-200/50 hover:bg-primary/5 border border-base-200 hover:border-primary/20 transition-all group">
                            <span class="text-2xl font-display font-black text-primary">{{ formatearNumero(stats.videos) || '0' }}</span>
                            <span class="text-xs font-bold text-base-content/50 uppercase tracking-wider leading-tight">Vídeos<br/>y más</span>
                        </Link>
                    </div>

                    <Link href="/biblioteca" class="inline-flex items-center gap-2 text-primary font-bold text-xs uppercase tracking-wider hover:text-base-content transition-colors">
                        Acceder a la biblioteca completa
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-24 lg:py-32 bg-primary/5">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 bg-secondary/10 text-secondary text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full mb-6">
                    <HelpCircle class="w-3.5 h-3.5" />
                    Preguntas frecuentes
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight leading-tight">
                    Respuestas a tus dudas
                </h2>
                <p class="text-base-content/60 mt-3 text-sm leading-relaxed">
                    Lo que siempre quisiste saber y nunca te atreviste a preguntar.
                </p>
            </div>

            <div class="space-y-3">
                <details class="group rounded-xl bg-base-100 border border-base-200 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base-content text-sm">
                        ¿Cómo se comunican los Guías Estelares con vosotros?
                        <ChevronDown class="w-4 h-4 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-100 pt-4">
                        A través de nuestro canalizador <strong>Chac-Mool Puente</strong> mediante la <strong>telepatía externa</strong>. Este proceso lleva más de 40 años documentándose y está disponible públicamente en la web. No hay trance ni pérdida de consciencia — es una comunicación lúcida y verificable.
                    </p>
                </details>
                <details class="group rounded-xl bg-base-100 border border-base-200 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base-content text-sm">
                        ¿De verdad existe contacto extraterrestre?
                        <ChevronDown class="w-4 h-4 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-100 pt-4">
                        Es una pregunta legítima. No te pedimos que creas, sino que <strong>examines la evidencia</strong>: más de 40 años de comunicados públicos, cientos de libros, psicografías y testimonios de personas que han vivido este proceso. Cada cual debe llegar a sus propias conclusiones a través de la experimentación personal.
                    </p>
                </details>
                <details class="group rounded-xl bg-base-100 border border-base-200 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base-content text-sm">
                        ¿Hay que tener alguna creencia para participar?
                        <ChevronDown class="w-4 h-4 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-100 pt-4">
                        Ninguna. No hay dogmas, no hay credo que suscribir. Puedes ser ateo, agnóstico, espiritual o religioso. Lo único que se pide es apertura para explorar y disposición para compartir desde el respeto.
                    </p>
                </details>
                <details class="group rounded-xl bg-base-100 border border-base-200 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base-content text-sm">
                        ¿Hay que pagar algo? ¿Hay compromiso?
                        <ChevronDown class="w-4 h-4 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-100 pt-4">
                        No y no. Todo es gratuito: libros, audios, curso, eventos. Y no hay compromiso: puedes explorar, leer, escuchar y participar sin ninguna obligación. Tampoco hay plazos ni permanencia.
                    </p>
                </details>
                <details class="group rounded-xl bg-base-100 border border-base-200 p-5 cursor-pointer [&_summary::-webkit-details-marker]:hidden hover:border-primary/20 transition-colors">
                    <summary class="flex items-center justify-between gap-4 font-display font-bold text-base-content text-sm">
                        ¿Cómo puedo empezar?
                        <ChevronDown class="w-4 h-4 text-base-content/30 shrink-0 transition-transform group-open:rotate-180" />
                    </summary>
                    <p class="mt-4 text-sm text-base-content/60 leading-relaxed border-t border-base-100 pt-4">
                        La puerta de entrada recomendada es el <Link href="/cursos" class="text-primary hover:underline">curso holístico</Link>: es gratuito, tutelado y te da el contexto completo. También puedes explorar la <Link href="/biblioteca" class="text-primary hover:underline">biblioteca</Link> o escuchar las <Link href="/audios" class="text-primary hover:underline">meditaciones</Link>.
                    </p>
                </details>
            </div>

            <div class="text-center mt-10">
                <Link href="/preguntas-frecuentes" class="text-xs font-bold text-primary hover:text-base-content transition-colors uppercase tracking-wider">
                    Ver todas las preguntas →
                </Link>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="py-24 bg-base-100">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider px-3 py-1.5 rounded-full mb-6">
                <Mail class="w-3.5 h-3.5" />
                Boletín
            </div>
            <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight leading-tight mb-4">
                Mantente al día
            </h2>
            <p class="text-base-content/60 text-sm max-w-xl mx-auto mb-8 leading-relaxed">
                Recibe puntualmente en tu correo las últimas actualizaciones de comunicados, nuevos libros en PDF y la programación de nuestros próximos encuentros virtuales.
            </p>
            <Suscribe class="mx-auto"/>
        </div>
    </section>

    <!-- CTA final -->
    <section class="relative py-24 lg:py-32 overflow-hidden">
        <div class="absolute inset-0">
            <img src="/almacen/medios/portada/final.jpg" alt=""
                class="w-full h-full object-cover" />
            <div class="absolute inset-0 bg-gradient-to-br from-primary/90 via-primary/85 to-primary/80"></div>
        </div>
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-black text-white tracking-tight leading-tight mb-6">
                El primer paso es<br/>
                <span class="text-white/80">decidir explorar</span>
            </h2>
            <p class="text-white/70 text-sm sm:text-base leading-relaxed max-w-xl mx-auto mb-10">
                El curso holístico es la puerta de entrada a la comunidad. En él conocerás la filosofía, las herramientas de trabajo interior y el camino para integrarte como voluntario en la ONG.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link href="/cursos" class="btn rounded-xl bg-white text-base-content border-0 hover:bg-base-200 px-8 font-bold text-xs uppercase tracking-wider shadow-lg transition-all duration-300 hover:-translate-y-0.5">
                    Conocer el curso
                    <ArrowRight class="w-4 h-4" />
                </Link>
                <Link href="/biblioteca" class="btn rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs uppercase tracking-wider transition-all backdrop-blur-sm">
                    Explorar contenidos libres
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import Suscribe from "@/Components/Suscribe.vue"
import usePlayer from '@/Stores/player.js'
import { getSrcUrl } from '@/composables/srcutils.js'

import {
  ArrowRight,
  BookOpen,
  Calendar,
  ChevronDown,
  ChevronRight,
  HelpCircle,
  Mail,
  Users,
  Stars,
  HeartHandshake,
} from 'lucide-vue-next'

const player = usePlayer()

const scrollSeccion = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

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

function formatoDuracion(segundos) {
    if (!segundos || !isFinite(segundos)) return '0:00'
    const m = Math.floor(segundos / 60)
    const s = Math.floor(segundos % 60)
    return `${m}:${s.toString().padStart(2, '0')}`
}

const tiempoActual = computed(() => formatoDuracion(player.currentTime))
const duracionTotal = computed(() => formatoDuracion(player.duration))
</script>

<style scoped>
@keyframes waveform {
  0%, 100% { transform: scaleY(0.4); opacity: 0.4; }
  50% { transform: scaleY(1); opacity: 1; }
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
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
</style>
