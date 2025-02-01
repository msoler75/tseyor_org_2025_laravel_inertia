<template>
    <div v-show="nav.activeTab">
        <div v-if="nav.ghostTab && nav.ghostTab.hasItems"
            class="w-full h-30 flex flex-col z-40 top-8 bg-base-100 shadow-lg rounded-md border-gray-100 border">
            <div class="flex justify-between gap-10 p-12">
                <div v-for="section, index of nav.ghostTab.submenu?.sections" :key="index" class="flex-1">
                    <NavSubmenuGroup v-for="group, index of section.groups" :title="group.title" :items="group.items"
                    :classItems="group.class"
                    :key="index"/>
                </div>
            </div>
            <div v-if="nav.ghostTab.submenu?.footer" v-html="nav.ghostTab.submenu?.footer" class="p-5 bg-base-100" />
        </div>
    </div>
</template>


<script setup>
const nav = useNav()
</script>
