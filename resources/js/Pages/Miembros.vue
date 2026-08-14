<template>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <div class="flex items-center gap-3 mb-6 sm:mb-8">
            <Avatar imageClass="w-11 h-11 sm:w-14 sm:h-14 ring-2 ring-secondary/30 rounded-full" :user="$page.props.auth.user" :link="false" />
            <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-2xl font-bold text-base-content my-0">{{ $page.props.auth.user.name }}</h1>
                <p class="text-sm text-base-content/40 my-0">Tu espacio en TSEYOR</p>
            </div>
            <FontSizeControls />
        </div>

        <VistaMosaicoCompactoNueva :sections="sections" :icon-classes="iconClasses" />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import useUserStore from '@/Stores/user'

import VistaMosaicoCompactoNueva from './Miembros/VistaMosaicoCompactoNueva.vue'

const page = usePage()
const userStore = useUserStore()

const props = defineProps({
    misEquipos: { type: Number, default: 0 },
    esMuul: { type: Boolean, default: false },
    esIniciado: { type: Boolean, default: false },
    inscripcionesPendientes: { type: Number, default: 0 },
    inscripcionesTotales: { type: Number, default: 0 }
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
            items: [
                { label: 'Mi Perfil',      icon: 'ph:user-circle-duotone',       color: 'primary',   to: route('usuario', uid), tooltip: 'Ver tu perfil público' },
                { label: 'Mis Archivos',   icon: 'ph:folder-duotone',             color: 'accent',    to: route('mis_archivos'), tooltip: 'Gestionar tus archivos' },
                { label: 'Mis Equipos',    icon: 'ph:users-three-duotone',        color: 'secondary', to: '/equipos?categoria=Mis equipos', tooltip: 'Ver tus equipos' },
                { label: 'Mi Cuenta',      icon: 'ph:gear-six-duotone',           color: 'primary',   to: route('profile.show'), tooltip: 'Configuración de tu cuenta' },
            ]
        },
        {
            id: 'muular',
            title: 'muular electrónico',
            titleIcon: 'ph:coin-duotone',
            titleColor: 'text-secondary',
            items: [
                { label: 'Muulares',     icon: 'ph:currency-circle-dollar-duotone', color: 'secondary', to: '/muular-electronico', badge: userStore.saldo ?? '0', badgeClasses: 'text-info-content bg-info rounded-md p-1', badgeSummary: true, show: hasMuular, external: true, tooltip: 'Tu saldo de muulares' },
                { label: 'Solicitar',  icon: 'ph:currency-circle-dollar-duotone', color: 'warning',   to: '/contactar',                                   show: !hasMuular, tooltip: 'Solicitar muulares electrónicos' },
                { label: '¿Qué es?',   icon: 'ph:info-duotone',                   color: 'primary',   to: '/muular', tooltip: 'Información sobre el muular electrónico' },
            ]
        },
        {
            id: 'iniciados',
            title: 'iniciados (interiorización)',
            titleIcon: 'solar:meditation-round-bold-duotone',
            titleColor: 'text-accent',
            show: props.esIniciado,
            items: [
                { label: 'Interiorización', icon: 'solar:meditation-round-bold-duotone', color: 'accent',  to: route('equipo', page.props.equipo_interiorizacion_id), tooltip: 'Acceder al equipo de interiorización' },
                { label: 'Comunicados de Interiorización', icon: 'ph:file-text-duotone', color: 'primary', to: route('comunicados-interiorizacion'), tooltip: 'Comunicados restringidos del equipo de interiorización' },
                { label: 'Carpeta de iniciados',        icon: 'ph:folder-duotone',  color: 'primary', to: '/archivos/equipos/interiorizacion', tooltip: 'Archivos del equipo de interiorización' },
            ]
        },
        {
            id: 'trabajos',
            title: 'trabajos de la comunidad',
            titleIcon: 'ph:users-four-duotone',
            titleColor: 'text-accent',
            items: [
                { label: 'Equipos',      icon: 'ph:users-duotone',   color: 'secondary', to: route('equipos'), tooltip: 'Explorar equipos de trabajo' },
                { label: 'Experiencias', icon: 'whh:thinking',    color: 'accent',    to: route('experiencias'), tooltip: 'Experiencias compartidas' },
                { label: 'Archivos',     icon: 'ph:archive-duotone', color: 'primary',   to: route('archivos0'), tooltip: 'Archivos de la comunidad' },
                { label: 'Arte',         icon: 'ph:palette-duotone', color: 'warning',   to: route('trabajos.arte'), tooltip: 'Galería de trabajos artísticos' },
            ]
        },
        {
            id: 'herramientas',
            title: 'herramientas',
            titleIcon: 'ph:wrench-duotone',
            titleColor: 'text-warning',
            items: [
                { label: 'Tseyor Canva', icon: 'ph:palette-duotone',     color: 'accent',  to: '/tseyor-canva', external: true, tooltip: 'Plantillas y diseños en Canva' },
                { label: 'Puzle', icon: 'ph:puzzle-piece-duotone', color: 'primary', to: 'https://puzle.tseyor.org/', external: true, tooltip: 'Juego del puzle de Tseyor' },
            ]
        },
        {
            id: 'comunidad',
            title: 'comunidad',
            titleIcon: 'ph:user-list-duotone',
            titleColor: 'text-primary',
            items: [
                { label: 'Usuarios', icon: 'ph:users-duotone',        color: 'primary',   to: route('usuarios'), tooltip: 'Listado de miembros' },
                { label: 'Salas',    icon: 'ph:chat-circle-duotone',  color: 'secondary', to: route('salas'), tooltip: 'Salas de chat' },
            ]
        },
        {
            id: 'espacio-muul',
            title: 'espacio muul',
            titleIcon: 'icon-park-twotone:eagle',
            titleColor: 'text-primary',
            show: props.esMuul,
            items: [
                { label: 'Espacio Muul',   icon: 'icon-park-twotone:eagle',             color: 'accent',    to: '/muul', tooltip: 'Portal del espacio Muul' },
                { label: 'Tarjeta Visita', icon: 'ph:identification-card-duotone', color: 'primary',   to: route('tarjeta.visita'), tooltip: 'Tu tarjeta de visita' },
                { label: 'Correos @tseyor.org',        icon: 'ph:envelope-duotone',            color: 'secondary', to: '/muul/correos.tseyor', tooltip: 'Gestión de correos @tseyor.org' },
            ]
        },
        {
            id: 'admin',
            title: 'administración',
            titleIcon: 'ph:shield-check-duotone',
            titleColor: 'text-error',
            show: userStore.permisos.length > 0 || props.inscripcionesTotales > 0,
            items: [
                { label: 'Panel Admin', icon: 'ph:shield-duotone', color: 'error', to: '/admin/dashboard', native: true, show: userStore.permisos.length > 0, tooltip: 'Panel de administración' },
                { label: 'Inscripciones al curso', icon: 'ph:clipboard-text-duotone', color: 'warning', to: route('inscripciones.mis-asignaciones'), badge: props.inscripcionesPendientes > 0 ? String(props.inscripcionesPendientes) : '✓', badgeClasses: props.inscripcionesPendientes > 0 ? 'bg-warning/20 text-warning text-xs font-bold px-1.5 py-0.5 rounded' : 'bg-success/20 text-success text-xs font-bold px-1.5 py-0.5 rounded', badgeTitle: props.inscripcionesPendientes > 0 ? '' : 'No tienes inscripciones pendientes', badgeSummary: true, show: props.inscripcionesTotales > 0, tooltip: 'Gestionar inscripciones al curso asignadas' },
            ]
        },
    ]

    return all.filter(s => s.show !== false)
})
</script>
