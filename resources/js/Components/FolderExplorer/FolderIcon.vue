<template>
    <ConditionalLink :href="href" class="flex gap-3 items-baseline" :is-link="link">
    <Icon :icon="owner?'ph:folder-user-duotone':private ? 'ph:folder-lock-duotone' : 'ph:folder-simple-duotone'" class="text-yellow-500 transform scale-125" />
    {{ name ? url.substring(url.lastIndexOf('/') + 1) : '' }}</ConditionalLink>
</template>

<script setup>
const props = defineProps({
    url: String,
    private: Boolean,
    owner: Boolean,
    name: {
        type: Boolean,
        default: false
    },
    link: {type: Boolean, default: true}
})

const href = computed(() => props.url && !props.url.startsWith('/') ? '/' + props.url : props.url)
</script>
