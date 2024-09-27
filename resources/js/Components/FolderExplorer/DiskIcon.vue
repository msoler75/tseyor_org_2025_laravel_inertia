<template>
    <ConditionalLink :href="href" class="flex gap-3 items-baseline" :is-link="isLink">
    <Icon icon="ph:hard-drive-duotone" class="text-slate-500 transform scale-150" />
    {{ name ? url.substring(url.lastIndexOf('/') + 1) : '' }}</ConditionalLink>
</template>

<script setup>
const props = defineProps({
    url: String,
    name: {
        type: Boolean,
        default: false
    },
    isLink: {type: Boolean, default: true}
})

const href = computed(() => props.url && !props.url.startsWith('/') ? '/' + props.url : props.url)
</script>
