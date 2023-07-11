<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    path: String
})

const items = computed(() => {
    const r = []
    const parts = props.path.split('/')
    let url = ''
    for (var part of parts) {
        url += '/' + part
        r.push({ label: part, url: url })
    }
    return r
})
</script>


<template>
    <ol class="list-reset flex gap-1">
        <template v-for="item, index, of items" :key="index">
            <li v-if="index>0">
                <span class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
            </li>
            <li class="flex items-center space-x-3">
                <component :is="index<items.length-1?Link: 'div' " :href="item.url"
                    :class="index < items.length - 1 ? 'text-gray-800' : 'text-gray-400'">{{ item.label }}</component>
            </li>
        </template>
    </ol>
</template>
