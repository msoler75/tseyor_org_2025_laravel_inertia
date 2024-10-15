<template>
    <ConditionalLink :href="href" class="flex gap-3 items-baseline relative" :is-link="isLink">
        <span v-show="loading" class="loading loading-spinner loading-sm"></span>
        <Icon v-show="!loading"
            :icon="arrow ? 'charm:folder-symlink' : owner ? 'ph:folder-user-duotone' : private ? 'ph:folder-lock-duotone' : 'ph:folder-duotone'"
            class="text-yellow-500 transform scale-125" ></Icon>
        {{ name ? url.substring(url.lastIndexOf('/') + 1) : '' }}
    </ConditionalLink>
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
    isLink: { type: Boolean, default: true },
    arrow: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
})

const href = computed(() => props.url && !props.url.startsWith('/') ? '/' + props.url : props.url)
</script>
