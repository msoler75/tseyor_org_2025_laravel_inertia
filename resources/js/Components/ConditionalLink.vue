<template>
    <component :is="isLink?Link:tag" class="conditional-link" :data-link="isLink ? 1 : 0" :href="href"
        :preserve-page="preservePage" :auto-scroll="autoScroll">
        <slot />
    </component>
</template>

<script setup>
import Link from '@/Components/Link.vue'

defineProps({
    isLink: { type: Boolean, required: true },
    href: { type: String },
    tag: { type: String, default: 'a' },
    preservePage: {
        type: Boolean,
        default: false
    },
    autoScroll: {
        type: Boolean,
        default: true
    }
})
</script>
