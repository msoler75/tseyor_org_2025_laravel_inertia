<template>
    <component :is="isLink?Link:tag" class="conditional-link" :data-link="isLink?1:0" :href="href">
        <slot />
    </component>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    isLink: { type: Boolean, required: true },
    href: {type: String },
    tag: {type: String, default: 'a'}
})
</script>
