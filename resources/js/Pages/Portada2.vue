<template>
    <h1 class="hidden">ONG Mundo Armónico TSEYOR</h1>

    <!-- Hero -->
    <section id="hero" class="relative overflow-hidden py-16 md:pt-24"
    style="background: url(/almacen/medios/portada/portada_tseyor_2026_hero.jpg) center center; background-size: cover"
    >

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-2xl bg-base-100/90 backdrop-blur-xs rounded-4xl p-7 relative mt-12">

                <img src="/almacen/medios/portada/libelula.png?w=180" class="absolute right-10 -top-18"/>

                <div class="inline-block px-1 py-1.5 text-xs font-bold tracking-widest uppercase text-primary font-display">
                    ONG 
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl font-display font-black text-secondary tracking-tight leading-[1.1] mb-8">
                    <span class="">MUNDO ARMÓNICO </span> <br class="hidden sm:inline" /><span class="text-primary
                    ">TSEYOR</span>
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
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">+300 libros</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">disponibles gratis</span>
                    </div>
                </Link>
                <Link href="/comunicados" class="group bg-base-100/80 backdrop-blur-lg p-4 sm:p-5 rounded-2xl border border-base-300/50 shadow-lg hover:shadow-2xl hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">+1.500</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">comunicados de guías</span>
                    </div>
                </Link>
                <Link href="/biblioteca" class="group bg-base-100/80 backdrop-blur-lg p-4 sm:p-5 rounded-2xl border border-base-300/50 shadow-lg hover:shadow-2xl hover:border-primary/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-primary to-primary/70 shadow-inner flex items-center justify-center text-white shrink-0 group-hover:scale-110 group-hover:shadow-lg transition-all duration-300">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M3 16v5h5"/><path d="M21 16v5h-5"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>
                    </div>
                    <div>
                        <span class="block text-lg font-black text-base-content font-display leading-tight tracking-tight">Biblioteca</span>
                        <span class="block text-[11px] font-semibold text-base-content/40 tracking-widest uppercase mt-0.5">descarga libre</span>
                    </div>
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

    <!-- Sigue leyendo -->
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
                                <span><b class="text-white">Amistad y hermanamiento</b> — como una familia que crece unida superando obstáculos</span>
                            </li>
                        </ul>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex -space-x-2.5 overflow-hidden">
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white/30 object-cover" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                            </div>
                            <span class="text-xs font-bold bg-white/20 backdrop-blur-sm px-2.5 py-1 rounded-full text-white uppercase tracking-widest border border-white/10">+1.2k activos</span>
                        </div>
                    </div>
                    <div class="flex gap-4 justify-between">
                      <Link href="/quienes-somos" class="w-full text-center py-3.5 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest transition-all shadow-md hover:bg-base-200">
                            Conoce quiénes somos
                        </Link>
                        <Link href="/ong" class="w-full text-center py-3.5 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest transition-all shadow-md hover:bg-base-200">
                            Conoce la ONG y sus proyectos
                        </Link>
                    </div>
                </div>

                <div class="md:col-span-4 bg-base-100 p-8 sm:p-10 rounded-3xl border border-base-300 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <Link  href="/biblioteca">
                        <div class="h-48 sm:h-56 w-full rounded-2xl overflow-hidden mb-6 relative bg-base-200">
                            <img src="/almacen/medios/portada/biblioteca_tseyor_2026.jpg" alt="Biblioteca Tseyor"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
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
                            Accede a mas de 400 libros, cientos de audios de meditaciones y reflexiones, vídeos, psicografías y mucho más. Todo basado en las referencias de nuestros Guías Estelares.
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
    <section class="relative min-h-[500px] sm:min-h-[640px] bg-cover bg-center bg-no-repeat flex flex-col border-t border-b border-base-300"
        style="background-image: url('/almacen/medios/portada/mujer_meditando.jpg'); background-position: 25% center">

        <div class="absolute inset-0 bg-gradient-to-l from-black/20 via-black/10 to-black/40"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full flex flex-col grow">

            <div class="flex justify-end items-start pt-8 sm:pt-16">
                <div class="w-full sm:max-w-sm bg-base-100/95 backdrop-blur-sm rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl border border-base-300/40">
                    <div class="inline-block px-3 py-1 mb-3 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                        Meditación diaria
                    </div>
                    <h3 class="text-xl sm:text-2xl font-display font-black text-base-content tracking-tight leading-tight">
                        Siéntate y conecta con tu universo interior
                    </h3>
                    <p class="text-base-content/60 text-sm mt-2 leading-relaxed font-light">
                        Meditaciones y reflexiones de nuestros Guías Estelares para el autoconocimiento y el despertar de nuestra consciencia. 
                    </p>
                    <div class="mt-5 w-full flex items-center justify-center gap-3 px-5 py-3.5 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest shadow-md cursor-default">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                        Escuchar meditación diaria
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

    <!-- Descubre -->
    <section id="descubre" class="py-24 bg-base-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16 flex flex-col items-center">
                <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                    FILOSOFÍA TSEYOR
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-primary tracking-tight">
                    Temas clave de nuestra filosofía
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link v-for="pagina in paginasDescubre" :key="pagina.ruta" :href="pagina.ruta"
                    class="bg-base-100 rounded-3xl border border-base-300 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/40 transition-all duration-300 flex flex-col justify-between h-full group">
                    <div>
                        <div class="h-56 relative overflow-hidden bg-base-200">
                            <img :src="pagina.imagen+'?w=440'" :alt="pagina.titulo" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
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
                <Link href="/descubre" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary text-neutral-content font-bold text-xs uppercase tracking-widest hover:bg-primary/80 transition-all shadow-sm">
                    Ver todos los temas <sup class="text-[8px] opacity-50">55%</sup>
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- Community Life -->
    <section id="vida" class="py-24 bg-base-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-baseline justify-between mb-12 border-b border-base-300 pb-4">
                <h3 class="text-3xl font-display font-black text-pimary tracking-tight">
                    Vida de la comunidad
                </h3>
                <Link href="/blog" class="text-xs font-bold text-primary flex items-center gap-1 hover:text-base-content transition-colors uppercase tracking-wider">
                    Ver todo el blog
                    <ArrowRight class="w-3.5 h-3.5" />
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-16">
                <div v-if="entradasRecientes?.length" v-for="entrada in entradasRecientes.slice(0, 4)" :key="entrada.id"
                    class="flex flex-col sm:flex-row gap-6 bg-base-100 p-5 rounded-3xl border border-base-300 hover:border-primary/30 shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="sm:w-1/3 h-44 rounded-2xl overflow-hidden shrink-0 relative bg-base-200">
                        <img :src="entrada.imagen" :alt="entrada.titulo" class="absolute inset-0 w-full h-full object-cover" />
                    </div>
                    <div class="grow flex flex-col justify-between py-1">
                        <div>
                            <span class="text-xs font-bold text-base-content/50 tracking-widest uppercase block mb-1.5">{{ entrada.fecha }}</span>
                            <h4 class="font-display font-bold text-base-content text-lg mb-2">{{ entrada.titulo }}</h4>
                            <p class="text-base-content/60 text-sm leading-relaxed font-light">{{ entrada.descripcion.substr(0, 128) + "..." }}</p>
                        </div>
                        <Link :href="entrada.ruta" class="text-xs font-bold text-primary mt-4 block uppercase tracking-widest hover:text-base-content transition-colors">Leer artículo →</Link>
                    </div>
                </div>
            </div>

                
        </div>
    </section>

    <section  class="py-24 bg-base-300">
        <Card class="max-w-3xl mx-auto">
            <div class="flex flex-col items-center text-center">
                <div class="flex items-center gap-3 mb-4">
                    <Mail class="w-5 text-primary" />
                    <h4 class="text-xl font-display font-bold text-base-content mb-0">Boletín TSEYOR</h4>
                </div>
                <p class="text-base-content/60 text-sm mb-6 max-w-xl">Recibe puntualmente en tu correo electrónico las últimas actualizaciones de comunicados, nuevos libros en PDF y la programación de nuestros próximos encuentros virtuales gratuitos.</p>
                <div class="mx-auto">
                    <Suscribe />
                </div>
            </div>
        </Card>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-24 bg-base-200 border-t border-b border-base-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 flex flex-col items-center">
                <div class="inline-block px-3 py-1 mb-4 text-xs font-bold tracking-widest uppercase bg-primary/10 text-primary rounded-full border border-primary/20 font-display">
                    ACLARA TUS INQUIETUDES
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-base-content tracking-tight">
                    Preguntas frecuentes
                </h2>
                <p class="text-base-content/60 mt-4 text-sm font-light leading-relaxed">
                    Explora las respuestas a las preguntas más comunes sobre TSEYOR, los Guías Estelares y el Salto Cuántico.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div class="bg-base-100 rounded-3xl border border-base-300 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 space-y-4">
                    <div class="flex items-center gap-3 border-b border-base-200 pb-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary">
                            <HelpCircle class="w-4.5 h-4.5" />
                        </div>
                        <h3 class="font-display font-bold text-base-content text-sm uppercase tracking-wider">Sobre TSEYOR</h3>
                    </div>
                    <ul class="space-y-3">
                        <li><Link :href="route('preguntas.seccion', 'parte-3-sobre-tseyor')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Qué es el grupo Tseyor?</Link></li>
                        <li><Link :href="route('preguntas.seccion', 'parte-3-sobre-tseyor')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Es esto una secta?</Link></li>
                        <li><Link :href="route('preguntas.seccion', 'parte-3-sobre-tseyor')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Qué significa la palabra Tseyor?</Link></li>
                    </ul>
                    <Link :href="route('preguntas.seccion', 'parte-3-sobre-tseyor')" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-wider hover:text-base-content transition-colors">
                        Ver más <ChevronRight class="w-3 h-3" /> <sup class="text-[8px] opacity-50">15%</sup>
                    </Link>
                </div>
                <div class="bg-base-100 rounded-3xl border border-base-300 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 space-y-4">
                    <div class="flex items-center gap-3 border-b border-base-200 pb-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary">
                            <Compass class="w-4.5 h-4.5" />
                        </div>
                        <h3 class="font-display font-bold text-base-content text-sm uppercase tracking-wider">Guías Estelares</h3>
                    </div>
                    <ul class="space-y-3">
                        <li><Link :href="route('preguntas.seccion', 'parte-1-extraterrestres')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Cómo contactan con nosotros?</Link></li>
                        <li><Link :href="route('preguntas.seccion', 'parte-1-extraterrestres')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Por qué están aquí?</Link></li>
                        <li><Link :href="route('preguntas.seccion', 'parte-1-extraterrestres')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Por qué no se presentan?</Link></li>
                    </ul>
                    <Link :href="route('preguntas.seccion', 'parte-1-extraterrestres')" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-wider hover:text-base-content transition-colors">
                        Ver más <ChevronRight class="w-3 h-3" /> <sup class="text-[8px] opacity-50">15%</sup>
                    </Link>
                </div>
                <div class="bg-base-100 rounded-3xl border border-base-300 overflow-hidden shadow-sm hover:shadow-md hover:border-primary/30 transition-all duration-300 p-6 space-y-4">
                    <div class="flex items-center gap-3 border-b border-base-200 pb-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 border border-primary/20 flex items-center justify-center text-primary">
                            <Sparkles class="w-4.5 h-4.5" />
                        </div>
                        <h3 class="font-display font-bold text-base-content text-sm uppercase tracking-wider">Salto Cuántico</h3>
                    </div>
                    <ul class="space-y-3">
                        <li><Link :href="route('preguntas.seccion', 'parte-2-el-salto-cuantico')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿En qué consiste el cambio?</Link></li>
                        <li><Link :href="route('preguntas.seccion', 'parte-2-el-salto-cuantico')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Cómo nos afectará?</Link></li>
                        <li><Link :href="route('preguntas.seccion', 'parte-2-el-salto-cuantico')" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Podemos prepararnos?</Link></li>
                    </ul>
                    <Link :href="route('preguntas.seccion', 'parte-2-el-salto-cuantico')" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-wider hover:text-base-content transition-colors">
                        Ver más <ChevronRight class="w-3 h-3" /> <sup class="text-[8px] opacity-50">15%</sup>
                    </Link>
                </div>
            </div>
        </div>
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
                            Quiero inscribirme ahora
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
import Suscribe from "@/Components/Suscribe.vue"

import {
  ArrowRight,
  ArrowDown,
  BookOpen,
  Calendar,
  Info,

  Compass,
  FileText,
  CheckCircle2,
  HeartHandshake,
  ChevronRight,
  HelpCircle,
  Sparkles,
  Mail,
} from 'lucide-vue-next'

const scrollSeccion = (id) => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

defineProps({
    hayProximosEventos: Boolean,
    paginasDescubre: {
        type: Array,
        default: () => [],
    },
    entradasRecientes: {
        type: Array,
        default: () => [],
    },
})
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

.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}

.animate-scaleUp {
  animation: scaleUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}


</style>
