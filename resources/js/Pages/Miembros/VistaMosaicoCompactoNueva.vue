<template>
    <div class="relative">
        <!-- Top bar: edit mode actions -->
        <transition enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2">
            <div v-if="editing" class="sticky top-0 z-10 flex items-center justify-between gap-2 px-3 py-2 my-6 rounded-xl border border-base-300 bg-base-100 shadow-sm">
                <span class="text-sm font-semibold text-base-content/60 leading-tight">Ordenar los paneles</span>
                <div class="flex items-center gap-2 shrink-0">
                    <button @click="cancelEdit"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-base-300 bg-base-100 text-base-content/60 hover:bg-base-200/60 transition-colors cursor-pointer">
                        <Icon icon="ph:x-duotone" class="text-sm" />
                        Descartar
                    </button>
                    <button @click="saveEdit"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-success/40 bg-success/10 text-success hover:bg-success/20 transition-colors cursor-pointer">
                        <Icon icon="ph:check-circle-duotone" class="text-sm" />
                        Guardar
                    </button>
                </div>
            </div>
        </transition>

        <!-- Desktop: sidebar menu (md+) — drag and drop -->
        <div class="hidden md:flex gap-4">
            <aside ref="sidebarRef" class="shrink-0 w-52 sm:w-62 space-y-1">
                <div v-for="id in activeOrder" :key="id" :data-id="id">
                    <button @click="activa = id"
                        class="sidebar-item w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-left text-sm font-semibold transition-colors"
                        :class="[
                            activa === id
                                ? 'bg-primary/15 text-primary'
                                : 'text-base-content/50 hover:bg-base-200/60 hover:text-base-content/80',
                            editing ? 'cursor-grab active:cursor-grabbing' : 'cursor-pointer'
                        ]">
                        <Icon v-if="editing" icon="ph:dots-six-vertical-duotone" class="text-base-content/30 shrink-0 text-base" />
                        <Icon :icon="getSection(id)?.titleIcon" class="text-lg shrink-0" :class="activa === id ? getSection(id)?.titleColor : ''" />
                        <span class="truncate capitalize">{{ getSection(id)?.title }}</span>
                        <span v-if="getSection(id)?.badges?.length" class="flex gap-0.5 ml-auto shrink-0">
                            <span v-for="b in getSection(id).badges" :key="b.label" class="text-xs font-bold px-1.5 py-0.5 rounded"
                                :class="b.badgeClasses" :title="b.badgeTitle">{{ b.badge }}</span>
                        </span>
                    </button>
                </div>
            </aside>

            <div class="flex-1 min-w-0 border border-base-300 rounded-xl bg-base-100 overflow-hidden">
                <div v-if="activeSection" class="px-4 py-3 border-b border-base-300 bg-base-200/60">
                    <div class="flex items-center gap-2">
                        <Icon :icon="activeSection.titleIcon" class="text-lg shrink-0" :class="activeSection.titleColor" />
                        <h2 class="text-sm font-semibold uppercase tracking-wider text-base-content/60 my-0">{{ activeSection.title }}</h2>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3">
                    <component
                        v-for="item in activeSection?.items?.filter(i => i.show !== false) ?? []"
                        :key="item.label"
                        :is="(item.external || item.native) ? 'a' : Link"
                        :href="item.to"
                        :target="item.external ? '_blank' : undefined"
                        :rel="item.external ? 'noopener noreferrer' : undefined"
                        :title="item.tooltip"
                        class="group flex items-center gap-3 p-3 rounded-lg hover:bg-base-200/50 transition-colors duration-150">
                        <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center transition-colors duration-200"
                            :class="iconClasses(item.color)">
                            <Icon :icon="item.icon" class="text-xl" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-sm font-semibold text-base-content/80">{{ item.label }}</span>
                            <span v-if="item.badge" class="text-sm font-bold ml-1.5" :class="item.badgeClasses" :title="item.badgeTitle">{{ item.badge }}</span>
                        </div>
                    </component>
                </div>
            </div>
        </div>

        <!-- Mobile: mosaico compacto (<md) — arrow buttons -->
        <TransitionGroup name="panel-move" tag="div" class="md:hidden">
            <div v-for="(s, idx) in mobileSections" :key="s.id" class="mb-4">
                <div class="section-header flex items-center gap-2 mb-2">
                    <template v-if="editing">
                        <button @click="moveUp(s.id)" :disabled="idx === 0"
                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg border border-base-300 bg-base-100 transition-colors cursor-pointer"
                            :class="idx === 0 ? 'opacity-30 pointer-events-none' : 'hover:bg-base-200/60'">
                            <Icon icon="ph:caret-up-duotone" class="text-sm" />
                        </button>
                        <button @click="moveDown(s.id)" :disabled="idx === mobileSections.length - 1"
                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg border border-base-300 bg-base-100 transition-colors cursor-pointer"
                            :class="idx === mobileSections.length - 1 ? 'opacity-30 pointer-events-none' : 'hover:bg-base-200/60'">
                            <Icon icon="ph:caret-down-duotone" class="text-sm" />
                        </button>
                    </template>
                    <Icon :icon="s.titleIcon" class="text-sm shrink-0" :class="s.titleColor" />
                    <h2 class="text-xs font-semibold text-base-content/50 uppercase tracking-wider my-0">{{ s.title }}</h2>
                    <div class="flex-1 h-px bg-base-300/70 mx-1"></div>
                    <div v-if="s.badges.length" class="flex gap-1 shrink-0">
                        <span v-for="b in s.badges" :key="b.label" class="text-xs font-bold px-1.5 py-0.5 rounded"
                            :class="b.badgeClasses" :title="b.badgeTitle">{{ b.badge }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap justify-start gap-1 bg-base-100 rounded-xl p-1.5">
                    <component
                        v-for="item in s.items.filter(i => i.show !== false)"
                        :key="item.label"
                        :is="(item.external || item.native) ? 'a' : Link"
                        :href="item.to"
                        :target="item.external ? '_blank' : undefined"
                        :rel="item.external ? 'noopener noreferrer' : undefined"
                        :title="item.tooltip"
                        class="group flex flex-col items-center justify-center gap-0.5 p-1.5 rounded-lg hover:bg-base-200/40 transition-colors duration-150"
                        style="width: 80px;">
                        <div class="shrink-0 w-9 h-9 rounded-lg flex items-center justify-center transition-colors duration-200"
                            :class="iconClasses(item.color)">
                            <Icon :icon="item.icon" class="text-lg" />
                        </div>
                        <span class="text-[11px] font-semibold text-base-content/70 text-center leading-tight flex items-center justify-center gap-0.5">
                            {{ item.label }}
                            <span v-if="item.badge" class="text-[11px] font-bold shrink-0" :class="item.badgeClasses" :title="item.badgeTitle">{{ item.badge }}</span>
                        </span>
                    </component>
                </div>
            </div>
        </TransitionGroup>

        <!-- Edit mode toggle button -->
        <div class="flex justify-center mt-4">
            <button @click="enterEdit"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold border border-base-300 bg-base-100 text-base-content/40 hover:bg-base-200/60 hover:text-base-content/60 transition-colors cursor-pointer"
                :class="{ 'pointer-events-none opacity-0': editing }">
                <Icon icon="ph:gear-six-duotone" class="text-sm" />
                Editar orden
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useStorage } from '@vueuse/core'
import { useSortable } from '@vueuse/integrations/useSortable'

