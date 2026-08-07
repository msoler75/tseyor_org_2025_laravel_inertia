<template>
    <div class="py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto" :class="{ 'pb-16': minimized.length }">
        <div class="flex items-center gap-3 mb-8 sm:mb-10">
            <Avatar imageClass="w-11 h-11 sm:w-14 sm:h-14 ring-2 ring-secondary/30 rounded-full" :user="$page.props.auth.user" :link="false" />
            <div class="min-w-0 flex-1">
                <h1 class="text-lg sm:text-2xl font-bold text-base-content my-0">{{ $page.props.auth.user.name }}</h1>
                <p class="text-sm text-base-content/40 my-0">Tu espacio en TSEYOR</p>
            </div>
            <FontSizeControls />
        </div>

        <div ref="panelContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-20">
            <template v-for="id in panelOrder" :key="id">
                <div v-if="sectionMap.has(id)"
                    :data-id="id"
                    :class="[panelLayouts.get(id)?.colSpan, { 'self-start': minimized.includes(id) }]"
                    class="panel-item border border-base-300 rounded-xl overflow-hidden bg-base-100">
                    <div class="panel-header bg-base-200/80 px-4 py-2 flex items-center gap-2 border-b border-base-300 cursor-grab active:cursor-grabbing"
                        :class="{ 'border-b-0': minimized.includes(id) }">
                        <Icon :icon="sectionMap.get(id).titleIcon" class="text-base shrink-0" :class="sectionMap.get(id).titleColor" />
                        <span class="text-sm font-semibold text-base-content/60 uppercase tracking-wider truncate">{{ sectionMap.get(id).title }}</span>
                        <button @click="toggleMinimized(id)"
                            class="ml-auto shrink-0 w-5 h-5 flex items-center justify-center rounded hover:bg-base-300/50 text-base-content/40 hover:text-base-content transition-colors cursor-pointer"
                            :title="minimized.includes(id) ? 'Expandir' : 'Minimizar'">
                            <Icon :icon="minimized.includes(id) ? 'ph:plus' : 'ph:minus'" class="text-xs" />
                        </button>
                    </div>
                    <div v-show="!minimized.includes(id)" class="p-2 grid gap-1"
                        :class="panelLayouts.get(id)?.innerCols">
                        <component
                            v-for="item in sectionMap.get(id).items.filter(i => i.show !== false)"
                            :key="item.label"
                            :is="(item.external || item.native) ? 'a' : Link"
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
            </template>
        </div>

        <div v-if="visibleCount === 0" class="text-center py-12 text-base-content/40">
            <Icon icon="ph:eye-slash-duotone" class="text-5xl mx-auto mb-2" />
            <p class="text-sm">No hay paneles visibles</p>
        </div>

        <div v-if="minimized.length" class="fixed bottom-0 left-0 right-0 z-50 bg-base-200/90 backdrop-blur-sm border-t border-base-300">
            <div class="flex gap-1.5 overflow-x-auto px-3 py-2 justify-center">
                <button v-for="id in minimized" :key="id"
                    @click="toggleMinimized(id)"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-md bg-base-100 hover:bg-primary/10 border border-base-300 text-xs transition-colors shrink-0 cursor-pointer">
                    <Icon v-if="sectionMap.has(id)" :icon="sectionMap.get(id).titleIcon" class="text-xs" />
                    <span class="truncate max-w-32">{{ sectionMap.get(id)?.title }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import { useStorage } from '@vueuse/core'
import { useSortable } from '@vueuse/integrations/useSortable'
import useUserStore from '@/Stores/user'

const page = usePage()
const userStore = useUserStore()

const props = defineProps({
    misEquipos: { type: Number, default: 0 },
    esMuul: { type: Boolean, default: false },
    esIniciado: { type: Boolean, default: false },
    inscripcionesPendientes: { type: Number, default: 0 }
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
                { label: 'Mi Perfil',      icon: 'ph:user-circle-duotone',       color: 'primary',   to: route('usuario', uid) },
                { label: 'Mis Archivos',   icon: 'ph:folder-duotone',             color: 'accent',    to: route('mis_archivos') },
                { label: 'Mis Equipos',    icon: 'ph:users-three-duotone',        color: 'secondary', to: '/equipos?categoria=Mis equipos' },
                { label: 'Mi Cuenta',      icon: 'ph:gear-six-duotone',           color: 'primary',   to: route('profile.show') },
            ]
        },
        {
            id: 'muular',
            title: 'muular electrónico',
            titleIcon: 'ph:coin-duotone',
            titleColor: 'text-secondary',
            items: [
                { label: 'Muulares',     icon: 'ph:currency-circle-dollar-duotone', color: 'secondary', to: '/muular-electronico', badge: userStore.saldo ?? '0', badgeClasses: 'text-info-content bg-info rounded-md p-1', show: hasMuular, external: true },
                { label: 'Solicitar',  icon: 'ph:currency-circle-dollar-duotone', color: 'warning',   to: '/contactar',                                   show: !hasMuular },
                { label: '¿Qué es?',   icon: 'ph:info-duotone',                   color: 'primary',   to: '/muular' },
            ]
        },
        {
            id: 'iniciados',
            title: 'iniciados (interiorización)',
            titleIcon: 'solar:meditation-round-bold-duotone',
            titleColor: 'text-accent',
            show: props.esIniciado,
            items: [
                { label: 'Interiorización', icon: 'solar:meditation-round-bold-duotone', color: 'accent',  to: route('equipo', 'iniciados-interiorizacion') },
                { label: 'Carpeta de iniciados',        icon: 'ph:folder-duotone',  color: 'primary', to: '/archivos/equipos/interiorizacion' },
            ]
        },
        {
            id: 'trabajos',
            title: 'trabajos de la comunidad',
            titleIcon: 'ph:users-four-duotone',
            titleColor: 'text-accent',
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
            items: [
                { label: 'Tseyor Canva', icon: 'ph:palette-duotone',     color: 'accent',  to: '/tseyor-canva', external: true },
                { label: 'Puzle', icon: 'ph:puzzle-piece-duotone', color: 'primary', to: 'https://puzle.tseyor.org/', external: true },
            ]
        },
        {
            id: 'comunidad',
            title: 'comunidad',
            titleIcon: 'ph:user-list-duotone',
            titleColor: 'text-primary',
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
            show: userStore.permisos.length > 0 || props.inscripcionesPendientes > 0,
            items: [
                { label: 'Panel Admin', icon: 'ph:shield-duotone', color: 'error', to: '/admin/dashboard', native: true, show: userStore.permisos.length > 0 },
                { label: 'Inscripciones', icon: 'ph:clipboard-text-duotone', color: 'warning', to: route('inscripciones.mis-asignaciones'), badge: props.inscripcionesPendientes > 0 ? String(props.inscripcionesPendientes) : '', badgeClasses: 'bg-warning/20 text-warning text-xs font-bold px-1.5 py-0.5 rounded', show: props.inscripcionesPendientes > 0 },
            ]
        },
    ]

    return all.filter(s => s.show !== false)
})

