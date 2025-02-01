<template>
    <div>
        <div class="text-gray-500 my-5 uppercase tracking-widest text-xs">{{
            title }}
        </div>
        <div  :class="classItems?classItems:'flex flex-col gap-7 mb-7'">
            <component :is="item.disabled?'div':(item.external?'a':Link)" :target="item.target" :href="item.url"
            v-for="item of items" :key="item.url" @click="nav.closeTabs()"
            class="group flex gap-3 p-3 rounded-lg hover:bg-base-200 transition duration-100 cursor-pointer relative"
            :class="item.class+(item.disabled?' pointer-events-none':'')">
                <div class="flex justify-start" style="min-width:2.2rem">
                    <Icon v-if="item.icon":icon="item.icon" class="text-3xl text-primary group-hover:text-secondary flex-shrink-0" :class="item.disabled?'!text-gray-500':''"/>
                    <component v-else-if="item.component" :is="item.component" class="w-6 h-6 flex-shrink-0"
                    :class="item.disabled?'!opacity-50':''"/>
                </div>
                <div class="flex flex-col self-center w-full">
                    <span class="font-semibold item-lg text-primary group-hover:text-secondary flex items-center justify-between w-full"
                    :class="item.disabled?'!text-gray-500':''">{{ item.title }} <Icon icon="ph:arrow-right" class="opacity-0 group-hover:opacity-100"/></span>
                    <span v-if="item.description" class="text-gray-500 text-sm">{{ item.description }}</span>
                </div>
            </component>
        </div>
    </div>
</template>


<script setup>
import { Link } from '@inertiajs/vue3';

const nav = useNav()

const props = defineProps({
    title: String,
    items: Array,
    classItems: String
})
</script>
