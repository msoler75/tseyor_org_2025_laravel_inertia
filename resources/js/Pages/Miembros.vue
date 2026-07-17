<template>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto">
        <div class="flex items-center gap-3 mb-8 sm:mb-10">
            <Avatar imageClass="w-11 h-11 sm:w-14 sm:h-14 ring-2 ring-secondary/30 rounded-full" :user="$page.props.auth.user" :link="false" />
            <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-2xl font-bold text-base-content my-0">{{ $page.props.auth.user.name }}</h1>
                <p class="text-sm text-base-content/40 my-0">Tu espacio en TSEYOR</p>
            </div>
            <FontSizeControls />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-for="section in sections" :key="section.id"
                :class="section.wide ? 'md:col-span-2' : ''"
                class="border border-base-300 rounded-xl overflow-hidden bg-base-100">
                <div class="bg-base-200/80 px-4 py-2 flex items-center gap-2 border-b border-base-300">
                    <Icon :icon="section.titleIcon" class="text-base" :class="section.titleColor" />
                    <span class="text-sm font-semibold text-base-content/60 uppercase tracking-wider">{{ section.title }}</span>
                </div>
                <div class="p-2 grid gap-1" :class="section.wide && section.items.length >= 3 ? 'grid-cols-2 sm:grid-cols-4' : 'grid-cols-2'">
                    <component
                        v-for="item in section.items.filter(i => i.show !== false)"
                        :key="item.label"
                        :is="item.external ? 'a' : Link"
                        :href="item.to"
                        :target="item.external ? '_blank' : undefined"
                        :rel="item.external ? 'noopener noreferrer' : undefined"
                        class="group flex flex-col items-center gap-2 p-2 rounded-lg hover:bg-base-200/50 transition-colors duration-150">
                        <div class="shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center transition-colors duration-200"
                            :class="iconClasses(item.color)">
                            <Icon :icon="item.icon" class="text-4xl" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-sm font-semibold text-base-content/70">{{ item.label }}</span>
                            <span v-if="item.badge" class="text-sm font-bold ml-1.5" :class="item.badgeClasses">{{ item.badge }}</span>
                        </div>
                    </component>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import useUserStore from '@/Stores/user'

const page = usePage()
const userStore = useUserStore()

const props = defineProps({
    misEquipos: { type: Number, default: 0 },
    esMuul: { type: Boolean, default: false },
    esIniciado: { type: Boolean, default: false }
})

const iconClasses = (color) => {
    const map = {
        primary: 'bg-primary/10 text-primary group-hover:bg-primary group-hover:text-white',
        secondary: 'bg-secondary/10 text-secondary group-hover:bg-secondary group-hover:text-white',
        accent: 'bg-accent/10 text-accent group-hover:bg-accent group-hover:text-white',
        warning: 'bg-warning/10 text-warning group-hover:bg-warning group-hover:text-white',
        neutral: 'bg-neutral/10 text-neutral group-hover:bg-neutral group-hover:text-white',
        error: 'bg-error/10 text-error group-hover:bg-error group-hover:text-white',
        success: 'bg-success/10 text-success group-hover:bg-success group-hover:text-white',
    }
    return map[color] || map.primary
}

