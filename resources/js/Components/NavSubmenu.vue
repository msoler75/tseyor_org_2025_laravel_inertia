<template>
    <div v-show="nav.activeTab">
        <div v-if="nav.activeTab && nav.activeTab.hasItems"
            class="w-full flex flex-col z-40 top-8 bg-base-100 shadow-lg rounded-md border-gray-100 border">
            <div class="flex justify-between gap-10 p-12">
                <div v-for="section, index of nav.activeTab.submenu?.sections" :key="index" class="flex-1">
                    <NavSubmenuGroup v-for="group, index of section.groups" :title="group.title" :items="group.items"
                    :classItems="group.class"
                    :key="index"/>
                </div>
            </div>
            <div v-if="nav.activeTab.submenu?.footer" v-html="nav.activeTab.submenu?.footer" class="p-5 bg-base-100" />
        </div>
    </div>
</template>


<script setup>
const nav = useNav()
</script>
