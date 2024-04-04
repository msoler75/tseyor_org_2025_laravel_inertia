<template>
    <div v-show="nav.activeTab">
        <div v-if="nav.ghostTab && nav.ghostTab.hasItems"
            class="w-full h-30 flex flex-col z-40 top-8 bg-base-100 shadow-lg rounded-md border-gray-100 border">
            <div class="flex justify-between gap-10 p-12">
                <div v-for="section, index of nav.ghostTab.submenu?.sections" :key="index" class="flex-1">
                    <div class="text-gray-500 my-5 uppercase tracking-widest text-xs">{{
                        section.title }}
                    </div>
                    <div  :class="section.class?section.class:'flex flex-col gap-7 mb-7'">
                        <component :is="item.external?'a':Link" :target="item.external ? '_blank' : ''" :href="item.url"
                            v-for="item of section.items" :key="item.url" @click="nav.closeTabs()"
                            class="group flex gap-3 p-3 rounded-lg hover:bg-base-200 transition duration-100 cursor-pointer relative"
                            :class="item.class">
                            <div class="flex justify-start" style="min-width:2.2rem">
                                <Icon :icon="item.icon" class="text-3xl text-primary flex-shrink-0" />
                            </div>
                            <div class="flex flex-col self-center w-full">
                                <strong class="item-lg group-hover:text-primary flex items-center justify-between w-full">{{ item.title }} <Icon icon="ph:arrow-right" class="opacity-0 group-hover:opacity-100"/></strong>
                                <span v-if="item.description" class="text-gray-500 text-sm">{{ item.description }}</span>
                            </div>
                        </component>
                    </div>
                </div>
            </div>
            <div v-if="nav.ghostTab.submenu?.footer" v-html="nav.ghostTab.submenu?.footer" class="p-5 bg-base-100" />
        </div>
    </div>
</template>


<script setup>
import { Link } from '@inertiajs/vue3';

const nav = useNav()
</script>
