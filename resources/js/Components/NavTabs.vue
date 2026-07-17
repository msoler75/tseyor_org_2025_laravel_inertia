<template>
    <div class="relative bg-base-100/0" ref="container"
    >
        <template v-for="tab, index of nav.items" :key="tab.url">
            <NavLink v-if="!tab.onlyAside" class="nav-tab relative px-4"
            :class="[
                tab.hasItems ? 'navigation-tab' : '',
                tab == nav.tabHovering ? 'hovering' : '',
                tab.current ? 'current-tab' : ''
                ]"
            :href="tab.hasItems ? undefined : tab.url"
                @click="clickedTab(tab)" @mouseover="enterTab(tab)" @mouseout="leaveTab(tab)"
                :active="tab.open || tab.current" :data-nav-tab="index">
                {{ tab.title }}
                <div v-if="tab.open"
                    class="hover-helper absolute z-40 -left-28 -right-28 top-full h-8 transform"
                    :style="{transform: `translateX(${(5-index)*20}px)`}"/>
            </NavLink>
        </template>
        <div class="underscore transition-all duration-300 absolute h-1 left-0 bottom-0 pointer-events-none bg-primary"
        :style="underScoreStyle"/>
    </div>
</template>


<script setup>
import { useEventListener } from '@vueuse/core'

// Constantes para evitar números mágicos
const PORTADA_SCROLL_THRESHOLD = 300
const HOVER_DELAY_MS = 120

const nav = useNav()
const container = ref(null)
const page = usePage();
const portada = computed(() => page.url == "/");
const hoverTimeout = ref(null)

function clickedTab(tab) {
    if(tab.hasItems) {
        nav.toggleTab(tab)
        return
    }
    if(tab.url && tab.url !== 'undefined') {
        if(nav.activeTab) nav.closeTabs()
    } else {
        nav.toggleTab(tab)
    }
}
function enterTab(tab) {
    // Cancelar cualquier timeout previo
    if (hoverTimeout.value) {
        clearTimeout(hoverTimeout.value)
        hoverTimeout.value = null
    }

    // Activar el hover con un retardo
    hoverTimeout.value = setTimeout(() => {
        nav.hoverTab(tab)
        hoverTimeout.value = null
        nextTick(updateUnderscore)
    }, HOVER_DELAY_MS)

}

function leaveTab(tab) {
    // Cancelar el timeout si el mouse sale antes de que se active
    if (hoverTimeout.value) {
        clearTimeout(hoverTimeout.value)
        hoverTimeout.value = null
    }

    nav.unhoverTab(tab)
    nextTick(updateUnderscore)
}

const underscoreShow = ref(false)
const underscoreLeft = ref(0)
const underscoreWidth = ref(0)

function updateUnderscore() {
    // Verificar que el componente esté montado
    if (!container.value) {
        return
    }

    // Prioridad: 1. Tab con hover, 2. Tab actual según URL
    const elem = document.querySelector('.nav-tab.hovering') ||
                 document.querySelector('.nav-tab.current-tab')

    if(!elem || !elem.getBoundingClientRect) {
        underscoreShow.value = false
        return
    }

    try {
        const containerRect = container.value.getBoundingClientRect()
        const tabRect = elem.getBoundingClientRect();
        underscoreShow.value = true
        underscoreLeft.value = `${tabRect.left - containerRect.left}px`
        underscoreWidth.value = `${tabRect.width}px`
    } catch (error) {
        // Si hay error durante la medición, ocultar underscore
        underscoreShow.value = false
    }
}

watch(()=>nav.activeTab, (tab) => {
    // Recalcular posición del underscore cuando cambia el activeTab
    nextTick(updateUnderscore)
})

const underScoreStyle = computed(() => {
    return {
        transform: `translateX(${underscoreLeft.value})`,
        width: underscoreWidth.value,
        opacity: underscoreShow.value ? '1' : '0'
    }
})


watch(()=>nav.tabHovering, (tab)=> {
    updateUnderscore()
})

// Recalcular underscore cuando cambia la URL (navegación)
watch(() => page.url, () => {
    nextTick(updateUnderscore)
})


let _handleKeydown = null

