<template>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        <div class="flex items-center gap-5 mb-10">
            <Avatar imageClass="w-16 h-16 ring-4 ring-secondary/30 rounded-full" :user="$page.props.auth.user" :link="false" />
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">
                    Bienvenid@, {{ $page.props.auth.user.name }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Tu espacio personal en TSEYOR</p>
            </div>
        </div>

        <section class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:user-duotone" class="text-xl" />
                mi espacio
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link :href="route('usuario', $page.props.auth.user.id)"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:user-circle-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Mi Perfil</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tu perfil público y datos personales</p>
                    </div>
                </Link>

                <Link :href="route('mis_archivos')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:folder-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Mis Archivos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tus documentos, imágenes y recursos</p>
                    </div>
                </Link>

                <Link href="/equipos?categoria=Mis equipos"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:users-three-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Mis Equipos</h3>
                        <p v-if="misEquipos > 0" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Participas en {{ misEquipos }} equipo{{ misEquipos !== 1 ? 's' : '' }}</p>
                        <p v-else class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Aún no formas parte de ningún equipo</p>
                    </div>
                </Link>

                <Link :href="route('profile.show')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:gear-six-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Mi Cuenta</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Configuración de tu cuenta y preferencias</p>
                    </div>
                </Link>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:coin-duotone" class="text-xl" />
                Muular
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a v-if="userStore.saldo !== 'Error'" href="/muular-electronico"
                    class="group col-span-1 sm:col-span-2 lg:col-span-2 relative overflow-hidden rounded-xl bg-gradient-to-br from-rose-500 via-rose-600 to-rose-700 shadow-md hover:shadow-xl transition-all duration-300">
                    <div class="absolute inset-0 opacity-[0.06]">
                        <Icon icon="ph:currency-circle-dollar-duotone" class="text-[300px] absolute -right-16 -top-16 text-white" />
                    </div>
                    <div class="relative p-6">
                        <div class="flex items-center gap-4">
                            <div class="shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-white/20 backdrop-blur-sm">
                                <Icon icon="ph:currency-circle-dollar-duotone" class="text-3xl text-white" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-white/90 text-lg">Muular Electrónico</h3>
                                <p class="text-white/60 text-sm">Tu moneda digital TSEYOR</p>
                            </div>
                        </div>
                        <div class="mt-5 flex items-baseline gap-2">
                            <span class="text-5xl font-bold tracking-tight text-white">{{ userStore.saldo ?? '0' }}</span>
                            <span class="text-white/60 text-lg font-medium">muulares</span>
                        </div>
                        <div class="mt-4 flex items-center gap-2 text-white/80 text-sm font-medium group-hover:text-white transition-colors">
                            <span>Gestionar mi muular</span>
                            <Icon icon="ph:arrow-right" class="text-lg transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>
                </a>
                <a v-else href="/contactar"
                    class="group col-span-1 sm:col-span-2 lg:col-span-2 relative overflow-hidden rounded-xl bg-gradient-to-br from-rose-500/80 to-rose-700/80 shadow-md hover:shadow-xl transition-all duration-300 border-2 border-dashed border-rose-300 dark:border-rose-600">
                    <div class="relative p-6 flex items-center gap-5">
                        <div class="shrink-0 w-14 h-14 flex items-center justify-center rounded-full bg-white/20">
                            <Icon icon="ph:currency-circle-dollar-duotone" class="text-3xl text-white" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-white text-lg">Muular Electrónico</h3>
                            <p class="text-white/70 text-sm mt-1">Solicita tu muular electrónico y únete a la economía TSEYOR</p>
                        </div>
                    </div>
                </a>

                <a href="/muular"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 group-hover:bg-rose-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:info-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">¿Qué es el Muular?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Conoce nuestra moneda de intercambio</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:users-four-duotone" class="text-xl" />
                Trabajos de la comunidad
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link :href="route('equipos')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:users-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Equipos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Colabora en equipos de trabajo</p>
                    </div>
                </Link>

                <Link :href="route('experiencias')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:star-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Experiencias</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Comparte y lee experiencias de la comunidad</p>
                    </div>
                </Link>

                <Link :href="route('archivos0')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 group-hover:bg-sky-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:archive-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Archivos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Documentos y recursos compartidos</p>
                    </div>
                </Link>

                <Link :href="route('trabajos.arte')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-violet-100 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 group-hover:bg-violet-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:palette-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Trabajos de Arte</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Creaciones artísticas de la comunidad</p>
                    </div>
                </Link>

                <Link :href="route('usuarios')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:user-list-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Usuarios</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Conoce a los miembros de la comunidad</p>
                    </div>
                </Link>

                <Link :href="route('salas')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 group-hover:bg-teal-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:chat-circle-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Salas</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Espacios de conversación y encuentro</p>
                    </div>
                </Link>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:wrench-duotone" class="text-xl" />
                Herramientas de la comunidad
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="/tseyor-canva"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:palette-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">CANVA Tseyor</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Herramienta de diseño colaborativo</p>
                    </div>
                </a>

                <a href="https://puzle.tseyor.org/" target="_blank" rel="noopener noreferrer"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 group-hover:bg-teal-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:puzzle-piece-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Puzle</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Explora el puzle holográfico cuántico</p>
                    </div>
                </a>
            </div>
        </section>

        <section v-if="esIniciado" class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:flower-lotus-duotone" class="text-xl" />
                Iniciados en Interiorización
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link :href="route('equipo', 'iniciados-interiorizacion')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-indigo-200 dark:border-indigo-800 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:compass-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Espacio de Interiorización</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Accede al espacio del equipo</p>
                    </div>
                </Link>

                <a href="/archivos/interiorizacion"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-indigo-200 dark:border-indigo-800 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:folder-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Archivos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Documentos y recursos de interiorización</p>
                    </div>
                </a>
            </div>
        </section>

        <section v-if="esMuul" class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:star-four-duotone" class="text-xl" />
                Espacio Muul
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="/muul"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-amber-200 dark:border-amber-800 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:compass-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Espacio Muul</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Tu centro de recursos muul</p>
                    </div>
                </a>

                <Link :href="route('tarjeta.visita')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-amber-200 dark:border-amber-800 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:identification-card-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Tarjeta de Visita</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Crea y personaliza tu tarjeta de presentación</p>
                    </div>
                </Link>

                <a href="/muul/correos.tseyor"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-amber-200 dark:border-amber-800 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:envelope-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Correos @tseyor.org</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gestiona tu correo electrónico TSEYOR</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:globe-duotone" class="text-xl" />
                Herramientas de la comunidad
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link :href="route('archivos0')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-900/30 text-slate-600 dark:text-slate-400 group-hover:bg-slate-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:archive-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Archivos de la comunidad</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Documentos, presentaciones y recursos compartidos</p>
                    </div>
                </Link>
            </div>
        </section>

        <section v-if="userStore.permisos.length">
            <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-5 flex items-center gap-2">
                <Icon icon="ph:shield-check-duotone" class="text-xl" />
                Administración
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <a href="/admin/dashboard"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 group-hover:bg-red-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:shield-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Panel de Administración</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Gestiona contenidos, usuarios y configuración</p>
                    </div>
                </a>
            </div>
        </section>
    </div>
</template>

<script setup>
import useUserStore from '@/Stores/user'

const userStore = useUserStore()

defineProps({
    misEquipos: {
        type: Number,
        default: 0
    },
    esMuul: {
        type: Boolean,
        default: false
    },
    esIniciado: {
        type: Boolean,
        default: false
    }
})
</script>