const panelContainer = ref(null)
const minimized = useStorage('miembros-minimized', [])

function loadPanelOrder() {
    try {
        const raw = localStorage.getItem('miembros-panel-order')
        return raw ? JSON.parse(raw) : null
    } catch { return null }
}

function savePanelOrder(ids) {
    try {
        localStorage.setItem('miembros-panel-order', JSON.stringify(ids))
    } catch {}
}

const panelOrder = ref(loadPanelOrder() || sections.value.map(s => s.id))

const sectionMap = computed(() => {
    const map = new Map()
    for (const s of sections.value) {
        map.set(s.id, s)
    }
    return map
})

watch(() => sections.value.map(s => s.id).join(','), () => {
    const ids = sections.value.map(s => s.id)
    if (!ids.length) return

    const valid = panelOrder.value.filter(id => ids.includes(id))
    for (const id of ids) {
        if (!valid.includes(id)) valid.push(id)
    }
    if (valid.join(',') !== panelOrder.value.join(',')) {
        panelOrder.value = valid
        savePanelOrder(valid)
    }
})

watch(panelOrder, (val) => {
    savePanelOrder(val)
}, { deep: true })

const panelLayouts = computed(() => {
    const map = new Map()
    for (const s of sections.value) {
        const count = s.items.filter(i => i.show !== false).length
        const colSpan = count >= 3 ? 'sm:col-span-2 md:col-span-4'
            : 'sm:col-span-1 md:col-span-2'
        const innerCols = count >= 3 ? 'grid-cols-2 sm:grid-cols-4'
            : count === 2 ? 'grid-cols-2'
            : 'grid-cols-1'
        map.set(s.id, { colSpan, innerCols })
    }
    return map
})

useSortable(panelContainer, panelOrder, {
    handle: '.panel-header',
    animation: 150,
    ghostClass: 'sortable-ghost',
    dragClass: 'sortable-drag',
    delay: 100,
    delayOnTouchOnly: true,
    touchStartThreshold: 5,
    onEnd: () => {
        panelOrder.value = [...panelOrder.value]
    },
})

function toggleMinimized(id) {
    const idx = minimized.value.indexOf(id)
    if (idx >= 0) {
        minimized.value = minimized.value.filter(x => x !== id)
    } else {
        minimized.value = [...minimized.value, id]
    }
}

const visibleCount = computed(() => panelOrder.value.filter(id => !minimized.value.includes(id)).length)
</script>

<style scoped>
@reference "../../css/app.css";

.sortable-ghost {
    @apply opacity-30;
}
.sortable-drag {
    @apply opacity-90 shadow-2xl scale-[1.02];
}
</style>