onMounted(() => {
    updateUnderscore()
    useEventListener(window, 'resize', updateUnderscore, false)

    window.addEventListener("page-loaded", updateUnderscore)

    let _lastKeyTime = 0
    let _lastKey = ''
    let _keyRepeat = 0
    let _focusTimeout = null
    let _currentCol = 0

    function getColumns() {
        const el = document.querySelector('[data-nav-submenu]')
        if (!el) return []
        const inner = el.querySelector(':scope > div div[data-nav-column]')
        if (!inner) return []
        const container = inner.closest('[data-nav-columns-container]')
        if (!container) return []
        return Array.from(container.querySelectorAll(':scope > [data-nav-column]'))
    }

    function getColumnItems(colIdx) {
        const cols = getColumns()
        if (!cols.length) return []
        const target = typeof colIdx === 'number' ? cols[Math.min(colIdx, cols.length - 1)] : cols[_currentCol]
        if (!target) return []
        return Array.from(target.querySelectorAll('a, button'))
    }

    function _focusFirstSubmenuItem(colIdx = 0) {
        const items = getColumnItems(colIdx)
        if (items.length > 0) {
            items[0]?.focus()
            return
        }
        nextTick(() => {
            requestAnimationFrame(() => {
                const items = getColumnItems(colIdx)
                if (items.length > 0) items[0]?.focus()
            })
        })
    }

    _handleKeydown = (event) => {
        if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA' || event.target.isContentEditable) return
        if (event.ctrlKey || event.metaKey || event.altKey) return

        if (event.key === 'Escape') {
            if (nav.activeTab) {
                nav.closeTabs()
                const activeIdx = nav.items.indexOf(nav.activeTab)
                const tabEl = document.querySelector(`[data-nav-tab="${activeIdx}"]`)
                if (tabEl) tabEl.focus()
            }
            return
        }

        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            const colEl = document.activeElement?.closest('[data-nav-column]')
            if (colEl) {
                const cols = getColumns()
                const colIdx = cols.indexOf(colEl)
                if (colIdx !== -1) _currentCol = colIdx
            }

            const items = getColumnItems()

            if (items.length > 0) {
                event.preventDefault()
                const current = document.activeElement
                const idx = items.indexOf(current)

                if (idx === -1) {
                    items[0]?.focus()
                    return
                }

                if (idx === 0 && event.key === 'ArrowUp') {
                    const activeIdx = nav.items.indexOf(nav.activeTab)
                    const tabEl = document.querySelector(`[data-nav-tab="${activeIdx}"]`)
                    if (tabEl) tabEl.focus()
                    nav.closeTabs()
                    return
                }

                if (event.key === 'ArrowDown') {
                    items[(idx + 1) % items.length]?.focus()
                } else {
                    items[(idx - 1 + items.length) % items.length]?.focus()
                }
                return
            }

            if (event.key === 'ArrowDown') {
                const navTabEl = event.target.closest('.nav-tab')
                if (navTabEl) {
                    const tabIdx = parseInt(navTabEl.dataset.navTab)
                    const tab = nav.items[tabIdx]
                    if (tab?.hasItems) {
                        event.preventDefault()
                        nav._setKeyboardTime()
                        nav.closeTabs()
                        nav.activateTab(tab)
                        _currentCol = 0
                        _focusFirstSubmenuItem(0)
                    }
                }
            }
            return
        }

        if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
            const navTabEl = document.activeElement?.closest('.nav-tab')
            if (navTabEl) {
                event.preventDefault()
                const tabIdx = parseInt(navTabEl.dataset.navTab)
                const visibleTabs = nav.items.filter(t => !t.onlyAside)
                const visibleIdx = visibleTabs.indexOf(nav.items[tabIdx])
                if (visibleIdx !== -1) {
                    const targetVisibleIdx = event.key === 'ArrowRight'
                        ? (visibleIdx + 1) % visibleTabs.length
                        : (visibleIdx - 1 + visibleTabs.length) % visibleTabs.length
                    const targetTab = visibleTabs[targetVisibleIdx]
                    const targetIdx = nav.items.indexOf(targetTab)
                    const targetEl = document.querySelector(`[data-nav-tab="${targetIdx}"]`)
                    if (targetEl) {
                        nav.closeTabs()
                        targetEl.focus()
                    }
                }
                return
            }

            const cols = getColumns()
            if (cols.length <= 1) return
            event.preventDefault()

            const colEl = document.activeElement?.closest('[data-nav-column]')
            if (colEl) {
                const colIdx = cols.indexOf(colEl)
                if (colIdx !== -1) _currentCol = colIdx
            }

            const current = document.activeElement
            const currentItems = getColumnItems(_currentCol)
            const currentRow = currentItems.indexOf(current)

            const targetCol = event.key === 'ArrowRight'
                ? Math.min(_currentCol + 1, cols.length - 1)
                : Math.max(_currentCol - 1, 0)

            if (targetCol !== _currentCol) {
                _currentCol = targetCol
                const targetItems = getColumnItems(_currentCol)
                if (targetItems.length > 0) {
                    const targetIdx = Math.min(
                        currentRow >= 0 ? currentRow : 0,
                        targetItems.length - 1
                    )
                    targetItems[targetIdx]?.focus()
                }
            }
            return
        }

        if (event.key.length !== 1) return

        const now = Date.now()
        const key = event.key.toLowerCase()

        if (key === _lastKey && now - _lastKeyTime < 800) {
            _keyRepeat++
        } else {
            _keyRepeat = 0
        }
        _lastKeyTime = now
        _lastKey = key

        const match = nav.items.filter(t => !t.onlyAside && t.title.toLowerCase().startsWith(key))
        if (match.length > 0) {
            event.preventDefault()
            clearTimeout(_focusTimeout)
            const tab = match[_keyRepeat % match.length]
            nav._setKeyboardTime()
            nav.closeTabs()
            nav.activateTab(tab)
            _currentCol = 0
            _focusFirstSubmenuItem(0)
        }
    }

    window.addEventListener('keydown', _handleKeydown)
})

onBeforeUnmount(() => {
    if (hoverTimeout.value) {
        clearTimeout(hoverTimeout.value)
        hoverTimeout.value = null
    }
    if (_handleKeydown) {
        window.removeEventListener('keydown', _handleKeydown)
    }
    window.removeEventListener("page-loaded", updateUnderscore)
})
</script>


<style scoped>
:root {
    --font-weight-bold: 700;
    --cursor-size: 16;
}

.current-tab:focus,
.current-tab {
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}



</style>

<style>
.nav-tab.active {
    font-style: bold;
}

.navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='orange' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") var(--cursor-size) var(--cursor-size), auto;
}

/*[data-theme="night"] .navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='orange' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}*/
</style>
