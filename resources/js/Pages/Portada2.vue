<template>
    <h1 class="hidden">Despertando a una nueva consciencia</h1>

    <!-- Hero -->
    <section id="hero" class="relative overflow-hidden pt-16 pb-24 md:py-32"
    style="background: url(/almacen/medios/portada/portada_tseyor_2026_hero.jpg) center center; background-size: cover"
    >

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="max-w-2xl bg-base-100/80 backdrop-blur-xs rounded-4xl p-7">
                <div class="inline-block px-1 py-1.5 text-xs font-bold tracking-widest uppercase text-primary font-display">
                    ONG MUNDO ARMÓNICO TSEYOR
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl font-display font-black text-secondary tracking-tight leading-[1.1] mb-8">
                    <span class="">Despertando a</span> <br class="hidden sm:inline" /><span class="text-primary
                    ">una nueva consciencia</span>
                </h1>

                <p class="text-lg md:text-xl text-base-content/90 leading-relaxed mb-8 max-w-2xl font-light">
                    Somos una ONG dedicada al despertar de la consciencia y la creación de las Sociedades Armónicas. Nuestros Guías Estelares nos tutelan en este proceso de transformación interior y reconexión cósmica.
                </p>

                <div class="flex items-center gap-3 mb-10 max-w-2xl">
                    <Info class="w-5 h-4 text-primary shrink-0 mt-0.5" />
                    <p class="text-sm text-primary/80 font-semibold leading-relaxed p-0 m-0">
                        No nos creas, compruébalo. Este es un camino de experimentación personal.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <Link href="/cursos" class="flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-md transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer group">
                        Comenzar el Curso
                        <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                    </Link>
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
    </section>

    <!-- Stats -->
    <section class="bg-base-100 py-12 border-t border-b border-base-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link href="/origenes-de-tseyor" class="bg-base-100 p-6 rounded-3xl border border-base-300 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-primary/30 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <Clock class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-base-content font-display leading-tight">+40 años</span>
                        <span class="block text-xs font-bold text-base-content/50 tracking-widest uppercase mt-0.5">de trayectoria</span>
                    </div>
                </Link>
                <Link href="/libros" class="bg-base-100 p-6 rounded-3xl border border-base-300 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-primary/30 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <BookOpen class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-base-content font-display leading-tight">+300 libros</span>
                        <span class="block text-xs font-bold text-base-content/50 tracking-widest uppercase mt-0.5">disponibles gratis</span>
                    </div>
                </Link>
                <Link href="/comunicados"  class="bg-base-100 p-6 rounded-3xl border border-base-300 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-primary/30 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <MessageSquare class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-base-content font-display leading-tight">+1.500</span>
                        <span class="block text-xs font-bold text-base-content/50 tracking-widest uppercase mt-0.5">comunicados de nuestros guías</span>
                    </div>
                </Link>
                <Link href="/biblioteca" class="bg-base-100 p-6 rounded-3xl border border-base-300 shadow-sm flex items-center gap-4 hover:shadow-md hover:border-primary/30 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <Infinity class="w-6 h-6" />
                    </div>
                    <div>
                        <span class="block text-2xl font-black text-base-content font-display leading-tight">Biblioteca</span>
                        <span class="block text-xs font-bold text-base-content/50 tracking-widest uppercase mt-0.5">descarga libre</span>
                    </div>
                </Link>
            </div>
        </div>
    </section>

    <!-- ONG -->
    <section id="ong" class="py-24 bg-base-200 border-t border-b border-base-300 relative">
        <div class="absolute inset-0 bg-[radial-gradient(#4f46e504_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="text-center max-w-full mx-auto mb-16">
                <Image class="mx-auto w-[320px] mb-8" src="/almacen/medios/logos/ong.jpg"/>
                <h2 class="text-3xl sm:text-4xl font-display font-black text-primary tracking-tight mb-6">
                    Una ONG de ayuda humanitaria
                </h2>
                <div class="flex flex-wrap justify-center gap-3 max-w-full mx-auto text-base-content/60">
                    <span class="flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-base-300 bg-base-100 text-xs font-bold shadow-xs uppercase tracking-wider">
                        <CheckCircle2 class="w-3.5 h-3.5 shrink-0 text-primary" />
                        Sin ánimo de lucro
                    </span>
                    <span class="flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-base-300 bg-base-100 text-xs font-bold shadow-xs uppercase tracking-wider">
                        <CheckCircle2 class="w-3.5 h-3.5 shrink-0 text-primary" />
                        Ayuda humanitaria
                    </span>
                    <span class="flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-base-300 bg-base-100 text-xs font-bold shadow-xs uppercase tracking-wider">
                        <CheckCircle2 class="w-3.5 h-3.5 shrink-0 text-primary" />
                        Cooperación al desarrollo
                    </span>
                    <span class="flex items-center gap-1.5 px-4 py-1.5 rounded-full border border-base-300 bg-base-100 text-xs font-bold shadow-xs uppercase tracking-wider">
                        <CheckCircle2 class="w-3.5 h-3.5 shrink-0 text-primary" />
                        Conocimiento libre y abierto
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
                <div class="md:col-span-5 comunidad-bg text-white/90 p-8 sm:p-10 rounded-3xl flex flex-col justify-between shadow-xl relative overflow-hidden group border border-base-300">
                    <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-base-100/5 rounded-full blur-2xl group-hover:scale-110 transition-transform pointer-events-none"></div>
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-base-100 flex items-center justify-center text-primary mb-8">
                            <Users class="w-6 h-6" />
                        </div>
                        <h3 class="text-2xl sm:text-3xl font-display font-bold tracking-tight mb-4 text-white">
                            Comunidad de miembros
                        </h3>
                        <p class="leading-relaxed mb-8 text-md font-light">
                            Únete a miles de personas en todo el mundo compartiendo un ideal puro de paz, apoyo recíproco, estudio consciente y evolución espiritual.
                        </p>
                        <div class="flex items-center gap-3 mb-8">
                            <div class="flex -space-x-2.5 overflow-hidden">
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-base-300 object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-base-300 object-cover" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-base-300 object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-base-300 object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-base-300 object-cover" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-base-300 object-cover" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=100&h=100&q=80" alt="" />
                            </div>
                            <span class="text-xs font-bold bg-base-100 px-2.5 py-1 rounded-full text-base-content uppercase tracking-widest">+1.2k activos</span>
                        </div>
                    </div>
                    <Link href="/ong" class="w-full text-center py-3.5 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest transition-all shadow-md hover:bg-base-200">
                        Conoce la ONG y sus proyectos <sup class="text-[8px] opacity-50">75%</sup>
                    </Link>
                </div>

                <div class="md:col-span-7 bg-base-100 p-8 sm:p-10 rounded-3xl border border-base-300 flex flex-col justify-between shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <Link  href="/biblioteca">
                        <div class="h-48 sm:h-56 w-full rounded-2xl overflow-hidden mb-6 relative bg-base-200">
                            <img src="/almacen/medios/portada/biblioteca_tseyor_2026.jpg" alt="Biblioteca Tseyor"
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-102 transition-transform duration-500"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-4 text-primary-content text-xs font-bold tracking-widest uppercase bg-primary px-3 py-1.5 rounded-full">
                                BIBLIOTECA VIRTUAL
                            </div>
                        </div>
                        <h3 class="text-2xl font-display font-bold text-base-content tracking-tight mb-3">
                            Contenido libre y gratuito
                        </h3>
                        <p class="text-base-content/80 leading-relaxed mb-6 text-md font-light">
                            Accede a mas de 400 libros, cientos de audios de meditaciones y reflexiones, vídeos, psicografías y mucho más, sin registro obligatorio ni cuotas.
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

    <!-- Wisdom: El mensaje desde las estrellas -->
    <section id="sabiduria" class="py-24 overflow-hidden bg-base-100">
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
                        Nuestra filosofía proviene de los <b>Guías Estelares</b> de la <b>Confederación de Mundos Habitados de la Galaxia</b>, integrando ciencia, espiritualidad y experimentación personal. Una cosmovisión integradora que nos invita a trascender dogmas caducos, explorar las infinitas capacidades de la consciencia y participar activamente en la creación de Sociedades Armónicas planetarias.
                    </p>

                    <div class="border-l-4 border-primary bg-primary/10 p-5 rounded-r-3xl mb-8">
                        <p class="text-primary/80 italic font-semibold text-sm">
                            "No se trata de creer en la presencia cósmica de los Guías Estelares, sino de comprobarlo a través de la propia experimentación personal."
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4">

                        <Link href="/guias" class="flex items-center justify-center gap-2 px-8 py-4 rounded-full bg-primary text-primary-content font-bold text-xs uppercase tracking-widest hover:bg-primary/90 transition-all shadow-md transform hover:-translate-y-0.5 active:translate-y-0 cursor-pointer group">
                            <Compass class="w-4 h-4" />
                            Conocer a los Guías Estelares
                            <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </Link>
                        <Link href="/comunicados" class="flex items-center justify-center gap-2 px-6 py-3 rounded-full bg-base-100 border border-base-300 hover:border-primary/40 text-base-content/80 hover:bg-base-200 font-bold text-xs uppercase tracking-widest transition-all text-center shadow-sm">
                            <FileText class="w-4 h-4 text-primary" />
                            Leer comunicados <sup class="text-[8px] opacity-50">35%</sup>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Por dónde empezar -->
    <section class="py-24 bg-base-200 border-t border-b border-base-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-14">
                <h2 class="text-primary text-3xl font-display font-black text-base-content tracking-tight">
                    ¿Por dónde empezar?
                </h2>
                <p class="text-base-content/90 text-md mt-2">
                    Ponemos a tu alcance diversas puertas de entrada para iniciar este viaje interior.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <Link href="/cursos" class="bg-base-100 p-6 rounded-3xl border border-base-300 hover:border-primary/30 hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 border border-primary/20">
                        <BookMarked class="w-6 h-6" />
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-base-content text-base mb-1">Curso Holístico</h4>
                        <p class="text-base-content/60 text-sm leading-relaxed font-light">Formación libre guiada por tutores experimentados para profundizar en el autoanálisis mental.</p>
                        <span class="text-primary font-bold text-xs uppercase tracking-wider mt-2 inline-block">Comenzar <sup class="text-[8px] opacity-50">85%</sup></span>
                    </div>
                </Link>
                <Link href="/biblioteca" class="bg-base-100 p-6 rounded-3xl border border-base-300 hover:border-primary/30 hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 border border-primary/20">
                        <BookOpen class="w-6 h-6" />
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-base-content text-base mb-1">Biblioteca Virtual</h4>
                        <p class="text-base-content/60 text-sm leading-relaxed font-light">Acceso directo a todos los libros inspirados, comunicados ordenados y grabaciones de conferencias.</p>
                        <span class="text-primary font-bold text-xs uppercase tracking-wider mt-2 inline-block">Explorar <sup class="text-[8px] opacity-50">25%</sup></span>
                    </div>
                </Link>
                <Link href="/blog" class="bg-base-100 p-6 rounded-3xl border border-base-300 hover:border-primary/30 hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 border border-primary/20">
                        <FileText class="w-6 h-6" />
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-base-content text-base mb-1">Blog de la comunidad</h4>
                        <p class="text-base-content/60 text-sm leading-relaxed font-light">Crónicas de encuentros, eventos, convivencias, experiencias de campo y otros momentos históricos del grupo.</p>
                        <span class="text-primary font-bold text-xs uppercase tracking-wider mt-2 inline-block">Leer <sup class="text-[8px] opacity-50">20%</sup></span>
                    </div>
                </Link>
                <Link v-if="hayProximosEventos" href="/eventos" class="bg-base-100 p-6 rounded-3xl border border-base-300 hover:border-primary/30 hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shrink-0 border border-primary/20">
                        <Calendar class="w-6 h-6" />
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-base-content text-base mb-1">Eventos y Encuentros</h4>
                        <p class="text-base-content/60 text-sm leading-relaxed font-light">Entérate de las próximas reuniones presenciales y videoconferencias abiertas para el diálogo sincero.</p>
                        <span class="text-primary font-bold text-xs uppercase tracking-wider mt-2 inline-block">Ver <sup class="text-[8px] opacity-50">10%</sup></span>
                    </div>
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
                        <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Qué es el grupo Tseyor?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Es esto una secta?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Qué significa la palabra Tseyor?</Link></li>
                    </ul>
                    <Link href="/preguntas-frecuentes/parte-3-sobre-tseyor" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-wider hover:text-base-content transition-colors">
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
                        <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Cómo contactan con nosotros?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Por qué están aquí?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Por qué no se presentan?</Link></li>
                    </ul>
                    <Link href="/preguntas-frecuentes/parte-1-extraterrestres" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-wider hover:text-base-content transition-colors">
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
                        <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿En qué consiste el cambio?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Cómo nos afectará?</Link></li>
                        <li><Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="text-sm text-base-content/60 hover:text-primary transition-colors">¿Podemos prepararnos?</Link></li>
                    </ul>
                    <Link href="/preguntas-frecuentes/parte-2-el-salto-cuantico" class="inline-flex items-center gap-1 text-xs font-bold text-primary uppercase tracking-wider hover:text-base-content transition-colors">
                        Ver más <ChevronRight class="w-3 h-3" /> <sup class="text-[8px] opacity-50">15%</sup>
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA final -->
    <section class="py-24 relative overflow-hidden bg-base-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-neutral border border-neutral rounded-3xl p-8 sm:p-14 lg:p-20 text-center text-white relative overflow-hidden shadow-xl">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,#4f46e520,transparent_50%)]"></div>
                <div class="relative max-w-2xl mx-auto">
                    <span class="inline-block text-xs font-bold tracking-widest bg-primary/10 text-primary/70 border border-primary/20 uppercase px-3 py-1.5 rounded-full mb-6 font-display">
                        EL MOMENTO ES AHORA
                    </span>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-display font-black tracking-tight leading-tight mb-6">
                        Comienza tu viaje
                    </h2>
                    <p class="text-white/60 leading-relaxed mb-10 text-sm sm:text-base font-light">
                        El camino del autodescubrimiento profundo está totalmente abierto para ti. Sin dogmas restrictivos, desde tu propia experiencia inteligente y libre de discernimiento.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link href="/cursos/inscripcion/nueva" class="w-full sm:w-auto px-6 py-3.5 rounded-full bg-white text-base-content font-bold text-xs uppercase tracking-widest shadow-sm transition-all duration-300 hover:bg-base-200">
                            Inscríbete al curso gratuito <sup class="text-[8px] opacity-50">90%</sup>
                        </Link>
                        <Link href="/biblioteca" class="w-full sm:w-auto px-6 py-3.5 rounded-full bg-white/5 hover:bg-white/10 border border-white/20 font-bold text-xs uppercase tracking-widest transition-all text-center">
                            Explorar contenidos libres <sup class="text-[8px] opacity-50">15%</sup>
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
  BookOpen,
  Calendar,
  Info,
  Clock,
  MessageSquare,
  Infinity,
  Compass,
  FileText,
  CheckCircle2,
  Users,
  BookMarked,
  ChevronRight,
  HelpCircle,
  Sparkles,
  Mail,
} from 'lucide-vue-next'

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

.comunidad-bg {
background-color: #1d55c8 ;
background:
linear-gradient(115deg, transparent 75%, rgba(0,32,128,.1) 75%) 0 0,
linear-gradient(245deg, transparent 75%, rgba(0,32,128,.1) 75%) 0 0,
linear-gradient(115deg, transparent 75%, rgba(0,32,128,.1) 75%) 10px -20px,
linear-gradient(245deg, transparent 75%, rgba(0,32,128,.1) 75%) 10px -20px,
#249;
background-size: 20px 40px;
}
</style>
