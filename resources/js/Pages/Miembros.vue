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

                <a v-if="userStore.saldo !== 'Error'" href="/muular-electronico"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:currency-circle-dollar-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Muular Electrónico</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Saldo:</p>
                        <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ userStore.saldo ?? '0' }} <span class="text-sm font-normal text-gray-500 dark:text-gray-400">muulares</span></p>
                    </div>
                </a>
                <a v-else href="/contactar"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:currency-circle-dollar-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Muular Electrónico</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Solicita tu muular electrónico</p>
                    </div>
                </a>

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
                <Icon icon="ph:wrench-duotone" class="text-xl" />
                Herramientas
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Link :href="route('equipos')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:users-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Explorar Equipos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Descubre y únete a equipos de trabajo</p>
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

                <Link :href="route('novedades')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 group-hover:bg-sky-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:clock-counter-clockwise-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Novedades</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Últimas actualizaciones y contenido reciente</p>
                    </div>
                </Link>

                <Link v-if="$page.props.auth.user?.tiene_inscripciones_asignadas" :href="route('inscripciones.mis-asignaciones')"
                    class="group flex items-start gap-4 p-5 rounded-xl bg-white dark:bg-gray-800 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-700 transition-all duration-200">
                    <div class="shrink-0 w-12 h-12 flex items-center justify-center rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-200">
                        <Icon icon="ph:clipboard-text-duotone" class="text-2xl" />
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200 group-hover:text-secondary transition-colors">Gestión de inscritos</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Administra inscripciones asignadas</p>
                    </div>
                </Link>

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

                <a href="https://puzle.tseyor.org/" target="_blank"
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
    }
})
</script>
