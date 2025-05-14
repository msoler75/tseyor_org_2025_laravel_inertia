<template>
    <div class="relative" ref="container">
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
                    class="hover-helper absolute z-40 -left-[7rem] -right-[7rem] top-[100%] h-8 transform"
                     :style="{transform: `translateX(${(5-index)*20}px)`}"/>
            </NavLink>
        </template>
        <div class="underscore transition-all duration-300 absolute h-1 left-0 bottom-0 pointer-events-none bg-primary"
        :style="underScoreStyle"/>
    </div>
</template>


<script setup>
import { useEventListener } from '@vueuse/core'

const nav = useNav()
const container = ref(null)

function clickedTab(tab) {
    nav.toggleTab(tab);
    //console.log(tab.url, typeof tab.url)
    //if(tab.url&&tab.url!='undefined')
      //  nav.closeTabs()
}


function enterTab(tab) {
    nav.hoverTab(tab)
    nextTick(updateUnderscore)
}

function leaveTab(tab) {
    nav.unhoverTab(tab)
    nextTick(updateUnderscore)
}

const underscoreShow = ref(false)
const underscoreLeft = ref(0)
const underscoreWidth = ref(0)

function updateUnderscore() {
    const elem = document.querySelector('.nav-tab.hovering')
    if(!elem) {
        if(!nav.activeTab)
            underscoreShow.value = false
        return
    }
    const containerRect = container.value.getBoundingClientRect()
    const tabRect = elem.getBoundingClientRect();
    underscoreShow.value = true
    underscoreLeft.value = `${tabRect.left - containerRect.left}px`
    underscoreWidth.value = `${tabRect.width}px`
}

watch(()=>nav.activeTab, (tab) => {
    if(!tab)
        underscoreShow.value = false
})

const underScoreStyle = computed(() => {
    return {
        transform: `translateX(${underscoreLeft.value})`,
        width: underscoreWidth.value,
        opacity: underscoreShow.value ? '1' : '0'
    }
})


watch(()=>nav.tabHovering, (tab)=> {
    //nextTick( ()=> {
       // nextTick( ()=> {
            updateUnderscore ()
       // })
    //})
})


onMounted(  ()=>{
    updateUnderscore()
    // useEventListener(vImagesWrap.value, 'mousewheel', handleScroll, false)
    // usar eventlistener para cuando window haga resize
    useEventListener(window, 'resize', updateUnderscore, false)

    // get global event from window
    window.addEventListener("page-loaded", updateUnderscore)
})
</script>


<style scoped>

.current-tab:focus,
.current-tab {
    font-weight: var(--font-weight-bold, 700);
    color: var(--color-primary);
}


/*
.hover-helper {
    @apply bg-yellow-300;
}
*/

.top-navigation>.navigation-tab:nth-child(2)>.hover-helper {
    transform: translateX(4rem);
}

.top-navigation>.navigation-tab:nth-child(3)>.hover-helper {
    transform: translateX(1rem);
}

.top-navigation>.navigation-tab:nth-child(5)>.hover-helper {
    transform: translateX(-1rem);
}

.top-navigation>.navigation-tab:nth-child(6)>.hover-helper {
    transform: translateX(-4rem);
}
</style>

<style>
.nav-tab.active {
    font-style: bold;
}

.nav-tab.hovering {
    /* @apply bg-yellow-500; */
}

.navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='orange' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}

/*[data-theme="night"] .navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='orange' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}*/
</style>
