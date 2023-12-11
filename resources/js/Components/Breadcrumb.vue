<script setup>
// import { Link } from '@inertiajs/vue3';
import ConditionalLink from './ConditionalLink.vue'

const emit = defineEmits(['folder:value'])

const props = defineProps({
    path: String,
    links: { type: Boolean, default: true }
})

const items = computed(() => {
    const r = []
    const parts = props.path.split('/').filter(x => !!x)
    let url = ''
    for (var part of parts) {
        url += '/' + part
        r.push({ label: part, url: url })
    }
    return r
})

function handleClick(item, event) {
    console.log('breadcrumb.handleClick', item, event)
    if(!props.links) {
        console.log('emit')
        emit('folder', item)
        event.preventDefault()
    }
}
</script>


<template>
    <ol v-if="items.length" class="list-reset flex gap-1">
        <template v-for="item, index, of items" :key="index">
            <li v-if="index > 0">
                <span class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
            </li>
            <li class="flex items-center space-x-3">
                <component :is="links && index<items.length-1?ConditionalLink: 'div' " :href="item.url" 
                    @click="handleClick(item, $event)"
                    :class="!links ? 'pointer-events-none' :index < items.length - 1 ? 'text-gray-700 dark:text-gray-300 hover:underline' : 'text-gray-400 dark:text-gray-600'"
                    :is-link="true"
                    >
                    {{ item.label }}</component>
            </li>
        </template>
    </ol>
    <ol v-else>
        <li class="py-1">
                <span class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
            </li>
    </ol>
</template>
