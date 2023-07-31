<template>
    <component :is="link?Link:tag" class="conditional-link" :data-link="link?1:0" :href="href">
        <slot />
    </component>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    link: { type: Boolean, required: true },
    href: {type: String },
    tag: {type: String, default: 'a'}
})
</script>