const sections = computed(() => {
    const uid = page.props.auth.user.id
    const hasMuular = userStore.saldo !== 'Error'

    const all = [
        {
            id: 'mi-espacio',
            title: 'mi espacio',
            titleIcon: 'ph:user-duotone',
            titleColor: 'text-primary',
            wide: true,
            items: [
                { label: 'Mi Perfil',      icon: 'ph:user-circle-duotone',       color: 'primary',   to: route('usuario', uid) },
                { label: 'Mis Archivos',   icon: 'ph:folder-duotone',             color: 'accent',    to: route('mis_archivos') },
                { label: 'Mis Equipos',    icon: 'ph:users-three-duotone',        color: 'secondary', to: '/equipos?categoria=Mis equipos' },
                { label: 'Mi Cuenta',      icon: 'ph:gear-six-duotone',           color: 'neutral',   to: route('profile.show') },
            ]
        },
        {
            id: 'muular',
            title: 'muular electrónico',
            titleIcon: 'ph:coin-duotone',
            titleColor: 'text-secondary',
            wide: !props.esIniciado,
            items: [
                { label: 'Muulares',     icon: 'ph:currency-circle-dollar-duotone', color: 'secondary', to: '/muular-electronico', badge: userStore.saldo ?? '0', badgeClasses: 'text-info-content bg-info rounded-md p-1', show: hasMuular },
                { label: 'Solicitar',  icon: 'ph:currency-circle-dollar-duotone', color: 'warning',   to: '/contactar',                                   show: !hasMuular },
                { label: '¿Qué es?',   icon: 'ph:info-duotone',                   color: 'primary',   to: '/muular' },
            ]
        },
        {
            id: 'iniciados',
            title: 'iniciados (interiorización)',
            titleIcon: 'solar:meditation-round-bold-duotone',
            titleColor: 'text-accent',
            wide: false,
            show: props.esIniciado,
            items: [
                { label: 'Interiorización', icon: 'solar:meditation-round-bold-duotone', color: 'accent',  to: route('equipo', 'iniciados-interiorizacion') },
                { label: 'Carpeta de iniciados',        icon: 'ph:folder-duotone',  color: 'primary', to: '/archivos/interiorizacion' },
            ]
        },
        {
            id: 'trabajos',
            title: 'trabajos de la comunidad',
            titleIcon: 'ph:users-four-duotone',
            titleColor: 'text-accent',
            wide: true,
            items: [
                { label: 'Equipos',      icon: 'ph:users-duotone',   color: 'secondary', to: route('equipos') },
                { label: 'Experiencias', icon: 'whh:thinking',    color: 'accent',    to: route('experiencias') },
                { label: 'Archivos',     icon: 'ph:archive-duotone', color: 'primary',   to: route('archivos0') },
                { label: 'Arte',         icon: 'ph:palette-duotone', color: 'warning',   to: route('trabajos.arte') },
            ]
        },
        {
            id: 'herramientas',
            title: 'herramientas',
            titleIcon: 'ph:wrench-duotone',
            titleColor: 'text-warning',
            wide: false,
            items: [
                { label: 'Tseyor Canva', icon: 'ph:palette-duotone',     color: 'accent',  to: '/tseyor-canva' },
                { label: 'Puzle', icon: 'ph:puzzle-piece-duotone', color: 'primary', to: 'https://puzle.tseyor.org/', external: true },
            ]
        },
        {
            id: 'comunidad',
            title: 'comunidad',
            titleIcon: 'ph:user-list-duotone',
            titleColor: 'text-primary',
            wide: false,
            items: [
                { label: 'Usuarios', icon: 'ph:users-duotone',        color: 'primary',   to: route('usuarios') },
                { label: 'Salas',    icon: 'ph:chat-circle-duotone',  color: 'secondary', to: route('salas') },
            ]
        },
        {
            id: 'espacio-muul',
            title: 'espacio muul',
            titleIcon: 'icon-park-twotone:eagle',
            titleColor: 'text-primary',
            wide: true,
            show: props.esMuul,
            items: [
                { label: 'Espacio Muul',   icon: 'icon-park-twotone:eagle',             color: 'accent',    to: '/muul' },
                { label: 'Tarjeta Visita', icon: 'ph:identification-card-duotone', color: 'primary',   to: route('tarjeta.visita') },
                { label: 'Correos @tseyor.org',        icon: 'ph:envelope-duotone',            color: 'secondary', to: '/muul/correos.tseyor' },
            ]
        },
        {
            id: 'admin',
            title: 'administración',
            titleIcon: 'ph:shield-check-duotone',
            titleColor: 'text-error',
            wide: false,
            show: userStore.permisos.length > 0,
            items: [
                { label: 'Panel Admin', icon: 'ph:shield-duotone', color: 'error', to: '/admin/dashboard' },
            ]
        },
    ]

    return all.filter(s => s.show !== false)
})
</script>
