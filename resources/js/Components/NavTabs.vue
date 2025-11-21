<template>
    <div class="relative bg-base-100/0" ref="container"
    :data-theme="portada && !nav.navigating && nav.scrollY < PORTADA_SCROLL_THRESHOLD ? 'night' : ''"
    >
        <template v-for="tab, index of nav.items" :key="tab.url">
            <NavLink v-if="!tab.onlyAside" class="nav-tab relative px-4"
            :class="[
                tab.hasItems && !tab.url ? 'navigation-tab' : '',
                tab == nav.tabHovering ? 'hovering' : '',
                tab.current ? 'current-tab' : ''
                ]"
            :href="tab.url"
                @click="clickedTab(tab)" @mouseover="enterTab(tab)" @mouseout="leaveTab(tab)"
                :active="tab.open || tab.current">
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
    // Si el tab tiene URL (va a navegar), cerramos submenu solo si hay alguno abierto
    if(tab.url && tab.url !== 'undefined') {
        console.log('router: NavTabs: clickedTab', { tabUrl: tab.url, tabTitle: tab.title, time: Date.now() })
        if(nav.activeTab) {
            nav.closeTabs()
        }
    } else {
        // Si no tiene URL, alternamos el estado del submenu
        nav.toggleTab(tab);
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


onMounted(  ()=>{
    updateUnderscore()
    // useEventListener(vImagesWrap.value, 'mousewheel', handleScroll, false)
    // usar eventlistener para cuando window haga resize
    useEventListener(window, 'resize', updateUnderscore, false)

    // get global event from window
    window.addEventListener("page-loaded", updateUnderscore)


    window.addEventListener('keydown', function (event) {
        console.log('keydown', event.key, nav.activeTab)
        if (nav.activeTab && event.key == 'Escape') {
            console.log('closing')
            nav.closeTabs()
        }
    })
})

onBeforeUnmount(() => {
    // Limpiar el timeout al desmontar el componente
    if (hoverTimeout.value) {
        clearTimeout(hoverTimeout.value)
        hoverTimeout.value = null
    }
})
</script>


<style scoped>
/* Constantes CSS como variables CSS */
:root {
    --font-weight-bold: 700;
    --cursor-size: 16;
    --translate-offset-1: 4rem;
    --translate-offset-2: 1rem;
    --translate-offset-3: -1rem;
    --translate-offset-4: -4rem;
}

.current-tab:focus,
.current-tab {
    font-weight: var(--font-weight-bold);
    color: var(--color-primary);
}


/*
.hover-helper {
    @apply bg-yellow-300;
}
*/

.top-navigation>.navigation-tab:nth-child(2)>.hover-helper {
    transform: translateX(var(--translate-offset-1));
}

.top-navigation>.navigation-tab:nth-child(3)>.hover-helper {
    transform: translateX(var(--translate-offset-2));
}

.top-navigation>.navigation-tab:nth-child(5)>.hover-helper {
    transform: translateX(var(--translate-offset-3));
}

.top-navigation>.navigation-tab:nth-child(6)>.hover-helper {
    transform: translateX(var(--translate-offset-4));
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
