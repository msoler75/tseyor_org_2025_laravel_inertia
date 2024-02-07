<template>
    <div>
        <template v-for="tab of nav.items" :key="tab.url">
            <NavLink v-if="tab.url" :href="tab.url" @mouseover="handleHover(tab)"
            class="relative"
                :active="tab.open || (!nav.activeTab && nav.in(tab, $page.url))">
                {{ tab.title }}
                <div v-if="tab.hasItems" v-show="tab.open" class="hover-helper absolute z-40 -left-[7rem] -right-[7rem] top-[88%]  h-8" />
            </NavLink>
            <NavLink v-else @click="nav.toggleTab(tab)" @mouseover="handleHover(tab)"
                :active="tab.open || (!nav.activeTab && nav.in(tab, $page.url))" class="relative navigation-tab">
                {{ tab.title }}
                <div v-show="tab.open" class="hover-helper absolute z-40 -left-[7rem] -right-[7rem] top-[88%]  h-8" />
            </NavLink>
        </template>
    </div>
</template>


<script setup>
import { useNav } from '@/Stores/nav'
const nav = useNav()

function handleHover(tab) {
    if(tab.hasItems)
        nav.activateTab(tab)
    else
        nav.closeTabs()
}
</script>


<style scoped>

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
.navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='black' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}

.dark .navigation-tab {
    cursor: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' width='24' height='24'><path fill='white' d='M12 20.5l-9-9v-2l9 9 9-9v2z'></path></svg>") 16 16, auto;
}
</style>
