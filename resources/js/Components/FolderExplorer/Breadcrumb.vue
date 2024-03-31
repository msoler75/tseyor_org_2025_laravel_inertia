<script setup>
import { Link } from '@inertiajs/vue3';
// import ConditionalLink from './ConditionalLink.vue'

const emit = defineEmits(['folder'])

const props = defineProps({
    path: String,
    links: { type: Boolean, default: true },
    interceptClick: { type: Boolean, default: false },
    rootLabel: { type: String, require: false },
    rootUrl: { type: String, require: false },
})

const items = computed(() => {
    const r = []
    if (props.rootLabel)
        r.push({ label: props.rootLabel, url: props.rootUrl })

    const parts = props.path?.split('/').filter(x => !!x)
    let url = ''
    if(parts)
    for (var part of parts) {
        url += '/' + part
        r.push({ label: part, url: url })
    }
    return r
})

function handleClick(item, event) {
    console.log('breadcrumb.handleClick', { item, event })
    if (props.interceptClick) {
        console.log('emit')
        // cancelar evento
        event.preventDefault()
        event.stopPropagation()
        event.stopImmediatePropagation()
        emit('folder', item)
        return false;
    }
}
</script>


<template>
    <ol v-if="items.length" class="list-reset flex gap-1">
        <template v-for="item, index, of items" :key="index">
            <li class="flex items-center space-x-1">
                <span v-if="!rootLabel || index > 0" class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
                <Link v-if="links && index < items.length - 1" :href="item.url" @click.native.capture="handleClick(item, $event)"
                    class="whitespace-nowrap max-w-[50vw] truncate hover:underline py-2" :title="item.label"
                    :class="!links ? 'pointer-events-none' : ''">{{ item.label }}</Link>
                <span v-else class="opacity-80 py-2">{{ item.label }}</span>
            </li>
        </template>
    </ol>
    <ol v-else>
        <li class="py-1">
            <span class="mx-2 text-neutral-500 dark:text-neutral-400">/</span>
        </li>
    </ol>
</template>
