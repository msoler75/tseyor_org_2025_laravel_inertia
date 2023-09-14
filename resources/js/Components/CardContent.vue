<template>
    <component :is="href?Link:'div'" :href="href" class="card bg-base-100 shadow overflow-hidden group
                transition duration-300 hover:shadow-lg
                outline-gray-300 dark:outline-transparent outline-[0.4px] hover:outline
               relative" :class="imageLeft ? 'flex-row' : ''">
        <div class="flex-shrink-0 overflow-hidden" :class="(imageLeft ? 'w-1/3 h-full ' : 'h-40 ') + imageClass">
            <div class="w-full h-full bg-cover bg-center transition duration-300 group-hover:scale-110" :style="{
                'background-image': 'url(\''+getImageUrl(image)+'\')'
            }" />
        </div>
        <div v-if="title || tag || description || date" class="p-4 flex flex-col w-full">
            <h2 v-if="title" class="text-lg font-bold mb-2 transition duration-300 group-hover:text-primary  group-hover:drop-shadow" v-html="title"/>
            <div v-if="tag" class="flex justify-between">
                <div class="badge badge-primary badge-outline">{{ tag }}</div>
            </div>
            <div v-if="description" class="lg:opacity-50 transition duration-300 group-hover:opacity-90 text-sm" v-html="description"/>
            <TimeAgo v-if="date" :date="date" class="text-right mt-auto opacity-50" style="font-size: 60%" />
        </div>
        <slot />
    </component>
</template>

<script setup>
import { getImageUrl } from '@/composables/utils.js'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    imageLeft: {
        type: Boolean,
        default: false
    },
    title: String,
    href: String,
    image: String,
    tag: String,
    tagLink: String,
    description: String,
    date: String,
    imageClass: {
        type: String,
        default: ''
    }
})
</script>
