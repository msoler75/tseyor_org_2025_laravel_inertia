<template>
    <div v-show="nav.activeTab" data-nav-submenu>
        <div v-if="nav.activeTab && nav.activeTab.hasItems"
            class="w-full flex flex-col z-40 top-8 bg-base-100 shadow-lg rounded-md border-gray-100 border relative">
            <div class="cursor-pointer opacity-75 hover:opacity-100 hover:text-secondary text-xl p-2 absolute right-2 top-2" @click="nav.closeTabs()" title="Cerrar menú" aria-label="Cerrar menú">✕</div>
            <div class="flex justify-between gap-10 p-12" data-nav-columns-container>
                <div v-for="section, index of nav.activeTab.submenu?.sections" :key="index" class="flex-1" :data-nav-column="index">
                    <NavSubmenuGroup v-for="group, index of section.groups" :title="group.title" :items="group.items"
                    :classItems="group.class"
                    :key="index"/>
                </div>
            </div>
            <div v-if="nav.activeTab.submenu?.footer" class="p-5 bg-base-100">{{ nav.activeTab.submenu?.footer }}</div>
        </div>
    </div>
</template>


<script setup>
const nav = useNav()
</script>