const props = defineProps({
    sections: { type: Array, required: true },
    iconClasses: { type: Function, required: true }
})

// --- Enriched sections with badges ---
const enrichedSections = computed(() =>
    props.sections.map(s => ({
        ...s,
        badges: s.items.filter(i => i.show !== false && i.badgeSummary)
            .map(i => ({ label: i.label, badge: i.badge, badgeClasses: i.badgeClasses, badgeTitle: i.badgeTitle })),
    }))
)

const sectionMap = computed(() => new Map(enrichedSections.value.map(s => [s.id, s])))
function getSection(id) { return sectionMap.value.get(id) }

// --- Persisted order (committed) ---
const committedOrder = useStorage('miembros-mosaico-nueva-order', props.sections.map(s => s.id))
watch(() => props.sections, (sections) => {
    const ids = sections.map(s => s.id)
    committedOrder.value = [...committedOrder.value.filter(id => ids.includes(id)), ...ids.filter(id => !committedOrder.value.includes(id))]
}, { immediate: true })

// --- Draft order (used while editing, or equal to committed when not) ---
const editing = ref(false)
const draftOrder = ref([...committedOrder.value])
const activeOrder = computed(() => editing.value ? draftOrder.value : committedOrder.value)

// Mobile: sections ordered
const mobileSections = computed(() =>
    activeOrder.value
        .map(id => sectionMap.value.get(id))
        .filter(Boolean)
)

// --- Desktop sidebar active section ---
const activa = ref(activeOrder.value[0])
const activeSection = computed(() => enrichedSections.value.find(s => s.id === activa.value))

// --- Arrow move (mobile) ---
function moveUp(id) {
    const arr = [...draftOrder.value]
    const idx = arr.indexOf(id)
    if (idx > 0) {
        [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]]
        draftOrder.value = arr
    }
}

function moveDown(id) {
    const arr = [...draftOrder.value]
    const idx = arr.indexOf(id)
    if (idx < arr.length - 1) {
        [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]]
        draftOrder.value = arr
    }
}

// --- Edit mode ---
function enterEdit() {
    draftOrder.value = [...committedOrder.value]
    editing.value = true
    nextTick(() => setupSortables())
}

function saveEdit() {
    committedOrder.value = [...draftOrder.value]
    editing.value = false
}

function cancelEdit() {
    draftOrder.value = [...committedOrder.value]
    editing.value = false
}

// --- Drag and drop (desktop sidebar only) ---
const sidebarRef = ref(null)
let sidebarInstance = null

function destroySortables() {
    sidebarInstance?.destroy()
    sidebarInstance = null
}

function setupSortables() {
    destroySortables()
    if (!editing.value) return

    if (sidebarRef.value) {
        sidebarInstance = useSortable(sidebarRef, draftOrder, {
            handle: '.sidebar-item',
            animation: 150,
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            group: 'sections',
            delay: 100,
            delayOnTouchOnly: true,
            touchStartThreshold: 5,
            onEnd: () => { draftOrder.value = [...draftOrder.value] },
        })
    }
}

// Watch editing to clean up sortables
watch(editing, (val) => {
    if (!val) destroySortables()
})
</script>

<style scoped>
@reference "../../../css/app.css";

.sortable-ghost {
    @apply opacity-30;
}
.sortable-drag {
    @apply opacity-90 shadow-2xl scale-[1.02];
}

/* Panel reorder animation */
.panel-move-enter-active,
.panel-move-leave-active {
    transition: all 0.3s ease;
}
.panel-move-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
.panel-move-leave-to {
    opacity: 0;
    transform: translateY(10px);
}
.panel-move-move {
    transition: transform 0.3s ease;
}
</style>
